<?php

use App\Http\Controllers\WEB\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for yfgour application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

// Route::get('/', function () {
//     // return view('welcome');
//     if (Auth::check()) {
//         return redirect()->route('staff');
//     } else {
//         return redirect()->route('login');
//     }
//     // return redirect()->route('staff');
// });
// Route::view('/login', 'auth.login')->name('login_form');
// Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route::view('/test', 'staff.index');

// #change middleware
// Route::middleware(['departments:role'])->group(function () {
//     Route::view('/roles', 'roles.index')->name('roles');
// });

// // staff
// Route::middleware(['departments:staff'])->group(function () {
//     Route::view('/staff', 'staff.index')->name('staff');
// });
// Route::middleware(['departments:staff.create'])->group(function () {
//     Route::view('/staff/create', 'staff.create')->name('staff.crate');
// });
// Route::middleware(['departments:staff.edit'])->group(function () {
//     Route::view('/staff/{id}/edit', 'staff.edit')->name('staff.edit');
// });

// Route::middleware(['departments:uom-conversion'])->group(function () {
//     Route::view('/uoms', 'item_uoms.uom_list')->name('uoms');
//     Route::view('/uom_conversions', 'item_uoms.index')->name('uom_conversions');
// });

// // item
// Route::middleware(['departments:item'])->group(function () {
//     Route::view('/items', 'items.index')->name('items');
//     Route::view('/items/create', 'items.create')->name('items.create');
//     Route::view('/items/{id}/edit', 'items.edit')->name('items.edit');
//     Route::view('/items/{id}/pricing_history', 'items.pricing_history')->name('items.pricing_history');

//     Route::view('/selling_extras', 'selling_extras.index')->name('selling_extras');
// });
// Route::middleware(['departments:item.detail'])->group(function () {
//     Route::view('/items/{id}/suppliers', 'items.item_suppliers')->name('items.item_suppliers');
// });
// Route::middleware(['departments:item-supplier.detail'])->group(function () {
//     Route::view('/items/{id}/suppliers/{supplierId}/brands', 'items.supplier_brands')->name('items.supplier_brands');
// });

// Route::middleware(['departments:brand'])->group(function () {
//     Route::view('/brands', 'brands.index')->name('brands');
// });

// Route::middleware(['departments:task'])->group(function () {
//     Route::view('/tasks', 'tasks.index')->name('tasks');
//     Route::view('/tasks/reports', 'tasks.report')->name('tasks.report');
// });
// Route::middleware(['departments:custom-task'])->group(function () {
//     Route::view('/custom_tasks', 'tasks.customtask')->name('custom_tasks');
// });
// Route::middleware(['departments:report-task'])->group(function () {
//     Route::view('/task_reports', 'tasks.tasks')->name('task_report');
// });

// Route::middleware(['departments:department'])->group(function () {
//     Route::view('/departments', 'departments.index')->name('departments');
// });

// Route::middleware(['departments:area.list'])->group(function () {
//     Route::view('/areas', 'areas.index')->name('areas');
// });

// Route::middleware(['departments:inventory'])->group(function () {
//     Route::view('/inventories', 'inventories.index')->name('inventories');
//     // Route::view('/inventories/{inventory_id}/ledger', 'inventories.inventory_ledger')->name('inventory.ledger');
// });

// Route::middleware(['departments:inventory-transfer'])->group(function () {
//     Route::view('/inventory_transfers', 'transfers.index')->name('transfers.index');
//     Route::view('/inventory_transfers_list', 'transfers.transfers_list')->name('transfers.transfers');
//     // Route::view('/inventory_receives_list', 'transfers.receives_list')->name('transfers.receives');
// });

// Route::middleware(['departments:used-defected-item'])->group(function () {
//     Route::view('/used_defected_items', 'used_defected_items.index')->name('used_defected_items.index');
// });
// Route::middleware(['departments:inventory-receive'])->group(function () {
//     Route::view('/inventory_receives_list', 'transfers.receives_list')->name('transfers.receives');
// });
// Route::middleware(['departments:inventory-confirmation'])->group(function () {});
// #changed middleware

// Route::middleware(['departments:complaint'])->group(function () {

//     Route::view('/complaints', 'complains.index')->name('complains');
// });
// Route::middleware(['departments:service'])->group(function () {
//     Route::view('/services', 'services.index')->name('services');
// });
// Route::middleware(['departments:uom-conversion'])->group(function () {
//     Route::view('/uom_conversions', 'item_uoms.index')->name('uom_conversions');
// });

// Route::middleware(['departments:menu'])->group(function () {
//     Route::view('/menus', 'menus.index')->name('menus');
// });
// Route::middleware(['departments:menu.create'])->group(function () {
//     Route::view('/menus/create', 'menus.create')->name('menus.create');
// });
// Route::middleware(['departments:menu.edit'])->group(function () {
//     Route::view('/menus/{id}/edit', 'menus.edit')->name('menus.edit');
// });
// Route::middleware(['departments:menu-category'])->group(function () {
//     Route::view('/menu_categories', 'menu_categories.index')->name('menu_categories');
// });
// // Route::middleware(['departments:mrp'])->group(function () {

