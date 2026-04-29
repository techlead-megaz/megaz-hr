<?php

namespace App\Repositories\Salary;

use App\Models\Staff;
use App\Models\Salary;
use App\Models\CheckIn;
use App\Models\PaySlip;
use App\Models\Overtime;
use App\Models\Allowance;
use App\Enums\StaffStatus;
use App\Http\Action\SendNotification\FcmSendNotification;
use App\Models\DayInOffDay;
use App\Models\OvertimeFee;
use App\Models\SalaryBatch;
use App\Models\SalarySetup;
use Illuminate\Support\Carbon;
use App\Models\SalaryAllowance;
use App\Models\OffDayAssignment;
use App\Models\OvertimeCategory;
use App\Models\PaySlipAllowance;
use App\Models\SalaryBatchStaff;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Mobile\PaySlipResource;

class SalaryRepository implements SalaryRepositoryInterface
{
  use FcmSendNotification;
  public function getAllowances($request)
  {
    $query =  Allowance::with('role.department')->orderBy('id', 'desc');
    if ($request->has('role_id')) {
      $query->where('role_id', $request->role_id);
    }
    $allowances = $query->paginate(config('common.list_count'));
    return ResponseData($allowances);
  }
  public function createAllowance($data)
  {
    DB::beginTransaction();
    try {
      $existingAllowance = Allowance::where('name', $data['name'])
        ->where('role_id', $data['role_id'])
        ->where('type', $data['type'])
        ->first();
      if ($existingAllowance) {
        DB::rollback();
        ResponseMessage('Allowance already exists.', 409);
      }
      $allowance = Allowance::create([
        'name' => $data['name'],
        'amount' => $data['amount'],
        'type' => $data['type'],
        'role_id' => $data['role_id'],
      ]);
      DB::commit();
      ResponseData($allowance);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function storeSalarySetUp($data)
  {
    DB::beginTransaction();
    try {
      $exists = SalarySetup::where('role_id', $data['role_id'])->exists();

      if ($exists) {
        \ResponseMessage('Salary setup already exists for this role.', 422);
      }
      $salarySetup = SalarySetup::create([
        'basic_salary' => $data['basic_salary'],
        'role_id' => $data['role_id'],
      ]);

      if (isset($data['salary_allowances'])) {
        $salary_allowances = json_decode($data['salary_allowances'], true);
        foreach ($salary_allowances as $salary_allowance) {
          SalaryAllowance::create([
            'salary_setup_id' => $salarySetup->id,
            'allowance_id' => $salary_allowance['allowance_id'],
            'amount' => $salary_allowance['amount'],
          ]);
        }
      }

      $staffIds = Staff::whereHas('roles', function ($query) use ($data) {
        $query->where('id', $data['role_id'])
          ->whereIn('status', [
            StaffStatus::PROBATION->value,
            StaffStatus::PERMANENT->value,
          ]);
      })->pluck('id');
      if ($staffIds->isNotEmpty()) {
        foreach ($staffIds as $staffId) {
          Salary::updateOrCreate(
            [
              'staff_id' => $staffId,
              'salary_setup_id' => $salarySetup->id,
            ],
            [
              'basic_salary' => $data['basic_salary'],
              'staff_id' => $staffId,
              'salary_setup_id' => $salarySetup->id,
              'created_by' => UserData()->id,
            ]
          );
        }
      }
      DB::commit();
      ResponseData($salarySetup);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getSalarySetUp($request)
  {
    $data = SalarySetup::with(['salaryAllowances.allowance', 'role.department'])
      ->when($request->role_id, function ($query) use ($request) {
        return $query->where('role_id', $request->role_id);
      })
      ->when($request->department_id, function ($query) use ($request) {
        return $query->whereHas('role', function ($query) use ($request) {
          $query->where('department_id', $request->department_id);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));

    $data->getCollection()->transform(function ($item) {
      $totalAllowance = 0;
      $totalDeduction = 0;
      foreach ($item->salaryAllowances as $salaryAllowance) {
        if ($salaryAllowance->allowance->type == 'allowance') {
          $totalAllowance += (float) $salaryAllowance->amount;
        } elseif ($salaryAllowance->allowance->type == 'deduction') {
          $totalDeduction += (float) $salaryAllowance->amount;
        }
      }
      $item->total_allowances_amount = $totalAllowance;
      $item->total_deductions_amount = $totalDeduction;
      $item->net_salary = (float) $item->basic_salary + $totalAllowance - $totalDeduction;
      return $item;
    });
    ResponseData($data);
  }

  public function getSalarySetUpById($id)
  {
    $data = SalarySetup::with(['salaryAllowances.allowance', 'role.department'])
      ->where('id', $id)
      ->first();
    if (!$data) {
      ResponseMessage('Salary setup not found.', 404);
    }
    // $totalAllowance = (float) $data->salaryAllowances->sum('amount');
    // $data->total_allowances_amount = $totalAllowance;
    // $data->net_salary = (float) $data->basic_salary + $totalAllowance;
    ResponseData($data);
  }

  public function updateSalarySetUp($data, $id)
  {
    DB::beginTransaction();
    try {
      $salarySetup = SalarySetup::find($id);
      if (!$salarySetup) {
        ResponseMessage('Salary setup not found.', 404);
      }

      if (isset($data['basic_salary']) && $salarySetup->basic_salary !== $data['basic_salary']) {
        $salarySetup->basic_salary = $data['basic_salary'];
      }

      if (isset($data['role_id']) && $salarySetup->role_id !== $data['role_id']) {
        $salarySetup->role_id = $data['role_id'];
      }
      $salarySetup->save();

      if (isset($data['salary_allowances'])) {
        $salary_allowances = json_decode($data['salary_allowances'], true);
        foreach ($salary_allowances as $salary_allowance) {
          SalaryAllowance::updateOrCreate(
            [
              'salary_setup_id' => $salarySetup->id,
              'allowance_id' => $salary_allowance['allowance_id'],
            ],
            [
              'amount' => $salary_allowance['amount'],
            ]
          );
        }
      }

      DB::commit();
      ResponseData($salarySetup);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function deleteSalaryAllowance($salaryAllowanceId)
  {
    DB::beginTransaction();
    try {
      $salaryAllowance = SalaryAllowance::find($salaryAllowanceId);
      if (!$salaryAllowance) {
        ResponseMessage('Salary allowance not found.', 404);
      }
      $salaryAllowance->delete();
      DB::commit();
      ResponseMessage('Salary allowance deleted successfully.', 200);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getSalaries($request)
  {
    $data = Salary::with(['staff.department', 'salarySetup.role.department', 'salarySetup.salaryAllowances.allowance'])
      ->when($request->search_input, function ($q) use ($request) {
        $q->where('basic_salary', 'LIKE', '%' . $request->search_input . '%');
        $q->orWhereHas('staff', function ($q) use ($request) {
          $q->where('name', 'LIKE', '%' . $request->search_input . '%');
        });
        $q->orWhereHas('salarySetup.role', function ($q) use ($request) {
          $q->where('name', 'LIKE', '%' . $request->search_input . '%');
        });
      })
      ->when($request->role_id, function ($query) use ($request) {
        return $query->whereHas('salarySetup', function ($query) use ($request) {
          $query->where('role_id', $request->role_id);
        });
      })
      ->when($request->department_id, function ($query) use ($request) {
        return $query->whereHas('salarySetup.role.department', function ($query) use ($request) {
          $query->where('id', $request->department_id);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));

    $data->getCollection()->transform(function ($item) {
      $totalAllowance = 0;
      $totalDeduction = 0;
      foreach ($item->salarySetup->salaryAllowances as $salaryAllowance) {
        if ($salaryAllowance->allowance->type == 'allowance') {
          $totalAllowance += (float) $salaryAllowance->amount;
        } elseif ($salaryAllowance->allowance->type == 'deduction') {
          $totalDeduction += (float) $salaryAllowance->amount;
        }
      }
      $item->total_allowances_amount = $totalAllowance;
      $item->total_deductions_amount = $totalDeduction;
      $item->net_salary = (float) $item->basic_salary + $totalAllowance - $totalDeduction;
      return $item;
    });
    ResponseData($data);
  }
  public function getSalarySetupByRoleId($roleId)
  {
    $salarySetup = SalarySetup::with('salaryAllowances.allowance')->where('role_id', $roleId)->first();
    if (!$salarySetup) {
      ResponseMessage('Salary setup not found', 404);
    }
    return $salarySetup;
  }

  public function createSalary($data)
  {
    DB::beginTransaction();
    try {
      $salarySetup = SalarySetup::findOrFail($data['salary_setup_id']);
      $existing = Salary::where('staff_id', $data['staff_id'])
        ->where('salary_setup_id', $data['salary_setup_id'])
        ->first();
      if ($existing) {
        ResponseMessage('Salary already exists for this staff with this setup', 409);
      }

      $salary = Salary::create([
        'basic_salary'    => $data['basic_salary'] ?? $salarySetup->basic_salary,
        'staff_id'        => $data['staff_id'],
        'salary_setup_id' => $data['salary_setup_id'],
        'created_by'      => UserData()->id,
      ]);
      DB::commit();
      ResponseData($salary, 201);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function updateBasicSalary($request, $id)
  {
    DB::beginTransaction();
    try {
      $salary = Salary::findOrFail($id);
      $salary->basic_salary = $request['basic_salary'];
      $salary->save();
      DB::commit();
      ResponseData($salary);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }


  public function getOvertimeFee($request)
  {
    $data = OvertimeFee::with('role.department')
      ->when($request->search_input, function ($q) use ($request) {
        $q->where('fee', 'LIKE', '%' . $request->search_input . '%');
        $q->orWhereHas('role', function ($q) use ($request) {
          $q->where('name', 'LIKE', '%' . $request->search_input . '%');
        });
        $q->orWhereHas('role.department', function ($q) use ($request) {
          $q->where('name', 'LIKE', '%' . $request->search_input . '%');
        });
      })
      ->when($request->role_id, function ($query) use ($request) {
        return $query->where('role_id', $request->role_id);
      })
      ->when($request->department_id, function ($query) use ($request) {
        return $query->whereHas('role.department', function ($q) use ($request) {
          $q->where('id', $request->department_id);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));

    ResponseData($data);
  }

  public function createOvertimeFee($data)
  {
    DB::beginTransaction();
    try {
      $exits = OvertimeFee::where('role_id', $data['role_id'])
        ->first();
      if ($exits) {
        DB::rollback();
        ResponseMessage('Overtime fee already exists for this role.', 409);
      }
      $overtimeFee = OvertimeFee::updateOrCreate(
        [
          'id' => $data['id'] ?? null,
        ],
        [
          'role_id' => $data['role_id'],
          'fee' => $data['fee'],
        ]
      );
      DB::commit();

      ResponseData($overtimeFee);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function deleteOvertimeFee($id)
  {
    DB::beginTransaction();
    try {
      $overtimeFee = OvertimeFee::findOrFail($id);
      $overtimeFee->delete();
      DB::commit();
      ResponseMessage('Overtime fee deleted successfully.', 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      DB::rollback();
      ResponseMessage('Overtime fee not found.', 404);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function createOvertimeCategories($data)
  {
    DB::beginTransaction();
    try {
      $overtimeCategory = OvertimeCategory::updateOrCreate(
        ['id' => $data['id'] ?? null],
        [
          'name' => $data['name']
        ]
      );
      DB::commit();
      ResponseData($overtimeCategory);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getOvertimeCategories($request)
  {
    $data = OvertimeCategory::orderBy('id', 'desc')
      ->paginate(config('common.list_count'));
    ResponseData($data);
  }

  public function createOvertime($data)
  {
    DB::beginTransaction();
    try {
      $overtime = Overtime::updateOrCreate(
        ['id' => $data['id'] ?? null],
        [
          'from_date' => $data['from_date'],
          'to_date' => $data['to_date'],
          'staff_id' => $data['staff_id'],
          'overtime_category_id' => $data['overtime_category_id'],
          'time_shift_id' => $data['time_shift_id'],
          'from_time' => $data['from_time'],
          'to_time' => $data['to_time'],
          'remark' => $data['remark'] ?? null,
          'status' => $data['status'],
          'created_by' => UserData()->id,
          'confirmed_at' => $data['confirmed_at'] ?? null,
          'confirmed_by' => $data['confirmed_by'] ?? null,
          'cancelled_at' => $data['cancelled_at'] ?? null,
          'cancelled_by' => $data['cancelled_by'] ?? null,
        ]
      );
      DB::commit();
      $staff = Staff::find($data['staff_id']);
      $notificationData = [
        'title' => 'Overtime ',
        'preview' => "{$staff->name} has requested overtime",
      ];
      $allStaff = Staff::whereIn('status', [
        StaffStatus::PROBATION->value,
        StaffStatus::PERMANENT->value,
      ])
        ->whereHas('features', function ($query) {
          $query->where('module', 'over-time');
        })
        ->where('id', '!=', $overtime->staff_id)->get();
      $this->sendFcmNotification($overtime, $allStaff, $notificationData);
      ResponseData($overtime);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getOvertimes($request)
  {
    $data = Overtime::with(['staff.department', 'overtimeCategory', 'timeShift.shift'])
      ->when($request->role_id, function ($query) use ($request) {
        return $query->whereHas('staff.roles', function ($query) use ($request) {
          $query->where('id', $request->role_id);
        });
      })
      ->when($request->department_id, function ($query) use ($request) {
        return $query->whereHas('staff.department', function ($query) use ($request) {
          $query->where('id', $request->department_id);
        });
      })
      ->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));

    ResponseData($data);
  }

  public function setOvertimeApproval($data, $id)
  {
    DB::beginTransaction();
    try {
      $overtime = Overtime::find($id);
      if (!$overtime) {
        ResponseMessage('Overtime not found.', 404);
      }
      if ($overtime->status === 'confirmed') {
        ResponseMessage('Overtime already confirmed.', 409);
      }

      $status = $data['status'] ?? null;
      if (!$status) {
        ResponseMessage('Status is required.', 400);
      }
      if ($status === 'cancelled') {
        $overtime->status = 'cancelled';
        $overtime->cancelled_at = now();
        $overtime->cancelled_by = UserData()->id;
      }

      if ($status === 'confirmed') {
        $overtime->status = 'confirmed';
        $overtime->confirmed_at = now();
        $overtime->confirmed_by = UserData()->id;
        $staff = Staff::find($overtime->staff_id);
        $notificationData = [
          'title' => 'Overtime ',
          'preview' => "Overtime has been confirmed",
        ];
        // $allStaff = Staff::whereIn('status', [
        //   StaffStatus::PROBATION->value,
        //   StaffStatus::PERMANENT->value,
        // ])->where('id', '!=', $overtime->staff_id)->get();
        $this->sendFcmNotification($overtime, $staff, $notificationData);
      }

      $overtime->save();
      DB::commit();
      ResponseData($overtime);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getMobileOvertimesByStaffId($request, $staffId)
  {
    $data = Overtime::with(['overtimeCategory', 'timeShift.shift'])
      ->where('staff_id', $staffId)->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));
    ResponseData($data);
  }

  public function createSalaryBatch($data)
  {
    DB::beginTransaction();
    try {
      $salaryBatch = SalaryBatch::create([
        'name' => $data['name'],
        'day_of_monthly' => $data['day_of_monthly'],
        'created_by' => UserData()->id
      ]);

      if (isset($data['staff_ids'])) {
        $staff_ids = json_decode($data['staff_ids'], true);

        foreach ($staff_ids as $staffId) {
          $existingBatch = SalaryBatchStaff::where('staff_id', $staffId)->first();
          if ($existingBatch) {
            DB::rollback();
            ResponseMessage('Staff already exists in the batch.', 409);
          }
          SalaryBatchStaff::create([
            'salary_batch_id' => $salaryBatch->id,
            'staff_id' => $staffId,
          ]);
        }
      }

      DB::commit();
      ResponseData($salaryBatch);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getSalaryBatch($request)
  {
    // $data = SalaryBatch::with(['salaryBatchStaff.staff'])->withCount('salaryBatchStaff')
    //   ->orderBy('id', 'desc')
    //   ->paginate(config('common.list_count'));

    // ResponseData($data);

    $data = SalaryBatch::with(['salaryBatchStaff' => function ($query) use ($request) {
      $query->whereHas('staff', function ($q) use ($request) {
        if ($request->role_id) {
          $q->whereHas('roles', function ($roleQuery) use ($request) {
            $roleQuery->where('id', $request->role_id);
          });
        }
        if ($request->department_id) {
          $q->where('department_id', $request->department_id);
        }
      })
        ->with(['staff.roles', 'staff.department']);
    }])
      ->whereHas('salaryBatchStaff.staff', function ($q) use ($request) {
        if ($request->role_id) {
          $q->whereHas('roles', function ($roleQuery) use ($request) {
            $roleQuery->where('id', $request->role_id);
          });
        }
        if ($request->department_id) {
          $q->where('department_id', $request->department_id);
        }
      })->when($request->search_input, function ($q) use ($request) {
        $q->where('name', 'LIKE', '%' . $request->search_input . '%');
      })
      ->withCount(['salaryBatchStaff' => function ($query) use ($request) {
        $query->whereHas('staff', function ($q) use ($request) {
          if ($request->role_id) {
            $q->whereHas('roles', function ($roleQuery) use ($request) {
              $roleQuery->where('id', $request->role_id);
            });
          }
          if ($request->department_id) {
            $q->where('department_id', $request->department_id);
          }
        });
      }])
      ->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));

    return ResponseData($data);
  }

  public function getSalaryBatchById($id)
  {
    $data = SalaryBatch::with(['salaryBatchStaff.staff.department', 'salaryBatchStaff.staff.roles'])
      ->where('id', $id)
      ->get();
    if ($data->isEmpty()) {
      ResponseMessage('Salary batch not found.', 404);
    }
    ResponseData($data);
  }

  public function updateSalaryBatch($data, $id)
  {
    DB::beginTransaction();
    try {
      $salaryBatch = SalaryBatch::findOrFail($id);
      if (!$salaryBatch) {
        ResponseMessage('Salary batch not found.', 404);
      }
      $salaryBatch->name = $data['name'];
      $salaryBatch->day_of_monthly = $data['day_of_monthly'];
      $salaryBatch->save();

      if (isset($data['salary_batch_staffs'])) {
        $salary_batch_staffs = json_decode($data['salary_batch_staffs'], true);

        foreach ($salary_batch_staffs as $salary_batch_staff) {
          $existingBatch = SalaryBatchStaff::where('staff_id', $salary_batch_staff['staff_id'])->first();
          // if ($existingBatch) {
          //   DB::rollback();
          //   ResponseMessage('Staff already exists in the batch.', 409);
          // }
          SalaryBatchStaff::updateOrCreate(
            [
              'id' => $salary_batch_staff['id'] ?? null,
              'salary_batch_id' => $salaryBatch->id,
            ],
            [
              'staff_id' => $salary_batch_staff['staff_id'],
            ]
          );
        }
      }

      DB::commit();
      ResponseData($salaryBatch);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function deleteSalaryBatch($id)
  {
    DB::beginTransaction();
    try {
      $salaryBatch = SalaryBatch::find($id);
      if (!$salaryBatch) {
        ResponseMessage('Salary batch not found.', 404);
      }
      $salaryBatch->salaryBatchStaff()->delete();
      $salaryBatch->delete();
      DB::commit();
      ResponseMessage('Salary batch deleted successfully.', 200);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function deleteSalaryBatchStaff($id)
  {
    DB::beginTransaction();
    try {
      $salaryBatchStaff = SalaryBatchStaff::find($id);
      if (!$salaryBatchStaff) {
        ResponseMessage('Salary batch staff not found.', 404);
      }
      $salaryBatchStaff->delete();
      DB::commit();
      ResponseMessage('Salary batch staff deleted successfully.', 200);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }


  public function calculateSalary($request)
  {

    $startDate = Carbon::parse($request->from_date);
    $endDate = Carbon::parse($request->to_date);
    $totalDays = $startDate->diffInDays($endDate) + 1;
    $salaryBatchStaffs = SalaryBatchStaff::where('salary_batch_id', $request->salary_batch_id)
      ->with([
        'salaryBatch',
        'staff.overtimes',
        'staff.salary',
        'staff.leaves',
        'staff.salary.salarySetup',
        'staff.salary.salarySetup.salaryAllowances',
        // 'staff.leaves' => function ($query) use ($startDate, $endDate) {
        //   $query->where('status', 'confirmed')->where('is_unpaid_leave', 1)
        //     ->where(function ($q) use ($startDate, $endDate) {
        //       $q->whereBetween('start_date', [$startDate, $endDate])
        //         ->orWhereBetween('end_date', [$startDate, $endDate])
        //         ->orWhere(function ($q) use ($startDate, $endDate) {
        //           $q->where('start_date', '<=', $startDate)
        //             ->where('end_date', '>=', $endDate);
        //         });
        //     });
        // }
      ])
      ->paginate(config('common.list_count'));

    $publicHolidays = DayInOffDay::whereBetween('date', [$startDate, $endDate])->where('day', 'Public-holiday')->count();
    if ($salaryBatchStaffs->isEmpty()) {
      ResponseMessage('No staff found in this batch.', 404);
    }

    $salaryDetails = [];
    foreach ($salaryBatchStaffs as $salaryBatchStaff) {
      $staff = $salaryBatchStaff->staff; //stafflist based on batch
      $salary = $staff->salary;   //basic salary
      $checkIns = CheckIn::where('staff_id', $staff->id)
        ->whereBetween('check_in_date_time', [$startDate, $endDate])
        ->get();
      $actualWorkedDay = $checkIns
        ->pluck('check_in_date_time')
        ->map(fn($date) => \Carbon\Carbon::parse($date)->toDateString())
        ->unique()
        ->count();
      if ($salary && $checkIns->isNotEmpty()) {
        $totalAllowance = 0;
        $totalDeduction = 0;
        foreach ($salary->salarySetup->salaryAllowances as $salaryAllowance) {
          if ($salaryAllowance->allowance->type == 'allowance') {
            $totalAllowance += (float) $salaryAllowance->amount;
          } elseif ($salaryAllowance->allowance->type == 'deduction') {
            $totalDeduction += (float) $salaryAllowance->amount;
          }
        }
        $offDayAssignments = OffDayAssignment::where(function ($query) use ($staff) {
          $query->where('offdayable_id', $staff->id)
            ->where('offdayable_type', 'staff');
        })
          ->orWhere(function ($query) use ($staff) {
            $query->where('offdayable_id', $staff->department_id)
              ->where('offdayable_type', 'department');
          })
          ->with('offDay.days')
          ->get();
        $offDayCount = 0;

        foreach ($offDayAssignments as $assignment) {
          $offDay = $assignment->offDay;

          foreach ($offDay->days as $day) {
            // if ($day->date) {
            //   // For specific dated off days (like public holidays)
            //   $offDayDate = Carbon::parse($day->date);
            //   if ($offDayDate->between($startDate, $endDate)) {
            //     $offDayCount++;
            //   }
            // } else {
            // For weekly/bi-weekly off days
            $dayName = $day->day;
            $currentDate = $startDate->copy();
            $weekCounter = 0;

            while ($currentDate <= $endDate) {
              if ($currentDate->englishDayOfWeek === $dayName) {
                // For bi-weekly off days, only count every other week
                if ($offDay->repetition === 'Bi-weekly') {
                  if ($weekCounter % 2 === 0) {
                    $offDayCount++;
                  }
                  $weekCounter++;
                }
                // For weekly off days, count every week
                else if ($offDay->repetition === 'Weekly') {
                  $offDayCount++;
                }
                // For monthly off days, count once per month
                else if ($offDay->repetition === 'Monthly') {
                  if ($currentDate->day <= 7) { // Count only if in first week of month
                    $offDayCount++;
                  }
                }
              }
              $currentDate->addDay();
            }
            // }
          }
        }

        $result = $staff->leaves()
          ->where('status', 'confirmed')
          ->selectRaw("
        SUM(CASE WHEN is_unpaid_leave = 1 THEN day ELSE 0 END) as unpaid_leave_count,
        SUM(CASE WHEN is_unpaid_leave = 0 OR is_unpaid_leave IS NULL THEN day ELSE 0 END) as paid_leave_count
    ")
          ->first();

        $unpaidLeaveCount = (int)$result->unpaid_leave_count ?? 0;
        $paidLeaveCount   = (int)$result->paid_leave_count ?? 0;
        // foreach ($staff->leaves as $leave) {
          // if ($leave->is_unpaid_leave) {
          //   // Count the number of unpaid leave days in the date range
          //   $leaveStartDate = Carbon::parse($leave->start_date);
          //   $leaveEndDate = Carbon::parse($leave->end_date);

          //   // Ensure the leave period overlaps with the requested date range
          //   if ($leaveStartDate->between($startDate, $endDate) || $leaveEndDate->between($startDate, $endDate) || ($leaveStartDate <= $startDate && $leaveEndDate >= $endDate)) {
          //     $unpaidLeaveCount += $leaveStartDate->diffInDays($leaveEndDate) + 1;
          //   }
          // }
        // }
        $actualWorkDays = max(0, $totalDays - $offDayCount - $unpaidLeaveCount - $publicHolidays);
        $actualBasicSalary = $actualWorkDays > 0 ?  ($salary->basic_salary) / $actualWorkDays : 0;

        $totalWorkedHours = 0;
        $totalOvertimeHours = 0;
        //to get total worked hours from request from date to to date
        foreach ($checkIns as $checkIn) {
          $checkInTime = Carbon::parse($checkIn->check_in_date_time);
          $checkOutTime = Carbon::parse($checkIn->check_out_date_time);
          if ($checkOutTime->gt($checkInTime)) {

            $workedHours =  $checkInTime->diffInHours($checkOutTime);
            $totalWorkedHours += (float) $workedHours;
          }
        }
        // dd($totalWorkedHours);

        $overtimes = Overtime::where('staff_id', $staff->id)
          ->whereBetween('from_date', [$startDate, $endDate])
          ->whereBetween('to_date', [$startDate, $endDate])
          ->get();

        foreach ($overtimes as $overtime) {
          $actualOvertimes = CheckIn::where('staff_id', $overtime->staff_id)
            ->where('time_shift_id', $overtime->time_shift_id)
            ->get();
          foreach ($actualOvertimes as $actualOvertime) {
            $overtimeCheckIn = Carbon::parse($actualOvertime->check_in_date_time);
            $overtimeCheckOut = Carbon::parse($actualOvertime->check_out_date_time);
            if ($overtimeCheckOut->gt($overtimeCheckIn)) {
              // Calculate the overtime worked hours
              $overtimeWorkedHours = $overtimeCheckIn->diffInHours($overtimeCheckOut);
              $totalOvertimeHours += (float)$overtimeWorkedHours;
            }
          }
        }
        $totalOvertimeHours = max(0, $totalOvertimeHours);
        $actualWorkedHours = max(0, $totalWorkedHours - $totalOvertimeHours);
        $hourlyRate = 0;
        if ($actualWorkedHours > 0 && $salary->basic_salary > 0) {
          $hourlyRate = $salary->basic_salary / $actualWorkedHours;
          // $hourlyRate = $salary->basic_salary / $actualWorkedHours > 0 ? $salary->basic_salary / $actualWorkedHours : 0;
        }
        $role = $staff->roles->first();
        $overtimePay = 0;
        if ($role) {
          $overtimeFee = OvertimeFee::where('role_id', $role->id)->first();
          $overtimePay = $overtimeFee ? $overtimeFee->fee * $hourlyRate * $totalOvertimeHours : 0;
        } else {
          $overtimePay = 0;
        }
        // $totalSalary =  max(0, (float) ($salary->basic_salary - $totalDeduction) + ($totalAllowance + $overtimePay));
        // $perDaySalary=(float)$totalSalary / $totalDays;
        // $netSalary=(int)($perDaySalary* $actualWorkedDay);
        $salaryPerDay = $salary->basic_salary / $totalDays;

        // Income
        $basicPay = $salaryPerDay * ($actualWorkedDay + $paidLeaveCount);

        $grossIncome = $basicPay + $totalAllowance + $overtimePay;

        // Deductions
        $unpaidLeaveDeduction = $salaryPerDay * $unpaidLeaveCount;

        $totalDeductions = $unpaidLeaveDeduction + $totalDeduction;

        // Final salary
        $netSalary = $grossIncome - $totalDeductions;
        $salaryDetails[] = [
          'staff_id' => $staff->id,
          'staff_name' => $staff->name,
          'department_id' => $staff->department->id,
          'department_name' => $staff->department->name,
          'role_id' => $staff->roles->first() ? $staff->roles->first()->id : null,
          'role_name' => $staff->roles->first() ? $staff->roles->first()->name : null,
          'salary_batch_id' => $request->salary_batch_id,
          'salary_batch_name' => $salaryBatchStaff->salaryBatch->name,
          'salary_id' => $salary->id,
          'formal_basic_salary' =>  $salary->basic_salary,
          'allowance' =>  round($totalAllowance, 2),
          'deductions' => $totalDeduction,
          'overtime_hours' => round(max(0, $totalOvertimeHours), 2) ?? null,
          'overtime_pay' => max(0, $overtimePay) ?? null,
          'off_day_count' => $offDayCount,
          'basic_salary' => round($actualBasicSalary, 2),
          'netSalary' => round($netSalary, 2),
          'unpaid_leave_count' => $unpaidLeaveCount,
          'actual_work_days' => $actualWorkDays,
          // 'actual_worked_hours' => $actualWorkedHours,
          'public_holidays' => $publicHolidays,
          'total_days' => $totalDays,
          '$offDayCount' => $offDayCount,
          // 'total_worked_hours' => $totalWorkedHours,
          // 'total_overtime_hours' => $totalOvertimeHours,
          // 'total_allowances_amount' => $totalAllowance,
          // 'total_deductions_amount' => $totalDeduction,
        ];
      }
    }

    return [
      'data' => $salaryDetails,
      'pagination' => [
        'total' => $salaryBatchStaffs->total(),
        'per_page' => $salaryBatchStaffs->perPage(),
        'current_page' => $salaryBatchStaffs->currentPage(),
        'last_page' => $salaryBatchStaffs->lastPage(),
        'from' => $salaryBatchStaffs->firstItem(),
        'to' => $salaryBatchStaffs->lastItem(),
        'first_page_url' => $salaryBatchStaffs->url(1),
        'last_page_url' => $salaryBatchStaffs->url($salaryBatchStaffs->lastPage()),
        'next_page_url' => $salaryBatchStaffs->nextPageUrl(),
        'prev_page_url' => $salaryBatchStaffs->previousPageUrl(),
        'path' => $salaryBatchStaffs->path(),
        'links' => $salaryBatchStaffs->links(),
      ]
    ];
  }

  public function getAllowanceTypes($request)
  {
    $data = Allowance::where('type', $request->type)->where('role_id', $request->role_id)->get();
    if ($data->isEmpty()) {
      ResponseMessage('Allowance types not found.', 404);
    }
    ResponseData($data);
  }

  public function createPaySlip($data)
  {
    DB::beginTransaction();
    try {
      if (isset($data['pay_slips'])) {
        $pay_slips = json_decode($data['pay_slips'], true);
        $paySlipIds = [];
        foreach ($pay_slips as $pay_slip) {
          $paySlip = PaySlip::create([
            'staff_id' => $pay_slip['staff_id'],
            'salary_batch_id' => $pay_slip['salary_batch_id'],
            'salary_id' => $pay_slip['salary_id'],
            'basic_salary' => $pay_slip['basic_salary'],
            'allowance' => $pay_slip['allowance'],
            'deduction' => $pay_slip['deduction'] ?? 0,
            'added_allowance' => $pay_slip['added_allowance_amount'] ?? 0,
            'added_deduction' => $pay_slip['added_deduction_amount'] ?? 0,
            'total_allowance' => $pay_slip['total_allowance'],
            'overtime' => $pay_slip['overtime'],
            'net_salary' => $pay_slip['net_salary'],
            'created_by' => UserData()->id,
          ]);

          $paySlipIds[] = $paySlip->id;
          if (isset($pay_slip['added_allowances'])) {
            foreach ($pay_slip['added_allowances'] as $addedAllowance) {
              PaySlipAllowance::create([
                'pay_slip_id' => $paySlip->id,
                'allowance_id' => $addedAllowance['id'],
              ]);
            }
          }

          if (isset($pay_slip['added_deductions'])) {
            foreach ($pay_slip['added_deductions'] as $addedDeduction) {
              PaySlipAllowance::create([
                'pay_slip_id' => $paySlip->id,
                'allowance_id' => $addedDeduction['id'],
              ]);
            }
          }
        }

        DB::commit();
        ResponseData($paySlipIds);
      }
    } catch (\Exception $e) {

      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function getPaySlips($request)
  {
    return PaySlip::with(['staff.department', 'staff.roles'])
      ->when($request->role_id || $request->department_id, function ($query) use ($request) {
        $query->whereHas('staff', function ($q) use ($request) {
          if ($request->role_id) {
            $q->whereHas('roles', function ($roleQuery) use ($request) {
              $roleQuery->where('id', $request->role_id);
            });
          }
          if ($request->department_id) {
            $q->where('department_id', $request->department_id);
          }
        });
      })

      ->orderBy('id', 'desc')
      ->paginate(config('common.list_count'));
  }

  public function deletePaySlip($id)
  {
    DB::beginTransaction();
    try {
      $paySlip = PaySlip::find($id);
      if (!$paySlip) {
        ResponseMessage('Pay slip not found.', 404);
      }
      $paySlip->paySlipAllowances->each(function ($allowance) {
        $allowance->delete();
      });
      $paySlip->delete();
      DB::commit();
      ResponseMessage('Pay slip deleted successfully.', 200);
    } catch (\Exception $e) {
      DB::rollback();
      ResponseMessage($e->getMessage(), 402);
      throw $e;
    }
  }

  public function confirmPaySlip($id)
  {
    $paySlip = PaySlip::find($id);
    if (!$paySlip) {
      \ResponseMessage('PaySlip is Empty', 419);
    }
    if ($paySlip->is_confirm) {
      \ResponseMessage('PaySlip is already confirmed', 419);
    }
    $paySlip->is_confirm = true;
    $paySlip->confirmed_at = now();
    $paySlip->confirmed_by = \UserData()->id ?? null;
    $paySlip->save();
    return $paySlip;
  }

  public function getStaffPaySlip($data)
  {
    $staffId = \UserData()->id;
    $salary = Salary::where('staff_id', $staffId)->latest()->first();
    $salarySetupId = $salary->salary_setup_id ?? null;
    $salaryAllowance = SalaryAllowance::where('salary_setup_id', $salarySetupId)
      ->join('allowances', 'salary_allowances.allowance_id', 'allowances.id')
      ->where('allowances.type', 'allowance')
      ->select(DB::raw('SUM(salary_allowances.amount) as total_allowances'))
      ->first();
    $paySlips = PaySlip::orderBy('id', 'desc')
      ->where('is_confirm', true)
      ->where('staff_id', $staffId)
      ->get();
    $paySlipResource = PaySlipResource::collection($paySlips);
    return [
      'salary' => [
        'basic_salary' => $salary->basic_salary ?? 0,
        'allowance_amount' =>  $salaryAllowance->total_allowances ?? 0,
      ],
      'pay_slips' => $paySlipResource,
    ];
  }
}
