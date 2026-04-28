<?php

namespace App\Http\Controllers\API;

use App\Enums\StaffStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StaffCreateRequest;
use App\Http\Requests\Staff\StaffStatusUpdate;
use App\Http\Requests\Staff\StaffUpdateRequest;
use App\Models\Staff;
use App\Repositories\Staff\StaffRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Psy\Readline\Hoa\_Protocol;

class StaffAPIController extends Controller
{
    //
    protected $staffRepo;

    public function __construct(StaffRepositoryInterface $staffRepo)
    {
        $this->staffRepo = $staffRepo;
    }

    public function staffBalanceList(Request $request)
    {
        $staffBalances = $this->staffRepo->staffBalanceList($request);
    }

    public function detailStaffBalance(Request $request, int $id)
    {
        $staffBalance = $this->staffRepo->staffBalanceDetail($request, $id);
    }

    public function getStaffData(Request $request)
    {
        $staffs = $this->staffRepo->listAllData($request);
        ResponseData($staffs);
    }

    public function createStaff(StaffCreateRequest $request)
    {
        $data = $request->except(['nrc_front_image', 'nrc_back_image', 'household_registration_image', 'profile_image']);

        // $data['roles'] = explode(',', $request->roles);
        $data['role_id'] = $request->role_id;
        $data['feature_ids'] = json_decode($request->feature_ids);
        $data['inventory_ids'] = ($request->inventory_ids) ? json_decode($request->inventory_ids) : [];
        $data['skill_ids'] = ($request->skill_ids) ? json_decode($request->skill_ids) : [];
        $staff = $this->staffRepo->createData($data);

        $data = [];
        if ($request->hasFile('nrc_front_image')) {
            $uploadedFile = UploadFileToServer($request, 'nrc_front_image', "staff_gov_docs/{$staff->id}");
            $data['nrc_front_url'] = $uploadedFile['file_url'];
            $data['nrc_front_path'] = $uploadedFile['file_path'];
        }
        if ($request->hasFile('nrc_back_image')) {
            $uploadedFile = UploadFileToServer($request, 'nrc_back_image', "staff_gov_docs/{$staff->id}");
            $data['nrc_back_url'] = $uploadedFile['file_url'];
            $data['nrc_back_path'] = $uploadedFile['file_path'];
        }
        if ($request->hasFile('household_registration_image')) {
            $uploadedFile = UploadFileToServer($request, 'household_registration_image', "staff_gov_docs/{$staff->id}");
            $data['household_registration_url'] = $uploadedFile['file_url'];
            $data['household_registration_path'] = $uploadedFile['file_path'];
        }
        if ($request->hasFile('profile_image')) {
            $uploadedFile = UploadFileToServer($request, 'profile_image', "staff_profile_images/{$staff->id}");
            $data['profile_image_url'] = $uploadedFile['file_url'];
            $data['profile_image_path'] = $uploadedFile['file_path'];
        }

        $staff->update($data);

        ResponseData($staff);
    }

    public function updateStaff(StaffUpdateRequest $request, $id)
    {
        $data = $request->except(['nrc_front_image', 'nrc_back_image', 'household_registration_image', 'profile_image']);

        $staff = Staff::find($id);
        if (!$staff) {
            ResponseMessage('Staff not found with given ID', 404);
        }

        if ($request->hasFile('nrc_front_image')) {
            $uploadedFile = UploadFileToServer($request, 'nrc_front_image', "staff_gov_docs/{$staff->id}");
            $data['nrc_front_url'] = $uploadedFile['file_url'];
            $data['nrc_front_path'] = $uploadedFile['file_path'];
        }
        if ($request->hasFile('nrc_back_image')) {
            $uploadedFile = UploadFileToServer($request, 'nrc_back_image', "staff_gov_docs/{$staff->id}");
            $data['nrc_back_url'] = $uploadedFile['file_url'];
            $data['nrc_back_path'] = $uploadedFile['file_path'];
        }
        if ($request->hasFile('household_registration_image')) {
            $uploadedFile = UploadFileToServer($request, 'household_registration_image', "staff_gov_docs/{$staff->id}");
            $data['household_registration_url'] = $uploadedFile['file_url'];
            $data['household_registration_path'] = $uploadedFile['file_path'];
        }
        if ($request->hasFile('profile_image')) {
            $uploadedFile = UploadFileToServer($request, 'profile_image', "staff_gov_docs/{$staff->id}");
            $data['profile_image_url'] = $uploadedFile['file_url'];
            $data['profile_image_path'] = $uploadedFile['file_path'];
        }
        $data['role_id'] = $request->role_id;
        $data['feature_ids'] = json_decode($request->feature_ids);
        $data['inventory_ids'] = ($request->inventory_ids) ? json_decode($request->inventory_ids) : [];
        $data['skill_ids'] = ($request->skill_ids) ? json_decode($request->skill_ids) : [];

        $staff = $this->staffRepo->updateData($data, $id);


        ResponseData($staff);
    }
    public function changePassword(Request $request, int $staffId)
    {
        $staff = $this->staffRepo->changePassword($request->all(), $staffId);
        ResponseData($staff);
    }