// Route::view('/mrp', 'MRP.index')->name('MRP');
// Route::view('/mrp/create', 'MRP.create')->name('MRP.create');
// Route::view('/mrp/{id}/edit', 'MRP.edit');
// // });
// Route::middleware(['departments:menu-area'])->group(function () {
//     Route::view('/menu_area', 'menu_area.index')->name('menu_area.index');
// });
// Route::middleware(['departments:table'])->group(function () {
//     Route::view('/tables', 'tables&rooms.table')->name('table');
// });
// Route::middleware(['departments:room'])->group(function () {
//     Route::view('/rooms', 'tables&rooms.index')->name('room');
// });

// Route::middleware(['departments:item-usage-forecast'])->group(function () {
//     Route::view('/item_usage_forecasts', 'item_usage_forecastings.index')->name('item_usage_forecasts');
//     Route::view('/item_usage_forecasts_by_month', 'item_usage_forecastings.listbyMonth')->name('item_usage_forecasts_by_month');
//     Route::view('/item_usage_forecasts/{month}', 'item_usage_forecastings.listbyMonthWithDepartment')->name('item_usage_forecasts_by_month_with_department');
//     Route::view('/item_usage_forecasts_with_month/{month}/department/{department}', 'item_usage_forecastings.listByMonthAndDepartment')->name('item_usage_forecasts_by_month_and_department');

//     Route::view('/item_usage_forecasts/{forecastId}/detail', 'item_usage_forecastings.detail')->name('item_usage_forecasts.detail');
//     Route::view('/create_item_usage_forecasts', 'item_usage_forecastings.create');
// });

// // purchase order
// Route::middleware(['departments:purchase-order'])->group(function () {
//     Route::view('/purchase_orders', 'purchase_orders.index')->name('purchase_orders');
//     Route::view('/purchase_orders/{poId}/buy', 'purchase_orders.buy')->name('purchase_orders.buy');
// });
// Route::middleware(['departments:purchase-order.create'])->group(function () {
//     Route::view('/purchase_orders/create', 'purchase_orders.create')->name('purchase_orders.create');
// });
// Route::middleware(['departments:purchase-order.edit'])->group(function () {
//     Route::view('/purchase_orders/{id}/edit', 'purchase_orders.edit');
// });
// Route::middleware(['departments:purchase-order.confirm'])->group(function () {
//     Route::view('/purchase_orders/{poId}/confirm', 'purchase_orders.confirm')->name('purchase_orders.confirm');
// });

// Route::middleware(['departments:confirm-purchase-order-item.list'])->group(function () {
//     Route::view('/confirm_purchase_order_items', 'purchase_orders.confirm_poitems')->name('purchase_orders.confirm_poitems');
// });
// Route::middleware(['departments:purchase-order'])->group(function () {
//     Route::view('/purchase_order_left_items', 'purchase_orders.left_items_index')->name('purchase_orders.left_items_index');
// });
// Route::middleware(['departments:purchase-order'])->group(function () {
//     Route::view('/purchase_order_left_items/{poId}', 'purchase_orders.left_items_detail')->name('purchase_orders.left_items_detail');
// });

// // supplier
// Route::middleware(['departments:supplier'])->group(function () {
//     Route::view('/suppliers', 'supplier.index')->name('suppliers.index');
// });
// Route::middleware(['departments:supplier.create'])->group(function () {
//     Route::view('/suppliers/create', 'supplier.create')->name('suppliers.create');
// });
// Route::middleware(['departments:supplier.edit'])->group(function () {
//     Route::view('/suppliers/{id}/edit', 'supplier.edit')->name('suppliers.edit');
//     // Route::view('/suppliers/{id}/detail', 'supplier.detail')->name('suppliers.detail');
// });
// Route::middleware(['departments:cashbook'])->group(function () {
//     Route::view('/cashbook', 'cashbook.index')->name('cashbook');
//     Route::view('/cashbook/office', 'cashbook.cash_office')->name('office_cash');
//     Route::view('/cashbook/owner', 'cashbook.cash_owner')->name('owner_cash');
//     Route::view('/cashbook/service', 'cashbook.cash_service')->name('service_cash');
//     Route::view('/cashbook/advance', 'cashbook.cash_advance')->name('advance_cash');
//     Route::view('/cashbook/agm', 'cashbook.cash_agm')->name('agm_cash');
//     Route::view('/cashbook/gm', 'cashbook.cash_gm')->name('gm_cash');
//     Route::view('/cashbook/ktv_project', 'cashbook.cash_ktv_project')->name('ktv_project_cash');

//     Route::view('/bankbook/kbz_special_md_gm', 'cashbook.bank_kbz_special_md_gm')->name('kbz_special_bank');
//     Route::view('/bankbook/kbz_old_gm', 'cashbook.bank_kbz_old_gm')->name('kbz_old_gm_bank');
//     Route::view('/bankbook/kpay', 'cashbook.bank_kpay')->name('kpay_bank');
// });

// Route::middleware(['departments:financial-transaction'])->group(function () {
//     Route::view('/financial_transaction', 'financial_transaction.index')->name('financial_transactions');
// });
// // chart of account (coa)
// Route::middleware(['departments:account'])->group(function () {
//     Route::view('/accounting', 'accounting.index')->name('accounting');
// });


