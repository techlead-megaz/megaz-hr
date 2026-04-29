<?php

use App\Models\Gender;
use App\Models\AreaType;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Division;
use App\Models\Township;
use App\Models\Complaint;
use App\Models\AreaCategory;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\UsedDefectedItem;
use App\Models\ComplaintCategory;
use App\Models\PurchaseOrderItemLeft;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\AreaController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\AssetItemController;
use App\Http\Controllers\API\AdsAPIController;
use App\Http\Controllers\API\CommonController;
use App\Http\Controllers\API\UomAPIController;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\AccruedController;
use App\Http\Controllers\API\CanteenController;
use App\Http\Controllers\API\CreditorControler;
use App\Http\Controllers\API\DutyAPIController;
use App\Http\Controllers\API\ItemAPIController;
use App\Http\Controllers\API\MenuAPIController;
use App\Http\Controllers\API\PackAPIController;
use App\Http\Controllers\API\PoOrderController;
use App\Http\Controllers\API\RoleAPIController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\CashbookController;
use App\Http\Controllers\API\CreditorController;
// use App\Http\Controllers\API\CustomerAuthController;
use App\Http\Controllers\API\OrderAPIController;
use App\Http\Controllers\API\SkillAPIController;
use App\Http\Controllers\API\StaffAPIController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\AccessoryController;
use App\Http\Controllers\API\EntityAPIController;
use App\Http\Controllers\API\ObjectiveController;
use App\Http\Controllers\API\BookingAPIController;
use App\Http\Controllers\API\FeatureAPIController;
use App\Http\Controllers\API\InvoiceAPIController;
use App\Http\Controllers\API\JournalAPIController;
use App\Http\Controllers\API\PackageAPIController;
use App\Http\Controllers\API\PrepaidAPIController;
use App\Http\Controllers\API\ProfileAPIController;
use App\Http\Controllers\API\SubAccountController;
use App\Http\Controllers\API\CustomerAPIController;
use App\Http\Controllers\API\ExcelImportController;
use App\Http\Controllers\API\HeadAccountController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\TransferAPIController;
use App\Http\Controllers\API\ComplaintAPIController;
use App\Http\Controllers\API\FoodOrderAPIController;
use App\Http\Controllers\API\InventoryAPIController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\DepartmentAPIController;
use App\Http\Controllers\API\AccountPayableController;
use App\Http\Controllers\API\CookingPlaceAPIController;
use App\Http\Controllers\API\MenuCategoryAPIController;
use App\Http\Controllers\API\RoomDiscountAPIController;
use App\Http\Controllers\API\SellingExtraAPIController;
use App\Http\Controllers\API\StaffAdvanceAPIController;
use App\Http\Controllers\API\UsedDefectedAPIController;
use App\Http\Controllers\API\PurchaseOrderAPIController;
use App\Http\Controllers\API\DeliveryChargeAPIController;
use App\Http\Controllers\API\ItemUsageForecastController;
use App\Http\Controllers\API\SaleTargetMenuAPIController;
use App\Http\Controllers\API\SaleTargetResultAPIController;
use App\Http\Controllers\API\AccountReceivableAPIController;
use App\Http\Controllers\API\AssetInventoryLedgerController;
use App\Http\Controllers\API\BenefitController;
use App\Http\Controllers\API\BirthDayPromotionAPIController;
use App\Http\Controllers\API\FixedAssetPurchaseAPIController;
use App\Http\Controllers\API\PurchaseOrderItemLeftController;
use App\Http\Controllers\API\SaleTargetPositionAPIController;
use App\Http\Controllers\API\MenuServiceDiscountAPIController;
use App\Http\Controllers\API\CustomerLevelDiscountAPIController;
use App\Http\Controllers\API\MaterialRequirementsPlanningAPIController;
use App\Http\Controllers\API\Customers\AuthController as CustomerAuthController;
use App\Http\Controllers\API\Customers\AdsAPIController as CustomerAdsAPIController;
use App\Http\Controllers\API\Customers\MenuAPIController as CustomersMenuAPIController;
use App\Http\Controllers\API\Customers\CustomerAPIController as UserAppCustomerAPIController;
use App\Http\Controllers\API\Customers\PackageAPIController as CustomersPackageAPIController;
use App\Http\Controllers\API\Customers\MenuCategoryAPIController as CustomerMenuCategoryAPIController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\GpsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::get('/categories', function () {
    ResponseData(Category::all());
});