    public function deleteStaff($id)
    {
        $staffDeleted = $this->staffRepo->deleteData($id);
        if ($staffDeleted) {
            ResponseMessage("Staff deleted");
        } else {
            ResponseMessage('staff not found or some error occur');
        }
    }

    public function getStaffListBySupervisor(Request $request)
    {
        $staff = Staff::find($request->user()->id);

        $roles = $staff->roles->pluck('name')->toArray();
        // $isSupervisorOrManager = in_array('Supervisor', $roles) || in_array('Manager', $roles) || in_array('Captain', $roles) || in_array('Chief Accountant', $roles) || in_array('Demi Chef', $roles);
        // if (!$isSupervisorOrManager) {
        //     ResponseMessage('Not authorized', 403);
        // }
        $allowedRoles = ['Supervisor', 'Manager', 'Captain', 'Chief Accountant', 'Sous Chef', 'Senior Receptionist'];

        if (!array_intersect($roles, $allowedRoles)) {
            ResponseMessage('Not authorized', 403);
        }

        $staff = $this->staffRepo->getStaffByDepartment($request, $staff->department_id, $roles);
        ResponseData($staff);
    }

    public function detailStaff(int $id)
    {
        $staff = $this->staffRepo->staffDetail($id);
        ResponseData($staff);
    }

    public function deleteRoleStaff(int $staff_id, int $role_id)
    {
        $staff = $this->staffRepo->deleteStaffRole($staff_id, $role_id);
    }

    public function deleteInventoryStaff(int $staff_id, int $inventory_id)
    {
        $staff = $this->staffRepo->deleteStaffInventory($staff_id, $inventory_id);
    }


    public function deleteFeatureStaff(int $staff_id, int $feature_id)
    {
        $staff = $this->staffRepo->deleteStaffFeature($staff_id, $feature_id);
    }

    public function getStaffByDepartment(Request $request, int $department_id)
    {
        $staff = $this->staffRepo->getStaffByDepartment($request, $department_id);
        ResponseData($staff);
    }

    public function getStaffByDepartmentSlug($slug)
    {
        $staff = $this->staffRepo->getStaffByDepartmentSlug($slug);
        ResponseData($staff);
    }

    public function staffReport(Request $request)
    {
        $staff = $this->staffRepo->staffReport($request);
    }

    public function getStaffWithDuties(Request $request, int $id)
    {
        $this->staffRepo->staffDuty($request, $id);
    }

    public function nrcLists(Request $request)
    {
        $staff = $this->staffRepo->nrcLists($request);
    }

    public function staffList(Request $request)
    {
        $staff = $this->staffRepo->staffList($request);
        ResponseData($staff);
    }

    public function uploadStaffContracts(Request $request, $id)
    {
        $request->validate([
            'contract_images' => 'required|array|min:1',
            'contract_images.*' => 'required|file|',
        ]);

        $this->staffRepo->attachStaffContracts($id, $request);
    }

    public function updateStatus(StaffStatusUpdate $request)
    {
        $data = $this->staffRepo->updateStaffStatus($request);
        \ResponseMessage("Status Update  Successfully");
    }

    public function getDepartmentStaff(Request $request, int $id)
    {
        $staff = Staff::where('department_id', $id)
            ->whereIn('status', [
                StaffStatus::PROBATION->value,
                StaffStatus::PERMANENT->value,
            ])
        ->get();
        ResponseData($staff);
    }

    public function changeStaffPassword(Request $request){
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'new_password' => 'required|min:6',
            'admin_password'=>'required|current_password',
        ]);
        $data = $this->staffRepo->changeStaffPassword($request);
        ResponseMessage("Password changed successfully");
    }
}