// Route::middleware(['departments:fix-asset'])->group(function () {
//     Route::view('/fixed_assets', 'fixed_assets.index')->name('fixed_assets.index');
// });
// Route::middleware(['departments:asset'])->group(function () {
//     Route::view('/assets/create', 'fixed_assets.assets')->name('fixed_assets.assets');
//     Route::view('/assets', 'fixed_assets.asset_list')->name('assetList');
// });

// Route::middleware(['departments:asset-item'])->group(function () {
//     Route::view('/asset_items', 'fixed_assets.asset_item_list')->name('assetItemList');
// });
// Route::middleware(['departments:asset-item.create'])->group(function () {
//     Route::view('/asset_items/create', 'fixed_assets.asset_items')->name('fixed_assets.asset_items');
// });
// Route::middleware(['departments:ap-balance'])->group(function () {
//     Route::view('/account_payables', 'AP.index')->name('AP.index');
//     Route::view('/account_payables/suppliers/{supplierId}/transactions', 'AP.history')->name('AP.history');
// });

// // Route::middleware(['departments:all_departments'])->group(function () {
// //     Route::view('/inventory_transfers', 'transfers.index')->name('transfers.index');
// //     Route::view('/inventory_transfers_list', 'transfers.transfers_list')->name('transfers.transfers');
// //     Route::view('/inventory_receives_list', 'transfers.receives_list')->name('transfers.receives');
// // });

// //pos
// Route::group(['prefix' => 'pos'], function () {
//     Route::view('/login', 'pos.auth.index')->name('pos.login');
//     // Route::middleware(['departments:pos'])->group(function () {
//     Route::view('/home', 'pos.home.home')->name('pos.index');
//     Route::view('/home_new', 'pos.home.home')->name('pos.home');
//     Route::view('/customer', 'pos.customers.index')->name('pos.customers');
//     Route::view('/customer/create', 'pos.customers.create')->name('pos.customers.create');
//     Route::view('/ar', 'pos.AR.index')->name('pos.ar');
//     Route::view('/cashbook', 'pos.cashbook.index')->name('pos.cashbooks');
//     Route::view('/cashbook/detail', 'pos.cashbook.detail')->name('pos.cashbooks.detail');
//     Route::view('/invoices', 'pos.invoices.index')->name('pos.invoices');
//     Route::view('/invoices/detail', 'pos.invoices.detail')->name('pos.invoices.detail');
//     Route::view('/customer_deposit', 'pos.customer_deposit.index')->name('pos.customer_deposit');
//     Route::view('/selling_areas', 'pos.areas.index')->name('pos.areas');
//     Route::view('/sale_report', 'pos.sale_reports.index')->name('pos.sale_reports');
//     Route::view('/pos_order/{id}', 'pos.home.order')->name('pos.home.order');
//     Route::view('/cashbook_history', 'pos.cashbook.history')->name('pos.cashbook.history');
//     // });
// });

// Route::middleware(['departments:pos-home'])->group(function () {
//     Route::view('/home', 'pos.home.home')->name('pos.index');
// });
// Route::middleware(['departments:pos-customer'])->group(function () {
//     Route::view('/customer', 'pos.customers.index')->name('pos.customers');
// });
// // Route::middleware(['departments:pos-customer'])->group(function () {
// //     Route::view('/customer/create', 'pos.customers.create')->name('pos.customers.create');
// // });
// Route::middleware(['departments:pos-customer-deposit'])->group(function () {
//     Route::view('/customer_deposit', 'pos.customer_deposit.index')->name('pos.customer_deposit');
// });
// Route::middleware(['departments:pos-ar'])->group(function () {
//     Route::view('/ar', 'pos.AR.index')->name('pos.ar');
// });
// Route::middleware(['departments:pos-cashbook'])->group(function () {
//     Route::view('/cashbook', 'pos.cashbook.index')->name('pos.cashbooks');
// });
// // Route::middleware(['departments:pos-booking'])->group(function () {
// Route::view('/booking', 'pos.booking.index');
// Route::view('/booking/create', 'pos.booking.create');
// // });
// Route::middleware(['departments:pos-food-order'])->group(function () {
//     Route::view('/food_orders', 'pos.menu_order.index');
//     Route::view('/food_orders/create', 'pos.menu_order.create');
// });
// Route::middleware(['departments:pos-ar'])->group(function () {
//     Route::view('/ar', 'pos.AR.index')->name('pos.ar');
// });



// Route::middleware(['departments:inventory-stock'])->group(function () {
//     Route::view('/inventory_stocks', 'inventory_stocks.index')->name('inventory_stocks.index');
//     Route::view('/inventory_stocks/opening', 'inventory_stocks.inventory_opening')->name('inventory_stocks.inventory_opening');
// });

// Route::middleware(['departments:package'])->group(function () {
//     Route::view('/packages', 'packages.index')->name('packages.index');
//     Route::view('/packages/create', 'packages.create')->name('packages.create');
//     Route::view('/packages/{id}/edit', 'packages.edit')->name('packages.edit');
// });

// Route::middleware(['departments:menu-service-discount'])->group(function () {
//     Route::view('/menu_service_discounts', 'menu_and_service_discount.index')->name('menu_service_discount.index');
// });
// Route::middleware(['departments:room-discount'])->group(function () {
//     Route::view('/room_discounts', 'room_discount.index')->name('room_discount.index');
// });

// //test

// Route::middleware(['departments:customer'])->group(function () {
//     Route::view('/crm/customers', 'CRM.customers.index')->name('crm.customers.index');
// });