Route::get('/complaint_categories', function () {
    ResponseData(ComplaintCategory::all());
});

Route::get('genders', function () {
    ResponseData(Gender::all());
});

Route::get('/area_categories', function (Request $request) {
    ResponseData(
        AreaCategory::when($request->area_type, function ($query, $areaType) {
            // $query->where('name', $areaType);
        })->get()
    );
});

Route::get('/area_types', function () {
    ResponseData(AreaType::all());
});
Route::get('/menu_categories', [MenuCategoryAPIController::class, 'getMenuCategories']);


Route::get('/divisions', function () {
    ResponseData(Division::with('townships')->get());
});

// Route::post('/customer_login',[CustomerAuthController::class,'customerLogin']);

Route::post('/login', [AuthController::class, 'login']);
Route::controller(FeatureAPIController::class)->group(function () {
    Route::post('/feature_import', 'featureImport');
});
Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/staffs/change_password', [StaffAPIController::class, 'changeStaffPassword']);
    Route::get('/staff_reports', [StaffAPIController::class, 'staffReport']);
    Route::get('/staffs', [StaffAPIController::class, 'getStaffData']);
    Route::get('/staffs/{id}', [StaffAPIController::class, 'detailStaff']);
    Route::post('/staffs', [StaffAPIController::class, 'createStaff']);
    Route::post('/staffs/{id}', [StaffAPIController::class, 'updateStaff'])->middleware('permission:staff.status-update');
    Route::post('/update_staff_status', [StaffAPIController::class, 'updateStatus']);
    Route::delete('/staffs/{id}', [StaffAPIController::class, 'deleteStaff']);
    Route::delete('/staffs/{staff_id}/roles/{role_id}', [StaffAPIController::class, 'deleteRoleStaff']);
    Route::delete('/staffs/{staff_id}/inventories/{inventory_id}', [StaffAPIController::class, 'deleteInventoryStaff']);
    Route::delete('/staffs/{staff_id}/features/{feature_id}', [StaffAPIController::class, 'deleteFeatureStaff']);
    Route::get('/dddepartment_id}/staffs', [StaffAPIController::class, 'getStaffByDepartment']);
    Route::get('/staff_by_department_slug/{slug}', [StaffAPIController::class, 'getStaffByDepartmentSlug']);
    Route::get('/staff_balances', [StaffAPIController::class, 'staffBalanceList']);
    Route::get('/staff_balances/{id}', [StaffAPIController::class, 'detailStaffBalance']);
    Route::get('/staff/{id}/duties', [StaffAPIController::class, 'getStaffWithDuties']);
    Route::post('/staffs/{staffId}/change_password', [StaffAPIController::class, 'changePassword']);
    Route::post('/staff/{id}/upload_contracts', [StaffAPIController::class, 'uploadStaffContracts']);
    Route::get('/nrcs', [StaffAPIController::class, 'nrcLists']);
    // Route::get('/profile', [ProfileAPIController::class, 'getProfile']);
    // Route::post('/profile/change_password', [ProfileAPIController::class, 'updatePassword']);

    Route::get('/staff/complaints', [ComplaintAPIController::class, 'getStaffComplaints']);
    Route::post('/complaints', [ComplaintAPIController::class, 'createComplain']);
    Route::get('/complaints/{id}', [ComplaintAPIController::class, 'complaintDetail']);
    Route::post('/complaints/{id}', [ComplaintAPIController::class, 'updateComplain']);
    Route::delete('/complaints/{id}', [ComplaintAPIController::class, 'deleteComplain']);
    Route::post('/complaints/{id}/update_status', [ComplaintAPIController::class, 'complainStatusChange']);
    Route::get('/complaints_responsibles', [ComplaintAPIController::class, 'complaintResponsiblesByStaff']);
    Route::get('/complaints_carbon_copies', [ComplaintAPIController::class, 'complaintCarbonCopiesByStaff']);

    // deleted related complaint
    Route::delete('/complaints_images/{id}', [ComplaintAPIController::class, 'deleteComplaintImage']);
    Route::delete('/complaints_responsibles/{id}', [ComplaintAPIController::class, 'deleteComplaintResponsible']);
    Route::delete('/complaints_carbon_copies/{id}', [ComplaintAPIController::class, 'deleteComplaintCarbonCopy']);


    Route::get('/staffs_by_role', [StaffAPIController::class, 'getStaffListBySupervisor']);
    // Route::get('/supervisor/staff/{staffId}/tasks', action: [TaskController::class, 'getStaffTasksBySupervisor']);
    // Route::get('/task_list', [TaskController::class, 'getStaffTasksBySupervisor']);

    // Route::controller(TaskController::class)->group(function () {
    //     Route::post('/tasks/{id}/double_checked', 'taskDoubleChecked');
    //     Route::post('/custom_tasks', 'createCustomTask');
    //     Route::get('/custom_tasks', 'getCustomTasks');
    //     Route::get('/custom_tasks/{id}', 'customTaskDetail');
    //     Route::post('/custom_tasks/{id}', 'updateCustomTask');

    //     // add image
    //     Route::post('/tasks/{id}/add_images', 'addTaskImage');
    //     Route::get('/tasks/{id}/images', 'getTaskImages');
    //     Route::delete('/tasks_images/{id}', 'deleteTaskImage');
    // });

    
    Route::resource('head_accounts', HeadAccountController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::resource('sub_accounts', SubAccountController::class)->only(['index', 'store', 'show', 'destroy']);
    // Route::resource('accounts', AccountController::class)->only(['index', 'store', 'show', 'destroy']);
    // Route::controller(AccountController::class)->group(function () {
    //     Route::get('sub_account_by_head_account/{id}', 'getSubAccountByHeadAccount');
    //     Route::get('get_cash_account', 'getCashAccount');
    //     Route::get('account_by_sub_account/{id}', 'accountBySubAccount');
    //     Route::post('create_second_account', 'createSecondAccount');
    //     Route::post('create_third_account', 'createThirdAccount');
    //     Route::get('get_second_account', 'getSecondAccount');
    //     Route::get('get_third_account', 'getThirdAccount');
    // });
   

    // Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'show', 'destroy']);
    // Route::controller(TransactionController::class)->group(function () {
    //     Route::post('transaction_confirmed', 'transactionConfirmed');
    // });

  
    Route::controller(CommonController::class)->group(function () {
        Route::post('is_active', 'toggleIsActive');
    });

   
    Route::resource('notifications', NotificationController::class)->only(['index']);
    Route::get('notification_by_user', [NotificationController::class, 'notificationUsersData']);

    Route::post('notifications/set_seen', [NotificationController::class, 'setSeenNotifications']);
    Route::post('notifications/{notificationId}/mark_read', [NotificationController::class, 'markReadNotification']);



 
    Route::get('/inventory/all', [InventoryAPIController::class, 'getallInventories']);
    Route::get('/inventories', [InventoryAPIController::class, 'getInventoryData']);
    Route::get('/get_inventory', [InventoryAPIController::class, 'getInventory']);
    Route::post('/inventories', [InventoryAPIController::class, 'createInventory']);
    Route::get('/inventories/{inventory}', [InventoryAPIController::class, 'detail']);
    Route::put('/inventories/{id}', [InventoryAPIController::class, 'updateInventory']);
    Route::delete('/inventories/{id}', [InventoryAPIController::class, 'deleteInventory']);
    Route::get('/inventories/{inventoryId}/ledgers', [InventoryAPIController::class, 'getInventoryLedgers']);
    Route::get('inventory_list', [InventoryAPIController::class, 'inventoryList']);


    Route::controller(StaffAdvanceAPIController::class)->group(function () {
        Route::post('/staff_advances', 'createStaffAdvance');
    });

  
    Route::controller(SkillAPIController::class)->group(function () {
        Route::get('/skills', 'listAllSkills');
        Route::post('/skills', 'createSkill');
        Route::get('/skills/{id}', 'skillDetail');
        Route::post('/skills/{id}', 'updateSkill');
        Route::delete('/skills/{id}', 'deleteSkill');
        Route::get('/roles/{role_id}/skills', 'skillByRole');
    });

  
    Route::controller(GpsController::class)->group(function () {
        Route::post('gps', 'updateOrCreateGps');
        Route::get('/gps', 'getGPS');
        Route::get('/gps/{id}', 'getGPSById');
        // Route::post('/gps/{id}', 'updateGPSById');
    });
    
    Route::prefix('benefits')->controller(BenefitController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'updateOrCreateBenefit');
        Route::get('/{id}', 'detailBenefit');
    });
    Route::prefix('admin/benefit_requests')->controller(BenefitController::class)->group(function () {
        Route::get('/', 'listBenefitRequest');
        Route::post('/update_status', 'updateStatusBenefitRequest');
    });
});

