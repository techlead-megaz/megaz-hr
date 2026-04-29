<?php

use App\Http\Controllers\API\AlignmentController;
use App\Http\Controllers\API\AssetItemEquipmentAssignController;
use App\Http\Controllers\API\BenefitController;
use App\Http\Controllers\API\ContractCategoryController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\CvController;
use App\Http\Controllers\API\ExamController;
use App\Http\Controllers\API\HandBookeController;
use App\Http\Controllers\API\InterviewController;
use App\Http\Controllers\API\KpiSnapshotController;
use App\Http\Controllers\API\LeaveController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\MeetingMinuteController;
use App\Http\Controllers\API\MenuItemImportController;
use App\Http\Controllers\API\OffDayHrController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ResignationController;
use App\Http\Controllers\API\SalaryController;
use App\Http\Controllers\API\StaffEquipmentHandoverController;
use App\Http\Controllers\API\StaffTimeShiftController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
  Route::prefix('hr')->controller(OffDayHrController::class)->group(function () {
    Route::get('/off_days', 'getOffDays');
    Route::post('/off_days', 'createOffDay');
    Route::delete('/off_days/{dayInOffDayId}', 'deleteOffDay');
    Route::post('/public_holidays', 'createPublicHoliday');
    Route::post('off_day_settings/toggle', 'toggleOffDaySetting');
    Route::get('off_day_settings', 'getOffDaySetting');

    Route::get('/off_day_requests', 'getOffDayRequests');
    Route::post('/off_day_requests/{id}/status', 'updateOffDayRequestStatus');
  });
  Route::prefix('hr')->controller(LeaveController::class)->group(function () {
    Route::get('/leave_categories', 'getLeaveCategoryLists');
    Route::post('/leave_categories', 'createLeaveCategory');
    Route::post('/leave_allowances', 'createLeaveAllowance');
    Route::get('/leave_allowances', 'getLeaveAllowance');
    Route::delete('/leave_allowances/{id}', 'deleteLeaveAllowance');
    Route::post('/leaves', 'createLeave');
    Route::get('/leaves', 'getLeave');
    Route::post('/leaves/{id}', 'updateLeave');
    Route::delete('/leaves/{id}', 'deleteLeave');
    Route::get('/leave_totals_by_staff/{staffId}', 'getLeaveTotalByStaff');

    Route::post('/exit_categories', 'createExitCategory');
    Route::get('/exit_categories', 'getExitCategoryLists');
    Route::post('/exit_passes', 'createExitPass');
    Route::get('/exit_passes', 'getExitPass');
    Route::post('/exit_passes/{id}', 'updateExitPass');
    Route::delete('/exit_passes/{id}', 'deleteExitPass');
    Route::get('/exit_passes_by_staff/{staffId}', 'getExitPassByStaff');
    Route::get('/staff_lists_by_role/{roleId}/department/{departmentId}', 'getStaffListByRoleAndDepartment');
  });

  Route::prefix('hr')->controller(SalaryController::class)->group(function () {
    Route::get('/salary_allowances', 'getAllowances');
    Route::post('/salary_allowances', 'createAllowance');
    Route::post('/salary_setups', 'storeSalarySetUp');
    Route::get('/salary_setups', 'getSalarySetUp');
    Route::get('/salary_setups/{id}', 'getSalarySetUpById');
    Route::post('/salary_setups/{id}', 'updateSalarySetUp');
    Route::delete('/salary_setup/salary_allowances/{salaryAllowanceId}', 'deleteSalaryAllowance');
    Route::get('/salary-setup-by-role/{roleId}', 'getSalarySetupByRoleId');
    Route::get('/salaries', 'getSalaries');
    Route::post('/salaries', 'createSalary');
    Route::post('/salaries/{id}', 'updateBasicSalary');
    Route::get('/overtime_fees', 'getOvertimeFee');
    Route::post('/overtime_fees', 'createOvertimeFee');
    Route::delete('/overtime_fees/{id}', 'deleteOvertimeFee');
    Route::get('/overtime_categories', 'getOvertimeCategories');
    Route::post('/overtime_categories', 'createOvertimeCategories');
    Route::post('/overtimes', 'createOvertime');
    Route::get('/overtimes', 'getOvertimes');
    Route::post('/approval/overtimes/{id}', 'setOvertimeApproval');
    Route::get('/mobile/overtimes/{staffId}', 'getMobileOvertimesByStaffId');

    Route::get('/salary_batches', 'getSalaryBatch');
    Route::post('/salary_batches', 'createSalaryBatch');
    Route::get('/salary_batches/{id}', 'getSalaryBatchById');
    Route::post('/salary_batches/{id}', 'updateSalaryBatch');
    Route::delete('/salary_batches/{id}', 'deleteSalaryBatch');
    Route::delete('/salary_batch_staffs/{id}', 'deleteSalaryBatchStaff');

    Route::get('/calculate_salary', 'calculateSalary');
    Route::get('/allowance_types', 'getAllowanceTypes');
    Route::post('/pay_slips', 'createPaySlip');
    Route::get('/pay_slips', 'getPaySlips');
    Route::delete('/pay_slips/{id}', 'deletePaySlip');
    Route::get('pay_slips/confirm/{id}', 'confirmPaySlip');

    Route::get('/export-salary', 'exportSalary');
    //mobile
    Route::get('/staff_pay_slips', 'getStaffPaySlip');
  });
  Route::prefix('hr')->controller(ResignationController::class)->group(function () {
    Route::get('/resignation_categories', 'getResignationCategoryLists');
    Route::post('/resignation_categories', 'createResignationCategory');
    Route::post('/resignations', 'createResignation');
    Route::get('/resignations', 'getAllResignations');
    Route::get('/resignations/{id}', 'getResignationById');

    Route::post('/resignations/{id}', 'updateResignation');
    Route::get('/resignations-by-staff/{staffId}', 'getResignationByStaffId');
  });
  Route::prefix('hr')->controller(CvController::class)->group(function () {
    Route::get('/cvs', 'getAllCvs');
    Route::post('/cvs/{id}', 'updateCv');
    Route::delete('/cvs/{id}', 'deleteCv');
    // Route::get('departments/{depId}/roles/{role_id}/skills', 'skillByRoleAndDepartment');
    Route::get('/cvs/{id}', 'getCvById');
    Route::post('/cvs/{id}/status', 'updateCvStatus');
    Route::get('/salary_setup/department/{departmentId}/role/{roleId}', 'getSalarySetupByDepartmentIdAndRoleId');
    Route::post('/new-staff-salary', 'createNewStaffSalary');
    Route::post('/new-staff-join-date', 'storeNewStaffJoinDate');
    Route::get('communication_form/{id}','getCommunicationForm');
  });
  Route::prefix('hr')->controller(ExamController::class)->group(function () {
    Route::get('/exams', 'getAllExams');
    Route::post('/exams', 'createExam');
    Route::post('/exams/{id}', 'updateExam');
    Route::delete('/exams/{id}', 'deleteExam');
    Route::get('/exams/{id}', 'getExamById');
    Route::delete('/exam_skills/{id}', 'deleteExamSkill');
    Route::delete('/grades/{id}', 'deleteGrade');
    Route::delete('/exam_questions/{id}', 'deleteExamQuestion');
    Route::post('toggle/exam_questions/{id}', 'toggleExamQuestion');
    Route::get('get_exam_by_role/{id}/exam_type/{examType}', 'getExamByRole');
    Route::post('staff_exam/answer', 'answerExamQuestion');
  });
  Route::prefix('hr')->controller(InterviewController::class)->group(function () {
    Route::get('/interviews-by-role/{roleId}', 'getInterviewsByRoleId');
    Route::get('/interviews/{id}', 'getInterviewById');
    Route::post('/interviews', 'storeInterview');
    Route::get('/interviews-results', 'getInterviewResults');
  });
  Route::prefix('hr')->controller(LocationController::class)->group(function () {
    Route::get('/locations', 'getAllLocations');
    Route::post('/locations', 'createLocation');
    Route::post('/added_location', 'addedLocation');
    Route::get('/locations/{locationId}/floor/{floorId}', 'getPlaceByLocationAndFloorId');
    Route::post('/places/{placeId}/assign-staff', 'assignStaffToPlace');
    Route::get('places', 'getAllPlace');
  });
  Route::prefix('hr')->controller(StaffTimeShiftController::class)->group(function () {
    Route::post('/staff_time_shifts', 'createStaffTimeShift');
    Route::get('/staff_time_shifts', 'getStaffTimeShifts');
    Route::post('/staff_time_shifts/{id}/status', 'updateStaffTimeShiftStatus');
    Route::post('/staff_time_shifts/{id}/request_off_day', 'createOffDayRequest');
  });
  // Route::prefix('hr')->controller(AssetItemEquipmentAssignController::class)->group(function () {
  //   Route::post('/asset-assignments', 'createAssetAssign');
  //   Route::get('/asset-assignments', 'getAssetAssigns');
  //   Route::post('/equipment-assignments', 'createEquipmentAssign');
  //   Route::get('/equipment-assignments', 'getEquipmentAssigns');
  //   Route::get('/equipment-assignments/staff', 'getEquipmentAssignsByStaffId');
  // });


  Route::prefix('hr')->controller(HandBookeController::class)->group(function () {
    Route::post('/hand-books', 'updateOrCreateHandBook');
    Route::get('/hand-books', 'getHandBookList');
    Route::get('/hand-books/{id}', 'getHandBookById');
    Route::delete('/hand-books/{id}', 'deleteHandBook');
    Route::get('/handbooks', 'getHandBooks');
  });

  Route::controller(StaffEquipmentHandoverController::class)->group(function () {
    Route::post('/staff-equipment-handovers', 'createStaffEquipmentHandover');
    Route::post('/staff-equipment-handovers/{id}/confirm', 'confirmHandover');
    Route::post('/staff-equipment-handovers/{id}/cancel', 'cancelHandover');
    Route::get('/staff-timeshifts/{staffId}', 'getStaffTimeshift');
    Route::get('/handover-staffs', 'getHandoverStaffs');
    Route::get('/staff-equipment-handovers/{id}', 'getStaffEquipmentHandoverById');
    Route::get('/lost-items', 'getLostItems'); //admin panel
  });
  // Route::controller(MenuItemImportController::class)->group(function () {
  //   Route::post('/ready-to-sale-menu-item-import', 'readyToSaleMenuItemImport');
  //   Route::post('/raw-item-import', 'rawItemImport');
  // });

  Route::prefix('benefits')->controller(BenefitController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'updateOrCreateBenefit');
    Route::get('/{id}', 'detailBenefit');
  });
  Route::prefix('admin/benefit_requests')->controller(BenefitController::class)->group(function () {
    Route::get('/', 'listBenefitRequest');
    Route::post('/update_status', 'updateStatusBenefitRequest');
  });

  Route::prefix('contract_categories')->controller(ContractCategoryController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/', 'index');
  });
  Route::prefix('contracts')->controller(ContractController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/', 'index');
    Route::post('add_staff', 'addStaffToContract');
    Route::get('/staff', 'getContractStaff');
    Route::post('/update_status','updateStatus');
  });
  Route::prefix('projects')->controller(ProjectController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{projectId}/instruction_by_project','getInstructionByProject');
  });
  Route::prefix('meeting_minutes')->controller(MeetingMinuteController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{meetingMinute}', 'show');
    Route::get('meeting/{meetingId}','getMeetingMinuteByMeetingId');
  });
  Route::prefix('kpi_snapshots')->controller(KpiSnapshotController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
  });
  Route::prefix('alignments')->controller(AlignmentController::class)->group(function () {
    Route::get('/', 'index');
  });
});
Route::prefix('hr')->controller(CvController::class)->group(function () {
  Route::post('/cvs', 'createCv');
  Route::post('/communication_form', 'createCommunicationForm');
  Route::get('departments/{depId}/roles/{role_id}/skills', 'skillByRoleAndDepartment');
});
