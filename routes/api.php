<?php
// namespace App\Http\Controllers\parent_app;
// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Auth::routes();


// Admin routes
Route::get('/', 'HomeController@index')->name('');
Route::get('/get_dashboard_data', 'HomeController@get_dashboard')->name('');
Route::get('/test', 'Test@index')->name('');
Route::get('/get_drivers_data', 'DriverController@get_data')->name('getDriversData');
Route::get('/get_parents_data', 'ParentsController@get_data')->name('getParentsData');
Route::get('/get_all_busess_routes/{id}', 'ParentsController@get_all_busess_routes')->name('getAllBusessRoutes');
Route::get('/get_busess_for_childs', 'ParentsController@get_busess_for_childs');
Route::get('/get_busess_for_childs_edit/{id}', 'ParentsController@get_busess_for_childs_edit');
Route::post('/get_bus_seat', 'ParentsController@get_bus_seat');
Route::post('/update_child_data', 'ParentsController@update_child_data');
Route::post('/save_seat', 'ParentsController@save_seat');
Route::post('/get_driver_route', 'ParentsController@get_driver_route');

Route::get('/get_busses_data', 'BusController@get_data')->name('getBussesData');
Route::post('/update_parent_data', 'ParentsController@update_parent_data')->name('updateParentsData');
Route::post('/update_driver_data', 'DriverController@update_driver_data')->name('updateDriversData');
Route::post('/delete_driver', 'DriverController@delete_driver')->name('deleteDriver');
Route::post('/update_bus_data', 'BusController@update_bus_data')->name('updateBussesData');
Route::post('/delete_bus', 'BusController@delete_bus')->name('deleteBus');
Route::post('/assign_bus', 'DriverController@assign_bus')->name('AssignBus');
Route::post('/assign_bus_to_drivers', 'DriverController@assign_bus_to_drivers')->name('AssignBusToDriver');
Route::get('/get_routes_data', 'RouteController@get_data')->name('GetRoutes');
Route::post('/update_route_data', 'RouteController@update_route_data')->name('UpdateRoute');
Route::post('/delete_route', 'RouteController@delete_route')->name('deleteRouter');
Route::post('/save_stations', 'RouteController@save_stations')->name('saveStations');
Route::post('/update_stations_list', 'RouteController@update_stations_list');
Route::post('/driver_stations', 'DriverController@driver_stations');
Route::post('/startTrip', 'NotificationController@startTripNotification')->name('');
Route::get('/get_all_notifications', 'NotificationController@get_all_notifications')->name('');
Route::get('/clear_notification', 'NotificationController@clear_notification')->name('');
Route::post('/read_notification', 'NotificationController@read_notification')->name('');
Route::get('/get_all_notifications_with_status', 'NotificationController@get_all_notifications_with_status')->name('');
Route::post('/login_make', 'LoginController@login')->name('Login');
Route::get('/get_session', 'LoginController@get_session')->name('');
Route::post('/get_admin_info', 'HomeController@get_admin_info')->name('');
Route::post('/update_admin', 'HomeController@update_admin')->name('');
Route::get('/get_conductors', 'ConductorController@get_data')->name('getConductorsData');
Route::post('/update_conductor_data', 'ConductorController@update_conductor_data')->name('updateconductordata');
Route::post('/delete_conductor', 'ConductorController@delete_conductor');




// for testing firebase cloud messaging

Route::get('/send_Alert_to_app', 'NotificationController@send_Alert_to_app');


//////////////


//parent App routes change ahmed
Route::post('/parent_login', 'Parents\MainController@parent_login');
Route::post('/myChilds', 'Parents\MainController@myChilds');
Route::post('/childDetail', 'Parents\MainController@childDetail');
Route::post('/sendEmail', 'Parents\MainController@sendEmail');
Route::post('/parent_detail', 'Parents\MainController@parent_detail');
Route::post('/childs_drivers', 'Parents\MainController@get_childs_drivers');
Route::post('/drivers_location', 'Parents\MainController@drivers_location');
Route::post('/childlistLocation', 'Parents\MainController@childlistLocation');
Route::post('/child_all_detail', 'Parents\MainController@child_all_detail');
Route::post('/child_bus_stops', 'Parents\MainController@child_bus_stops');
Route::post('/child_location', 'Parents\MainController@child_location');


//driver App routes
Route::post('/driver_login', 'Drivers\MainController@login');
Route::post('/driver_detail', 'Drivers\MainController@driver_detail');
Route::post('/driver_detail_byid', 'Drivers\MainController@driver_detail');
Route::post('/update_driver_token_and_location', 'Drivers\MainController@update_driver_token_and_location');
Route::post('/update_messages', 'Drivers\MainController@update_messages');
Route::post('/get_messages', 'Drivers\MainController@get_messages');
Route::get('/message_detail', 'Drivers\MainController@message_detail');


// conductor app routes

Route::post('/conductor_login', 'Conductors\MainController@login');