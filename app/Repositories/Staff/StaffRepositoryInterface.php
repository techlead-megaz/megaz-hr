<?php

namespace App\Repositories\Staff;

use Illuminate\Http\Request;

use App\Models\Staff;

interface StaffRepositoryInterface
{
    public function listAllData(Request $request);

    public function staffBalanceList(Request $request);

    public function staffBalanceDetail(Request $request, int $id);

    public function createData(array $data);

    public function updateData(array $data, int $id);

    public function changePassword(array $data, int $staffId);

    public function deleteData($id);

    public function getStaffByDepartment(Request $request, int $departmentId, array $roles = null);

    public function getStaffByDepartmentSlug($slug);

    public function staffDetail(int $id);

    public function deleteStaffRole(int $staff_id, int $role_id);

    public function deleteStaffInventory(int $staff_id, int $inventory_id);

    public function deleteStaffFeature(int $staff_id, int $feature_id);

    public function staffReport(Request $request);

    public function staffDuty(Request $request, int $id);

    public function nrcLists(Request $request);

    public function staffList(Request $request);

    public function attachStaffContracts($id, Request $request);

    public function updateStaffStatus($request);

    public function changeStaffPassword($request);
}