// Route::middleware(['departments:customer-level-discount'])->group(function () {
//     Route::view('/crm/level_discounts', 'CRM.level_discounts.index')->name('crm.level_discounts.index');
// });

// Route::middleware(['departments:customer-level-discount'])->group(function () {
//     Route::view('/crm/customers/birthdays', 'CRM.customers.birthdays')->name('crm.customers.birthdays');
// });
// Route::view('/crm/customers/{id}/detail', 'CRM.customers.detail')->name('crm.customers.detail');
// Route::view('/crm/birthday_promotions', 'CRM.birthday_discounts.index')->name('crm.birthday_discounts.index');



// Route::view('/delivery_charges', 'delivery_charges.index');

// Route::middleware(['departments:journal'])->group(function () {
//     Route::view('/journals', 'journals.index')->name('journal');
// });

// Route::middleware(['departments:staff-balance'])->group(function () {
//     Route::view('/advanced', 'advanced.index')->name('advance');
// });

// Route::view('/advanced/{id}/detail', 'advanced.detail');

// Route::middleware(['departments:advance'])->group(
//     function () {
//         Route::view('/advances', 'advances.index')->name('advances');
//     }
// );
// Route::middleware(['departments:advance.detail'])->group(function () {
//     Route::view('/advances/{id}/detail', 'advances.detail')->name('advances');
// });
// // Route::middleware(['departments:prepaid'])->group(function () {
// Route::view('/prepaid', 'prepaid.index')->name('prepaid');
// // });

// Route::middleware(['departments:ar'])->group(function () {
//     Route::view('/account_receivable', 'AR.index')->name('account_receivable');
// });

// Route::view('/account_receivable/{id}/detail', 'AR.detail');
// Route::middleware(['departments:cash-flow'])->group(function () {
//     Route::view('/cash_flow_statement', 'cash_flow_statement.index');
// });
// Route::middleware(['departments:indirect-cash-flow'])->group(function () {
//     Route::view('/indirect_cashflow_statement', 'cash_flow_statement.indirect_cashflow_statement')->name('indirect_cashflow_statement');
// });
// Route::middleware(['departments:working-capital'])->group(function () {
//     Route::view('/working_capital', 'working_capital.index');
// });
// Route::view('/profit_and_loss', 'profit_and_loss.index');
// Route::view('/trial_balance', 'trial_balance.index');
// Route::middleware(['departments:ap-balance'])->group(function () {
//     Route::view('/ap_balances', 'ap_balances.index');
// });
// Route::middleware(['departments:creditor-balance'])->group(function () {
//     Route::view('/creditor_balances', 'creditor_balances.index');
// });


// // Route::middleware(['departments:asset-depreciation-balance'])->group(function () {
// Route::view('/asset_depreciation_balance_list', 'asset_depreciation_balance.index')->name('asset_list');
// Route::view('/fix_asset_depreciation', 'asset_depreciation_balance.fix_asset_depreciation')->name('fix_asset_depreciation');
// // });


// // test
// Route::middleware(['departments:skill'])->group(function () {
//     Route::view('/skill', 'skill.index')->name('skill');
// });

// Route::middleware(['departments:cooking-place'])->group(function () {
//     Route::view('/cooking_places', 'cookingPlace.index')->name('cookingPlace');
// });
// Route::middleware(['departments:cooking-place.create'])->group(function () {
//     Route::view('/cooking_places/create', 'cookingPlace.create')->name('cookingPlaceCreate');
// });
// Route::middleware(['departments:cooking-place.edit'])->group(function () {
//     Route::view('/cooking_places/{id}/edit', 'cookingPlace.edit')->name('cookingPlaceEdit');
// });


// // duty
// Route::middleware(['departments:duty'])->group(function () {
//     Route::view('/duty', 'duty.index')->name('duty');
// });
// Route::middleware(['departments:duty.create'])->group(function () {
//     Route::view('/duty/create', 'duty.create')->name('dutyCreate');
// });
// Route::middleware(['departments:duty.edit'])->group(function () {
//     Route::view('/duty/{id}/edit', 'duty.edit')->name('dutyEdit');
// });

// Route::middleware(['departments:sale-target-position'])->group(function () {
//     Route::view('/sale_target_position', 'sale_target_position.index')->name('sale_target_position');
//     Route::view('/sale_target_position/create', 'sale_target_position.create')->name('sale_target_position/create');
//     Route::view('/sale_target_position/{id}/edit', 'sale_target_position.edit')->name('sale_target_position/edit');
// });
// Route::middleware(['departments:sale-target-menu'])->group(function () {
//     Route::view('/sale_target_menu', 'sale_target_menu.index')->name('sale_target_menu');
//     Route::view('/sale_target_menu/create', 'sale_target_menu.create')->name('sale_target_menu/create');
//     Route::view('/sale_target_menu/{id}/edit', 'sale_target_menu.edit')->name('sale_target_menu/edit');
// });


// Route::middleware(['departments:menu-sale-report'])->group(function () {
//     Route::view('/menu_sale_report', 'menu_sale_report.index')->name('menu_sale_report.index');
// });
// Route::middleware(['departments:menu-costing'])->group(function () {
//     Route::view('/menu_costing', 'menu_costing.index')->name('menu_costing.index');
// });

// Route::view('/pos_order_items', 'pos.orderItem.index');