Route::controller(FeatureAPIController::class)->group(function () {
    Route::get('/features', 'getFeatureData');
    Route::post('/feature_import', 'featureImport');
    Route::get('feature_by_department/{department_id}', 'getFeatureByDepartment');
    Route::get('feature_by_module', 'getFeatureByModule');
});
// Route::controller(AdsAPIController::class)->group(function () {
//     Route::get('/ads', 'getAds');
//     Route::post('/ads', 'createAds');
//     Route::post('/ads/{id}', 'editAds');
//     Route::get('/ads/{id}', 'adsDetail');
//     Route::delete('/ads/{id}', 'deleteAds');
//     Route::get('/latest_ads', 'latestAds');
// });

Route::controller(ExcelImportController::class)->group(function () {
    Route::post('/import_account', 'importAccount');
});

Route::get('/areas', [AreaController::class, 'getAreas']);
Route::get('/area_types/{id}/areas', [AreaController::class, 'getAreaByAreaType']);
Route::get('/area_categories/{id}/areas', [AreaController::class, 'getAreaByAreaCategory']);
Route::post('/areas', [AreaController::class, 'createArea']);
Route::put('/areas/{id}', [AreaController::class, 'updateArea']);
Route::delete('/areas/{id}', [AreaController::class, 'deleteArea']);
Route::get('areas_by_department/{department_id}', [AreaController::class, 'getAreaByDepartment']);
Route::get('/sellings_areas', [AreaController::class, 'getSellingAreas']);
Route::get('/cooking_areas', [AreaController::class, 'getCookingAreas']);


