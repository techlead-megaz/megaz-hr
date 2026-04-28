<?php

namespace App\Repositories\Staff;

use App\Models\Role;
use App\Models\Staff;
use App\Models\Feature;
use App\Models\Inventory;
use App\Enums\StaffStatus;
use App\Models\NrcTownship;
use App\Models\StaffAdvance;
use App\Models\StaffBalance;
use Illuminate\Http\Request;
use App\Models\StaffContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StaffResource;
use App\Models\StaffEmergencyContact;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Exception\Auth\UserDisabled;

class StaffRepository implements StaffRepositoryInterface
{
    public function listAllData(Request $request)
    {
        $departmentIds = $request->department_id;
        $roleIds = $request->role_id;

        $staffQuery = Staff::orderByDesc('id')
            ->with(['department', 'roles',  'bank', 'staffCertifications'])
            ->whereIn('status', [
                StaffStatus::PROBATION->value,
                StaffStatus::PERMANENT->value,
            ])
            ->when($request->search_input, function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search_input . '%');
            })
            ->when($departmentIds, function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            })
            ->when($roleIds, function ($query) use ($roleIds) {
                $query->whereHas('roles', function ($q) use ($roleIds) {
                    $q->whereIn('id', $roleIds);
                });
            })
            ->when(!isset($request->page), function ($q) {
                $q->where('is_active', 1);
            });
        $staff = isset($request->page) ? $staffQuery->paginate(config('common.list_count')) : $staffQuery->get();
        return $staff;
    }

    //for mobile app
    public function staffList($request)
    {
        $departmentId = $request->department_id;
        $data = Staff::with(['department', 'roles'])
            ->whereIn('status', [
                StaffStatus::PROBATION->value,
                StaffStatus::PERMANENT->value,
            ])
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->orderByDesc('id')->get();
        return StaffResource::collection($data);
    }

    public function staffBalanceList(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $staffBalances = StaffBalance::with('staff')->where('month', $month)->where('year', $year)->orderBy('created_at', 'desc')->paginate(config('common.list_count'));

        foreach ($staffBalances as $staffBalance) {
            $totalAddition = StaffAdvance::where('staff_id', $staffBalance->staff_id)
                ->whereMonth('date_time', $month)
                ->whereYear('date_time', $year)
                ->where('type', 'addition')
                ->sum('amount') ?? 0;

            $totalSettlement = StaffAdvance::where('staff_id', $staffBalance->staff_id)
                ->whereMonth('date_time', $month)
                ->whereYear('date_time', $year)
                ->where('type', 'settlement')
                ->sum('amount') ?? 0;

            $staffBalance->addition = $totalAddition;
            $staffBalance->settlement = $totalSettlement;
            $staffBalance->staff_name = $staffBalance->staff->name;
            unset($staffBalance->staff);
        }

        ResponseData($staffBalances);
    }

    public function staffBalanceDetail(Request $request, int $id)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $staff = Staff::with([
            'staffAdvances' => function ($query) use ($month, $year) {
                $query->whereMonth('date_time', $month)
                    ->whereYear('date_time', $year);
            },
            'staffBalance' => function ($query) use ($month, $year) {
                $query->where('month', $month)
                    ->where('year', $year);
            }
        ])->where('id', $id)->first();

        ResponseData($staff);
    }


    public function createData(array $data)
    {
        DB::beginTransaction();
        try {
            $data['is_active'] = 1;
            $data = RemoveNullValues($data);
            $staff = Staff::create($data);
            if (isset($data['certificate_images']) && is_array($data['certificate_images'])) {
                foreach ($data['certificate_images'] as $certificateImage) {
                    if ($certificateImage instanceof \Illuminate\Http\UploadedFile) {
                        $extension = $certificateImage->getClientOriginalExtension();
                        $hashedName = md5(uniqid() . microtime()) . '.' . $extension;
                        $path = $certificateImage->storeAs("staff_certificates/{$staff->id}", $hashedName, 'public');
                        $url = Storage::url($path);

                        $staff->staffCertifications()->create([
                            'certificate_file_url' => $url,
                            'certificate_file_path' => $path,
                        ]);
                    }
                }
            }
            if (isset($data['role_id'])) {
                $staff->roles()->attach($data['role_id']);
            }

            foreach ($data['feature_ids'] as $featureId) {
                $staff->features()->sync($featureId);
            }

            foreach ($data['inventory_ids'] as $inventoryId) {
                $staff->inventories()->attach($inventoryId);
            }

            foreach ($data['skill_ids'] as $skillId) {
                $staff->skills()->attach($skillId);
            }

            $data['staff_id'] = $staff->id;
            $this->createEmegercyContact($data);
            DB::commit();
            return $staff;
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }

    public function createEmegercyContact($data)
    {
        $staff = StaffEmergencyContact::where('staff_id', $data['staff_id'])->first();
        if (!$staff) {
            $staff = StaffEmergencyContact::create($data);
        }
    }

    public function updateData(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::find($id);
            if ($staff) {
                $data = RemoveNullValues($data);
                $staff->emergencyContacts()->updateOrCreate(['staff_id' => $staff->id], $data);
                if ($staff->department_id != $data['department_id']) {
                    $staff->roles()->detach();
                }
                if (isset($data['nrc_front_path'])) {
                    if ($staff->nrc_front_path) {
                        DeleteFileFromServer($staff->nrc_front_path);
                    }
                }

                if (isset($data['nrc_back_path'])) {
                    if ($staff->nrc_back_path) {
                        DeleteFileFromServer($staff->nrc_back_path);
                    }
                }

                if (isset($data['household_registration_path'])) {
                    if ($staff->household_registration_path) {
                        DeleteFileFromServer($staff->household_registration_path);
                    }
                }

                if (isset($data['profile_image_path']) && $staff->profile_image_path) {
                    DeleteFileFromServer($staff->profile_image_path);
                }

                if (isset($data['certificate_images']) && is_array($data['certificate_images'])) {
                    foreach ($staff->staffCertifications as $oldCertification) {
                        if ($oldCertification->certificate_file_path) {
                            DeleteFileFromServer($oldCertification->certificate_file_path);
                        }
                        $oldCertification->delete();
                    }
                }

                if (isset($data['certificate_images']) && is_array($data['certificate_images'])) {
                    foreach ($data['certificate_images'] as $certificateImage) {
                        if ($certificateImage instanceof \Illuminate\Http\UploadedFile) {
                            $extension = $certificateImage->getClientOriginalExtension();
                            $hashedName = md5(uniqid() . microtime()) . '.' . $extension;
                            $path = $certificateImage->storeAs("staff_images/{$staff->id}", $hashedName, 'public');
                            $url = Storage::url($path);

                            $staff->staffCertifications()->create([
                                'certificate_file_url' => $url,
                                'certificate_file_path' => $path,
                            ]);
                        }
                    }
                }
                $staff->update($data);

                if (isset($data['role_id'])) {
                    $staff->roles()->sync($data['role_id']);
                }

                if (isset($data['feature_ids']) && is_array($data['feature_ids'])) {
                    $staff->features()->sync($data['feature_ids']);
                }

                if (isset($data['inventory_ids']) && is_array($data['inventory_ids'])) {
                    $staff->inventories()->sync($data['inventory_ids']);
                }

                if (isset($data['skill_ids']) && is_array($data['skill_ids'])) {
                    $staff->skills()->sync($data['skill_ids']);
                }
            }
            DB::commit();
            return $staff;
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }

    public function staffDetail(int $id)
    {
        $staff = Staff::with(
            'department',
            'roles',
            'inventories',
            'emergencyContacts',
            'gender',
            // 'completed_tasks',
            'features',
            'skills',
            'bank',
            'staffCertifications'
        )->find($id);
        if ($staff == null) {
            ResponseMessage("Staff not found or invalid id", 404);
        }
        return $staff;
    }

    public function deleteData($id)
    {
        $staff = Staff::find($id);
        if ($staff) {
            $staff->is_active = 0;
            $staff->save();

            return true;
        }

        return false;
    }

    public function getStaffByDepartment(Request $request, int $departmentId, array $roles = null)
    {

        $allowedRoles = $roles ? ((in_array('Manager', $roles) || in_array('Captain', $roles) || in_array('Chief Accountant', $roles) || in_array('Sous Chef', $roles) || in_array('Senior Receptionist', $roles))
            ? ['Supervisor', 'Staff', 'Helper', 'Bartender', 'Demi Chef', 'Waiter', 'Receptionist', 'Accountant', 'Cashier', 'Driver', 'M&E', 'Security', 'Staff Cook']
            : ['Staff', 'Helper', 'Demi Chef', 'Bartender', 'Waiter', 'Receptionist', 'Driver', 'M&E', 'Security', 'Staff Cook', 'Accountant', 'Cashier']) : null;

        if ($request->per_page || $request->page) {
            // $totalCount = Staff::where('department_id', $departmentId)->where('is_active', 1)->count();
            // $pageNumber = 1;
            // $perPage = 20;
            // if ($request->page) {
            //     $pageNumber = $request->page;
            // }
            // if ($request->per_page) {
            //     $perPage = $request->per_page;
            // }
            // $skip = ($pageNumber - 1) * $perPage;
            // $staffs = Staff::with('department')->where('department_id', $departmentId)
            //     ->where('is_active', 1)
            //     ->skip($skip)->take($perPage)
            //     ->get();
            // $staffData = MakePaginationData($request, $totalCount, 'staffs', $staffs);

            $staffs = Staff::with(['department', 'roles'])
                ->when($roles, function ($query) use ($allowedRoles) {
                    $query->whereHas('roles', function ($query) use ($allowedRoles) {
                        $query->whereIn('name', $allowedRoles);
                    });
                })
                ->where('department_id', $departmentId)
                ->where('is_active', 1)
                ->paginate(20);
            return $staffs;
        } else {
            $staffs = Staff::with(['department', 'roles'])
                ->when($roles, function ($query) use ($allowedRoles) {
                    $query->whereHas('roles', function ($query) use ($allowedRoles) {
                        $query->whereIn('name', $allowedRoles);
                    });
                })
                // ->whereHas('roles', function ($query) use ($allowedRoles) {
                //     $query->whereIn('name', $allowedRoles);
                // })
                ->where('department_id', $departmentId)
                ->where('is_active', 1)
                ->get();

            return $staffs;
        }
    }

    public function getStaffByDepartmentSlug($slug)
    {
        $staffs = Staff::with('department')
            ->whereHas('department', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->where('is_active', 1)
            ->get();
        return $staffs;
    }

    public function changePassword(array $data, int $staffId)
    {
        if (
            !isset($data['old_password']) ||
            !isset($data['new_password']) ||
            !isset($data['confirm_new_password'])
        ) {
            ResponseMessage('All password fields are required.', 422);
        }

        $staff = Staff::find($staffId);
        if (!$staff) {
            ResponseMessage('Staff not found', 404);
        }
        DB::beginTransaction();
        try {
            if (!Hash::check($data['old_password'], $staff->password)) {
                ResponseMessage('Old password is incorrect.', 422);
            }
            if (isset($data['new_password']) && $data['new_password'] !== $data['confirm_new_password']) {
                ResponseMessage('New Password and confirm password do not match', 402);
            }
            $staff->update([
                'password' => ($data['new_password']),
            ]);
            DB::commit();
            ResponseMessage('Password changed successfully', 200);
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }


    public function deleteStaffRole(int $staff_id, int $role_id)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::find($staff_id);
            $role = Role::find($role_id);
            if (!$staff || !$role) {
                ResponseMessage('Staff or Role not found');
            } else {
                $staff->roles()->detach($role->id);
                DB::commit();
                ResponseMessage("Role detach successfully");
            }
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }


    public function deleteStaffInventory(int $staff_id, int $inventory_id)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::find($staff_id);
            $inventory = Inventory::find($inventory_id);
            if (!$staff || !$inventory) {
                ResponseMessage('Staff or inventory not found');
            } else {
                $staff->inventories()->detach($inventory->id);
                DB::commit();
                ResponseMessage("Inventory detach successfully");
            }
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }


    public function deleteStaffFeature(int $staff_id, int $feature_id)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::find($staff_id);
            $feature = Feature::find($feature_id);
            if (!$staff || !$feature) {
                ResponseMessage('Staff or feature not found');
            } else {
                $staff->features()->detach($feature->id);
                DB::commit();
                ResponseMessage("Detach successfully");
            }
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }

    public function staffReport(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $subQuery = DB::table('task_details')
            ->join('tasks', 'task_details.task_id', '=', 'tasks.id')
            ->where('task_details.status', 'passed')
            ->when($month, function ($query) use ($month, $year) {
                $query->whereYear('task_details.date_time', $year)
                    ->whereMonth('task_details.date_time', $month);
            })
            ->select('task_details.staff_id', DB::raw('SUM(tasks.kpi) as kpi'))
            ->groupBy('task_details.staff_id');

        $query = Staff::leftJoinSub($subQuery, 'kpi', function ($join) {
            $join->on('staff.id', '=', 'kpi.staff_id');
        })
            ->select('staff.*', 'kpi.kpi')
            ->where(function ($query) {
                $query->whereNotNull('kpi.kpi')
                    ->where('kpi.kpi', '>', 0);
            });

        if ($request->filled('department_id')) {
            $query->where('staff.department_id', $request->input('department_id'));
        }

        $staffs = $query->orderBy('created_at', 'desc')->paginate(config('common.list_count'));

        ResponseData($staffs);
    }

    public function staffDuty(Request $request, int $id)
    {
        $date = $request->query('date');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $fromDate = $fromDate ? \Carbon\Carbon::parse($fromDate)->startOfDay() : null;
        $toDate = $toDate ? \Carbon\Carbon::parse($toDate)->endOfDay() : null;

        $staff = Staff::with([
            'duties' => function ($query) use ($date, $fromDate, $toDate) {
                if ($date) {
                    $query->whereDate('date', $date);
                } elseif ($fromDate && $toDate) {
                    $query->whereBetween('date', [$fromDate, $toDate]);
                } else {
                    $query->whereDate('date', CurrentDate());
                }
                $query->with('cookingPlace', 'tasks');
            }
        ])->find($id);

        ResponseData($staff);
    }

    public function nrcLists($request)
    {
        $nrc_code = $request->nrc_code;

        $nrcLists = NrcTownship::orderBy('nrc_code', 'asc')
            ->when($nrc_code, function ($q) use ($nrc_code) {
                $q->where('nrc_code', $nrc_code);
            })
            ->get();
        ResponseData($nrcLists);
    }

    public function attachStaffContracts($id, Request $request)
    {
        $staff = Staff::find($id);
        if (!$staff) {
            ResponseMessage("No staff found with given id", 404, false);
        }
        foreach ($request->contract_images as $image) {
            $extension = $image->getClientOriginalExtension();
            $hashedName = md5(uniqid() . microtime()) . '.' . $extension;
            $path = $image->storeAs("staff_contracts/{$staff->id}", $hashedName, 'public');
            $url = Storage::url($path);
            $staff->contracts()->create([
                'contract_file_url' => $url,
                'contract_file_path' => $path,
            ]);
        }
        ResponseData($staff);
    }

    public function updateStaffStatus($request)
    {
        DB::beginTransaction();
        try {
            $staff = Staff::find($request->id);
            if (!$staff) {
                \ResponseMessage('Staff Not found', 419);
            }
            if (!in_array($request->status, StaffStatus::getValues())) {
                \ResponseMessage('Invalid staff status.', 422);
            }
            $staff->status = $request->status;
            $staff->probation_period = $request->probation_period ?? 0;
            $staff->save();
            DB::commit();
            return $staff;
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }

    public function changeStaffPassword($data){
        DB::beginTransaction();
        try {
            $staff = Staff::find($data['staff_id']);
            $staff->update([
                'password' => ($data['new_password']),
            ]);
            DB::commit();
            return $staff;
            // ResponseMessage('Password changed successfully', 200);
        } catch (\Exception $e) {
            DB::rollback();
            ResponseMessage($e->getMessage(), 402);
            throw $e;
        }
    }
}