// Route::view('/menu_position', 'menu_position.index')->name('menu_position');
// Route::view('/menu_position/create', 'menu_position.create')->name('menu_position.create');

// // accessory
// Route::middleware(['departments:accessory'])->group(function () {
//     Route::view('/accessories', 'accessories.index');
// });
// Route::middleware(['departments:accessory.store'])->group(function () {
//     Route::view('/accessories/create', 'accessories.create');
// });
// Route::middleware(['departments:accessory.show'])->group(function () {
//     Route::view('/accessories/{id}/edit', 'accessories.edit');
// });



// // okr duty
// Route::middleware(['departments:okr-duty'])->group(function () {
//     Route::view('/okr_duty', 'okr_duty.index')->name('okr_duty');
// });
// // Route::middleware(['departments:okr-duty.create'])->group(function () {
// Route::view('/okr_duty/create', 'okr_duty.create')->name('okr_duty.create');
// // });
// Route::middleware(['departments:okr-duty.edit'])->group(function () {
//     Route::view('/okr_duty/{id}/edit', 'okr_duty.edit');
// });

// Route::middleware(['departments:okr-dashboard'])->group(function () {
//     Route::view('/okr_dashboard', 'okr_dashboard.index')->name('okr_dashboard');
// });

// // okr
// Route::middleware(['departments:okr'])->group(function () {
//     Route::view('/OKR', 'OKR.index')->name('OKR');
// });
// Route::middleware(['departments:okr.create'])->group(function () {
//     Route::view('/OKR/create', 'OKR.create')->name('OKR.create');
// });
// Route::middleware(['departments:okr.edit'])->group(function () {
//     Route::view('/OKR/{id}/edit', 'OKR.edit');
// });

// // menu forecasting
// Route::middleware(['departments:menu-forecasting'])->group(function () {
//     Route::view('/menu_forecasting', 'menu_forecastings.index')->name('menu_forecasting');
// });
// Route::middleware(['departments:menu-forecasting.create'])->group(function () {
//     Route::view('/menu_forecasting/create', 'menu_forecastings.create')->name('menu_forecasting.create');
// });
// Route::middleware(['departments:menu-forecasting.edit'])->group(function () {
//     Route::view('/menu_forecasting/{id}/edit', 'menu_forecastings.edit')->name('menu_forecasting.edit');
// });

// // ktv forecasting
// Route::middleware(['departments:ktv-forecasting'])->group(function () {
//     Route::view('/ktv_forecasting', 'ktv_forecastings.index')->name('ktv_forecasting');
// });
// Route::middleware(['departments:ktv-forecasting.create'])->group(function () {
//     Route::view('/ktv_forecasting/create', 'ktv_forecastings.create')->name('ktv_forecasting.create');
// });
// Route::middleware(['departments:ktv-forecasting.edit'])->group(function () {
//     Route::view('/ktv_forecasting/{id}/edit', 'ktv_forecastings.edit')->name('ktv_forecasting.edit');
// });




// // ktv product tree
// // Route::middleware(['departments:ktv-product-tree'])->group(function () {
// Route::view('/ktv_product_tree', 'ktv_product_tree.index')->name('ktv_product_tree');
// // });
// // Route::middleware(['departments:ktv-product-tree.create'])->group(function () {
// Route::view('/ktv_product_tree/create', 'ktv_product_tree.create')->name('ktv_product_tree.create');
// // });
// // Route::middleware(['departments:ktv-product-tree.edit'])->group(function () {
// Route::view('/ktv_product_tree/{id}/edit', 'ktv_product_tree.edit');
// // });


// Route::middleware(['departments:po-order'])->group(function () {
//     Route::view('/procurement_order_items', 'procurement_order_items.index')->name('procurement_order_items');
// });
// Route::middleware(['departments:arrival-item'])->group(function () {
//     Route::view('/arrival_items', 'procurement_order_arrival.index')->name('arrival_items');
// });
// Route::middleware(['departments:po-order-invoice'])->group(function () {
//     Route::view('/purchase_order_invoices', 'purchase_order_invoices.index')->name('purchase_order_invoices');
// });
// Route::middleware(['departments:po-order-invoice.paid'])->group(function () {
//     Route::view('/purchase_order_invoices/{id}/confirm', 'purchase_order_invoices.confirm')->name('purchase_order_invoices.confirm');
// });



// //test
// Route::view('/lead_time', 'lead_time.index')->name('lead_time');
// Route::view('/suppliers/{id}/lead_times', 'supplier.supplier_leadtime')->name('supplier.lead_times');

// Route::view('/creditor', 'creditor.index')->name('creditor');
// Route::view('/creditor/suppliers/{creditorId}/transactions', 'creditor.history')->name('creditor.history');

// // Route::middleware(['departments:gps'])->group(function () {
// Route::view('/gps', 'GPS.index')->name('gps');
// // });
// Route::middleware(['departments:check-in'])->group(function () {
//     Route::view('/check_in', 'check_in.index')->name('check_in');
// });
// Route::middleware(['departments:time-shift'])->group(function () {
//     Route::view('/time_shift', 'time_shift.index')->name('time_shift');
// });
// //hr
// // Route::middleware(['departments:hr'])->group(function () {

// Route::middleware(['departments:contact'])->group(function () {
//     Route::view('/contact', 'contact.index')->name('contact');
// });

