<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ObjectiveController;
use App\Http\Controllers\API\MRPForecastController;
use App\Http\Controllers\API\JobDescriptionController;

Route::middleware('auth:api')->group(function () {
  Route::controller(ObjectiveController::class)->group(function () {
    //admin 
    Route::get('/objectives', 'getObjectives');
    Route::get('/roles_department/{departmentId}', 'getRolesByDepartmentId');
    Route::post('/objectives', 'store');
    // Route::post('/objectives/{id}', 'update');
    Route::get('/objectives/{id}', 'getObjectiveById');
    Route::delete('/objectives/{id}', 'deleteObjective');
    Route::get('dashboard-okr', 'dashboardOkr');
    Route::get('okr_assign_by_staff', 'okrAssignByStaff');
    Route::get('objective_staff_by_project/{project_id}','getObjectiveStaffByProject');

    //okr assign duties by objective staff
    Route::get('/objectives_keys_by_staff/{staff_id}', 'getObjectiveKeysByStaffId');
    Route::get('/assign_duties', 'getAssignDutiesByObjectiveKeys');
    Route::get('/assign_duties/{id}', 'showAssignDutiesById');
    Route::delete('/assign_duties/{id}', 'deleteAssignDutiesById');
    Route::delete('/assign_duties/objective_key_staff/{id}', 'deleteAssignObjKeyStaffById');

    //okr assign modified api
    Route::post('/okr-assigns', 'storeAssignDutiesByObjectives');
    Route::get('/okr-assigns', 'getOkrAssigns');
    Route::get('/okr-assigns/{okrAssignId}', 'getOkrAssignById');
    Route::delete('/okr-assigns/{okrAssignId}', 'deleteOkrAssignById');

    //mobile-api
    Route::get('/daily/objectives', 'objectiveLists');
    Route::get('/daily/objectives_key/{objId}', 'getdailyObjectives');
    Route::get('/daily/objectives/{staffId}', 'getdailyObjectivesByStaffId');
    Route::get('/daily/objective_by_accountable/{staffId}', 'getDailyObjectiveByAccountable');
    Route::get('objectives/staff_by_accountable/{staffId}', 'getStaffByAccountable');
    Route::post('/daily/objectives_key_staff/{objStaffId}', 'updateDailyObjective');
    Route::post('/objectives/key_staff/{objStaffId}/images', 'storeImages');
    Route::post('/objectives/key_staff/{objStaffId}/images/update', 'updateImages');
    Route::get('/objectives/key_staff/{objStaffId}/images', 'getObjKeyStaffImage');
    Route::delete('/objectives/key_staff/images/{objStaffId}', 'deleteObjKeystaffImage');
    Route::get('/complete-objective/{objectiveId}/staff/{staffId}', 'getCompletedObjKeysByStaffId');
    Route::post('objectives/reject_objective_key', 'rejectObjectKeyByObjectiveStaffId');

    //ktv-objective-tree
    Route::get('/ktv/entity_room', 'getKtvRoom');
    Route::get('/ktv/objectives/{departmentId}', 'getKtvObjective');
    Route::get('/ktv/objective_trees', 'getKtvObjectiveTree');
    Route::post('/ktv/objective_trees', 'storeKtvObjectiveTree');
    Route::get('/ktv/objective_trees/{id}', 'getKtvObjTreeById');
    Route::post('/ktv/objective_trees/{id}', 'updateKtvObjTree');
  });

  // Route::controller(MRPForecastController::class)->group(function () {
  //   Route::post('/forecast/menus', 'getForcastMenus');
  //   Route::post('/forecast/menus/{menuId}', 'getForcastMenusByMenuId');
  //   Route::post('/forecast/hr', 'getForcastHR');
  //   Route::post('/forecast/hr/{menuId}', 'getForcastHrByMenuId');
  //   Route::post('/forecast/raw_materials/{menuId}', 'getForcastRawMaterialByMenuId');
  //   Route::post('/forecast/raw_materials', 'getForcastRawMaterial');

  //   Route::get('/forecasts/purchase_orders', 'getPoForecasts');
  //   Route::post('/forecasts/purchase_orders_item/{itemId}', 'storePoForecastsByItemId');

  //   Route::post('/forecasts', 'storeForecast');
  //   Route::get('/forecasts_monthly_menus', 'getMonthlyMenuForecasts');
  //   Route::get('/forecasts_monthly_menu/{mrpForecastId}', 'getMonthlyMenuForecastsById');
  //   Route::post('/forecasts_monthly_menu/{mrpForecastId}', 'updateMenuForecast');
  //   Route::delete('/forecasts_mrp_monthly_menu/{target_mrp_forecast_id}', 'deleteMenuForecast');

  //   //ktv forecasts
  //   Route::get('/forecasts/monthly/ktv_product_tree', 'getMonthlyKTVProductTreeForecasts');
  //   Route::post('/forecast/ktvs', 'getForecastKTV');
  //   Route::post('/forecast/ktvs/{entityId}', 'getForecastKTVByEntityId');
  //   Route::post('/forecast/ktvs_raw_materials', 'getForecastKTVRawMaterials');
  //   Route::post('/forecast/ktvs_raw_materials/{entityId}', 'getForecastKTVRawMaterialsByEntityId');
  //   Route::post('/forecast/ktvs_hr', 'getForecastKTVHr');
  //   Route::post('/forecast/ktvs_hr/{entityId}', 'getForecastKTVHrByEntityId');
  //   Route::delete('/mrp_forecasts/{mrp_forecast_id}', 'deleteMrpForecast');
  // });

  Route::controller(JobDescriptionController::class)->group(function () {
    Route::get('/job-descriptions', 'getJobDescription');
    Route::post('/job-descriptions', 'storeJobDescription');
    Route::get('/job-descriptions/{id}', 'showJobDescription');
    Route::post('/job-descriptions/{id}', 'updateJobDescription');
    Route::delete('/job-descriptions/{id}', 'deleteJobDescription');

    Route::post('/job-specifications', 'storeJobSpecification');
    Route::get('/job-specifications', 'getJobSpecification');
    Route::get('/job-specifications/{id}', 'showJobSpecification');
    Route::delete('/job-specifications/{id}', 'deleteJobSpecification');

    Route::post('/sops', 'storeSop');
    Route::get('/sops', 'getSop');
    Route::get('get_sop','getAllSop');
    Route::get('/sop-jd/{jdSopId}', 'showSop');
    Route::delete('/sop-jd/{jdSopId}', 'deleteJdSopById');
  });
});