Route::get('/departments', [DepartmentAPIController::class, 'getDepartmentData']);
Route::post('/departments', [DepartmentAPIController::class, 'createDepartment']);
Route::post('/departments/{id}', [DepartmentAPIController::class, 'updateDepartment']);
Route::get('/departments/{id}/staffs', [StaffAPIController::class, 'getDepartmentStaff']);

Route::get('/roles', [RoleAPIController::class, 'getRoleData']);
Route::get('/role_by_department/{department_id}', [RoleAPIController::class, 'getRoleByDepartment']);
Route::get('role_by_departments',[RoleAPIController::class,'getRoleByDepartments']);
Route::post('/roles', [RoleAPIController::class, 'createRole']);
Route::post('/roles/{id}', [RoleAPIController::class, 'updateRole']);
Route::post('/roles/{roleId}/available_toggle', [RoleAPIController::class, 'roleAvailableToggle']);


Route::controller(BankController::class)->group(function () {
    Route::get('/banks', 'getAllBanks');
    Route::post('/banks', 'createBank');
});

Route::get('/staff_lists', [StaffAPIController::class, 'staffList']);


Route::get('/complaints', [ComplaintAPIController::class, 'getComplainData']);



// Route::get('/menus/{menu_id}/areas', [MenuAPIController::class, 'areaByMenu']);


Route::get('get_inventory', [InventoryAPIController::class, 'getInventory']);
// feature


// Route::controller(TagController::class)->group(function () {
//     Route::get('/tags', 'getTags');
//     Route::post('/tags', 'createTag');
// });