// // meeting
// Route::middleware(['departments:meeting'])->group(function () {
//     Route::view('/meeting', 'meeting.index')->name('meeting');
// });
// Route::middleware(['departments:meeting.create'])->group(function () {
//     Route::view('/meeting/create', 'meeting.create')->name('meeting.create');
// });
// Route::view('/meeting/{id}/operation', 'meeting.operation_meeting');
// Route::view('/meeting/{id}/detail', 'meeting.detail');
// Route::middleware(['departments:meeting.edit'])->group(function () {
//     Route::view('/meeting/{id}/edit', 'meeting.edit');
// });
// Route::view('/project', 'project.index');
// Route::view('/project/{id}/instruction', 'project.project_instruction');

// // training
// Route::middleware(['departments:training'])->group(function () {
//     Route::view('/training', 'training.index')->name('training');
// });
// Route::middleware(['departments:training.create'])->group(function () {
//     Route::view('/training/create', 'training.create')->name('training.create');
// });
// Route::middleware(['departments:training.edit'])->group(function () {
//     Route::view('/training/{id}/edit', 'training.edit');
// });

// // training
// Route::middleware(['departments:org-new'])->group(function () {
//     Route::view('/org_news', 'org_news.index')->name('org_news');
// });
// Route::middleware(['departments:org-new.create'])->group(function () {
//     Route::view('/org_news/create', 'org_news.create')->name('org_news.create');
// });
// Route::middleware(['departments:org-new.edit'])->group(function () {
//     Route::view('/org_news/{id}/edit', 'org_news.edit');
// });

// // warning
// Route::middleware(['departments:warning'])->group(function () {
//     Route::view('/warning', 'warning.index')->name('warning');
// });
// Route::middleware(['departments:warning.create'])->group(function () {
//     Route::view('/warning/create', 'warning.create')->name('warning.create');
// });
// Route::middleware(['departments:warning.edit'])->group(function () {
//     Route::view('/warning/{id}/edit', 'warning.edit');
// });

// Route::middleware(['departments:off-day'])->group(function () {
//     Route::view('/off_day', 'off_day.index')->name('off_day.index');
//     Route::view('/off_day_requests', 'off_day_requests.index')->name('off_day_requests.index');
// });

// // leave allowance
// Route::middleware(['departments:leave-allowance'])->group(function () {
//     Route::view('/leave_allowance', 'leave_allowance.index')->name('leave_allowance.index');
// });
// Route::middleware(['departments:leave-allowance.create'])->group(function () {
//     Route::view('/leave_allowance/create', 'leave_allowance.create')->name('leave_allowance.create');
// });

// Route::middleware(['departments:leave'])->group(function () {
//     Route::view('/leave', 'leave.index')->name('leave.index');
// });

// Route::middleware(['departments:exit-pass'])->group(function () {
//     Route::view('/exit_pass', 'exit_pass.index')->name('exit_pass.index');
// });

// Route::middleware(['departments:overtime-fee'])->group(function () {
//     Route::view('/overtime_fees', 'overtime_fees.index')->name('overtime_fees.index');
// });

// Route::middleware(['departments:overtime-confirmation'])->group(function () {
//     Route::view('/overtime_confirmation', 'overtime_confirmation.index')->name('overtime_confirmation.index');
// });

// // warning
// Route::middleware(['departments:salary-setup'])->group(function () {
//     Route::view('/salary_setup', 'salary_setup.index')->name('salary_setup.index');
// });
// Route::middleware(['departments:salary-setup.create'])->group(function () {
//     Route::view('/salary_setup/create', 'salary_setup.create')->name('salary_setup.create');
// });
// Route::middleware(['departments:salary-setup.edit'])->group(function () {
//     Route::view('/salary_setup/{id}/edit', 'salary_setup.edit');
// });

// Route::middleware(['departments:salary'])->group(function () {
//     Route::view('/salary', 'salary.index')->name('salary.index');
// });

// Route::middleware(['departments:allowance'])->group(function () {
//     Route::view('/allowance', 'allowance.index')->name('allowance.index');
// });
// Route::view('/holiday', 'holiday.index')->name('holiday.index');

// // salary batch
// Route::middleware(['departments:salary-batch'])->group(function () {
//     Route::view('/salary_batch', 'salary_batch.index')->name('salary_batch.index');
// });
// Route::middleware(['departments:salary-batch.create'])->group(function () {
//     Route::view('/salary_batch/create', 'salary_batch.create')->name('salary_batch.create');
// });
// Route::middleware(['departments:salary-batch.edit'])->group(function () {
//     Route::view('/salary_batch/{id}/edit', 'salary_batch.edit');
// });

// Route::middleware(['departments:salary-calculate'])->group(function () {
//     Route::view('/salary_calculate', 'salary_calculate.index')->name('salary_calculate.index');
// });

// Route::middleware(['departments:resignation-categories'])->group(function () {
//     Route::view('/pay_slip', 'salary_calculate.pay_slip')->name('salary_calculate.pay_slip');
// });

// Route::view('/resignation_categories', 'resignation_categories.index')->name('resignation_categories.index');
// Route::view('/resignations', 'resignations.index')->name('resignations.index');


// Route::view('/cv/form', 'CV.form')->name('CV.form');
// Route::view('/cv', 'CV.index')->name('CV.index')->middleware('departments:cv');
// Route::view('/cv/{id}/detail', 'CV.detail');
// Route::view('/cv/{id}/ocf', 'CV.ocf');
// Route::view('/exam', 'exams.index')->name('exams.index')->middleware('departments:exam');
// Route::view('/exam/create', 'exams.create')->name('exams.create');
// Route::view('/exam/{id}/edit', 'exams.edit');
// Route::view('/exam/{id}/assessment/{assessId}', 'exams.assessment')->name('exams.assessment');

// Route::view('/interviews', 'interviews.index')->name('interviews.index')->middleware('departments:interview');
// Route::view('/interviews/{id}/create/{cvId}/cv', 'interviews.create')->name('interviews.create');
// // Route::view('/interviews/create', 'interviews.create')->name('interviews.create');
// Route::view('/interview/result', 'interviews.result')->name('interviews.result')->middleware('departments:interview-result');
// Route::view('/locations', 'locations.index')->name('locations.index')->middleware('departments:location');
// Route::view('/location/{id}/floor/{floorId}/detail', 'locations.detail');


// Route::middleware(['departments:pay-slip'])->group(function () {
//     Route::view('/resignation_categories', 'resignation_categories.index')->name('resignation_categories.index');
// });

// Route::middleware(['departments:resignation'])->group(function () {
//     Route::view('/resignations', 'resignations.index')->name('resignations.index');
// });


// //commented for duplicate routes

// // Route::view('/exams', 'exams.index');
// // Route::view('/exam/create', 'exams.create')->name('exams.create');
// // Route::view('/exam/{id}/edit', 'exams.edit');
// // Route::view('/interviews', 'interviews.index');
// // Route::view('/interviews/{id}/create/{cvId}/cv', 'interviews.create')->name('interviews.create');
// // Route::view('/interviews/create', 'interviews.create')->name('interviews.create');
// // Route::view('/interview/result', 'interviews.result');
// // Route::view('/locations', 'locations.index');
// // Route::view('/location/{id}/floor/{floorId}/detail', 'locations.detail');
// //end duplicate routes


// Route::middleware(['departments:pay-slip'])->group(function () {
//     Route::view('/resignation_categories', 'resignation_categories.index')->name('resignation_categories.index');
// });

// Route::middleware(['departments:resignation'])->group(function () {
//     Route::view('/resignations', 'resignations.index')->name('resignations.index');
// });

// Route::middleware(['departments:event'])->group(function () {
//     Route::view('/events', 'event.index')->name('event.index');
// });



// Route::middleware(['departments:jd'])->group(function () {
//     Route::view('/JD', 'job_description.index')->name('job_description.index');
// });
// // Route::middleware(['departments:js'])->group(function () {
// Route::view('/JS', 'job_specifications.index')->name('job_specifications.index');
// // });
// // Route::middleware(['departments:js.create'])->group(function () {
// Route::view('/JS/create', 'job_specifications.create')->name('job_specifications.create');
// // });
// // Route::middleware(['departments:js.edit'])->group(function () {
// Route::view('/JS/{id}/edit', 'job_specifications.edit')->name('job_specifications.edit');
// // });
// Route::middleware(['departments:sop'])->group(function () {
//     Route::view('/SOP', 'SOP.index')->name('SOP.index');
// });
// Route::middleware(['departments:sop.create'])->group(function () {
//     Route::view('/SOP/create', 'SOP.create')->name('SOP.create');
// });
// Route::middleware(['departments:cv'])->group(function () {
//     Route::view('/cv', 'CV.index')->name('CV.index');
// });
// Route::middleware(['departments:shift-assignment'])->group(function () {
//     Route::view('/shift_assignment', 'shift_assignment.index')->name('shift_assignment.index');
// });
// Route::middleware(['departments:shift-assignment.create'])->group(function () {
//     Route::view('/shift_assignment/create', 'shift_assignment.create')->name('shift_assignment.create');
// });
// Route::middleware(['departments:benefit'])->group(function () {
//     Route::view('/benefits', 'benefits.index')->name('benefits.index');
// });
// Route::middleware(['departments:benefit_request'])->group(function () {
//     Route::view('/benefits_request', 'benefits.request');
// });


// Route::view('/cv/form', 'CV.form')->name('CV.form');
// Route::view('/cv/{id}/detail', 'CV.detail');
// Route::view('/okr_assign', 'okr_assign.index')->name('okr_assign.index');
// Route::view('/okr_assign/create', 'okr_assign.create')->name('okr_assign.create');
// Route::view('/okr_assign/{id}/edit', 'okr_assign.edit');
// Route::view('/okr_assign/{id}/detail', 'okr_assign.detail');
// Route::view('/handbooks', 'handbooks.index')->name('handbooks.index');
// Route::view('/handbooks/create', 'handbooks.create')->name('handbooks.create');

// Route::view('/asset_assignment', 'asset_assignment.index')->name('asset_assignment.index');
// Route::view('/equipment_assignment', 'equipment_assignment.index')->name('equipment_assignment.index');
// Route::view('/equipment_assignment/create', 'equipment_assignment.create')->name('equipment_assignment.create');
// Route::view('/shift', 'shift.index')->name('shift.index');
// Route::view('/working_capital', 'working_capital.index')->name('working_capital.index');
// Route::view('/cashbook_history', 'cashbook.history')->name('cashbook.history');
// Route::view('/accruals', 'accruals.index')->name('accruals.index');
// Route::view('/accruals/detail/{id}', 'accruals.detail')->name('accruals.detail');
// Route::view('/loans', 'loans.index')->name('loans.index');
// Route::view('/loans/{id}/details', 'loans.details')->name('loans.details');
// Route::view('/sale_ledger', 'sale_ledgers.index')->name('sale_ledgers.index');
// Route::view('/sale_ledger_ktv', 'sale_ledgers.sale_ledger_ktv')->name('sale_ledgers.sale_ledger_ktv');
// Route::view('/sale_ledger_restaurant', 'sale_ledgers.sale_ledger_restaurant')->name('sale_ledgers.sale_ledger_restaurant');
// Route::view('/ktv_report', 'ktv_reports.index');
// Route::view('/report/catering/daily_area_sales', 'catering_reports.daily_area_sales_by_staff');
// Route::view('/report/catering/target_actual_menu_sales', 'catering_reports.target_actual_menu_sales');
// Route::view('/ktv_report', 'ktv_reports.chart');
// Route::view('/bar_report', 'bar_reports.chart');
// Route::view('/kitchen_report', 'kitchen_reports.chart');
// Route::view('/ktv_menu_sale', 'kitchen_menu_sale.ktv_menu_sale');
// Route::view('/sky_menu_sale', 'kitchen_menu_sale.sky_menu_sale');
// Route::view('/budget_accounts', 'budget_accounts.index');
// Route::view('/cashflow_report', 'cashflow_report.index');
// Route::view('/off_day_setting', 'off_day_setting.index');
// Route::view('/contract', 'contract.index');
// Route::view('/signed_contract', 'contract.signed_contracts');
// Route::view('/contract/create', 'contract.create');

// // =======
// // Route::middleware(['departments:event'])->group(function () {
// //     Route::view('/events', 'event.index')->name('event.index');
// // });



// // Route::view('/JD', 'job_description.index')->name('job_description.index');
// // Route::view('/JS', 'job_specifications.index')->name('job_specifications.index');
// // Route::view('/JS/create', 'job_specifications.create')->name('job_specifications.create');
// // Route::view('/SOP', 'SOP.index')->name('SOP.index');
// // Route::view('/SOP/create', 'SOP.create')->name('SOP.create');
// // Route::view('/okr_assign', 'okr_assign.index')->name('okr_assign.index');
// // Route::view('/okr_assign/create', 'okr_assign.create')->name('okr_assign.create');
// // Route::view('/okr_assign/{id}/edit', 'okr_assign.edit');
// // >>>>>>> origin/k/teaology-backend
// // });

// // =======
// // Route::view('/purchase_orders/{id}/edit', 'purchase_orders.edit');
// // Route::view('/meeting', 'meeting.index')->name('meeting');
// // Route::view('/meeting/create', 'meeting.create')->name('meeting.create');
// // Route::view('/meeting/{id}/edit', 'meeting.edit');
// // Route::view('/training', 'training.index')->name('training');
// // Route::view('/training/create', 'training.create')->name('training.create');
// // Route::view('/training/{id}/edit', 'training.edit');
// // Route::view('/org_news', 'org_news.index')->name('org_news');
// // Route::view('/org_news/create', 'org_news.create')->name('org_news.create');
// // Route::view('/org_news/{id}/edit', 'org_news.edit');
// // Route::view('/warning', 'warning.index')->name('warning');
// // Route::view('/warning/create', 'warning.create')->name('warning.create');
// // Route::view('/warning/{id}/edit', 'warning.edit');
// // Route::view('/purchase_order_invoices/{id}/confirm', 'purchase_order_invoices.confirm')->name('purchase_order_invoices.confirm');
// // Route::view('/menu_area', 'menu_area.index')->name('menu_area.index');
// // Route::view('/off_day', 'off_day.index')->name('off_day.index');
// // Route::view('/leave_allowance', 'leave_allowance.index')->name('leave_allowance.index');
// // Route::view('/leave_allowance/create', 'leave_allowance.create')->name('leave_allowance.create');
// // Route::view('/leave', 'leave.index')->name('leave.index');
// // Route::view('/exit_pass', 'exit_pass.index')->name('exit_pass.index');
// // Route::view('/holiday', 'holiday.index')->name('holiday.index');
// // Route::view('/overtime_fees', 'overtime_fees.index')->name('overtime_fees.index');
// // Route::view('/overtime_confirmation', 'overtime_confirmation.index')->name('overtime_confirmation.index');
// // Route::view('/salary_setup', 'salary_setup.index')->name('salary_setup.index');
// // Route::view('/salary_setup/create', 'salary_setup.create')->name('salary_setup.create');
// // Route::view('/salary_setup/{id}/edit', 'salary_setup.edit');
// // Route::view('/salary', 'salary.index')->name('salary.index');
// // Route::view('/allowance', 'allowance.index')->name('allowance.index');
// // Route::view('/salary_batch', 'salary_batch.index')->name('salary_batch.index');
// // Route::view('/salary_batch/create', 'salary_batch.create')->name('salary_batch.create');
// // Route::view('/salary_batch/{id}/edit', 'salary_batch.edit');
// // >>>>>>> origin/test_hr_leave
