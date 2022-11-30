<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

header_remove('X-Powered-By');
header_remove('Server');


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Register Index Route
Route::get('/', function (Request $request) {
    $token = $request->session()->get('token');
    $userrow = \App\Users::where('token', $token)->first();
    if (!$userrow) return redirect('/acloud.middleware');
    if ($userrow->username == 'admin') return redirect('/home/dashboard');
    return redirect('/acloud');
});
Route::get('/dashboard', fn () => redirect('/home/dashboard'));
Route::get('/getQrcode','IndexController@getQrcode');
// authentication routes, no middleware
Route::get('/home/dashboard/authentication', 'AuthenticationController@index');
Route::post('/home/api/authentication', 'AuthenticationController@authentication');

// acloud登录页面

Route::get('/code.png', 'AcloudController@code');
Route::get('acloud.middleware', fn () => view('acloud.login'));
Route::get('/acloud/freeoauth/1.0','AcloudController@freeoauth');
Route::group(['prefix' => 'acloud', 'middleware' => 'authentication'], function () {
    Route::get('/', 'AcloudController@index');
    Route::get('/logout.middleware', 'AcloudController@logout');
});

//Register Dashboard Route
Route::group(['prefix' => '/home', 'middleware' => 'authentication'], function () {
    Route::get('lyear_main.html', fn () => redirect('/home/dashboard/main'));
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', 'DashboardController@index');
        Route::get('/changepassword', 'AuthenticationController@changepassword');
        Route::get('/logout', 'DashboardController@logout');
        Route::get('/main', 'DashboardController@main');

        Route::get('/flavor', 'BasicController@Flavor');
        Route::get('/image', 'BasicController@Image');
        Route::get('/networktop', 'DashboardController@networktop');
        Route::get('/volume', 'BasicController@Volume');
        Route::get('/create_volume', fn () => view('dashboard.create_volume'));
        Route::get('/create_flavor', fn () => view('dashboard.create_flavor'));
        Route::get('/volume/{id}', 'BasicController@VolumeDetail');
        Route::get('/edit_volume', 'BasicController@edit_volume');
        Route::get('/resize_volume', 'BasicController@resize_volume');


        Route::get('/edit_flavor', 'BasicController@edit_flavor');
        Route::get('/network', 'BasicController@network');

        Route::get('/instance', 'BasicController@instance');
        Route::get('/create_instance', fn () => view('dashboard.create_instance'));
        Route::get('/edit_instance', fn () => view('dashboard.edit_instance'));

        Route::get('/console/{id}', 'InstanceController@Console');
        Route::get('/boot_instance', 'InstanceController@boot_instance');

        Route::get('/connect_volume', 'InstanceController@connect_volume');
        Route::get('/volume_instance_connect', 'InstanceController@volume_instance_connect');

        Route::get('/backup', 'BasicController@backup');
        Route::get('/backup_instance', 'InstanceController@backup_instance');

        Route::get('/restore_instance', 'InstanceController@restore_instance');


        Route::group(['prefix' => 'admin', 'middleware' => 'Admin'], function () {
            Route::get('/compute', 'AdminController@compute');
            Route::get('/create_compute', fn () => view('dashboard.admin.create_compute'));
            Route::get('/rule_compute', 'AdminController@rule_compute');
            Route::get('/edit_compute', 'AdminController@edit_compute');
            Route::get('/flavor', 'AdminController@Flavor');
            Route::get('/network', 'AdminController@network');
            Route::get('/create_network', fn () => view('dashboard.admin.create_network'));
            Route::get('/edit_network', 'AdminController@edit_network');
            Route::get('/rule_network', 'AdminController@rule_network');
            Route::get('/image', 'AdminController@image');
            Route::get('/create_image', fn () => view('dashboard.admin.create_image'));
            Route::get('/edit_image', 'AdminController@edit_image');
            Route::get('/rule_image', 'AdminController@rule_image');
            Route::get('/user', 'AdminController@user');
            Route::get('/group', 'AdminController@group');
            Route::get('/create_group', fn () => view('dashboard.admin.create_group'));
            Route::get('/create_user', fn () => view('dashboard.admin.create_user'));
            Route::get('/repwd_user', fn () => view('dashboard.admin.repwd_user'));
            Route::get('/remail_user', fn () => view('dashboard.admin.remail_user'));
            Route::get('/system', 'AdminController@System');
            Route::get('/edit_group', fn () => view('dashboard.admin.edit_group'));
            Route::get('/group_member', 'AdminController@group_member');
            Route::get('/import_user', fn () => view('dashboard.admin.import_user'));
            Route::get('/license', 'AdminController@license');
            Route::get('/security', 'AdminController@security');
            Route::get('/bindGooleAuth', 'AdminController@bindGooleAuth');
            Route::get('/configoauth','AdminController@configoauth');
            Route::get('/tasks','AdminController@tasks');
            Route::get('/create_task',fn()=>view('dashboard.admin.create_task'));

            // 路由器
            Route::get('/route', 'AdminController@route');
            Route::get('/create_route', fn () => view('dashboard.admin.create_route'));
            Route::get('/route/static', fn() => view('dashboard.admin.route_static'));
            Route::get('/route/{id}', 'AdminController@route_manage');
        });
    });

    Route::group(['prefix' => 'api'], function () {
        Route::post('/changepassword', 'AuthenticationController@changepasswordApi');
        Route::get('/getstatus', fn () => response()->json(['reqid' => uuid(), 'code' => 200, 'msg' => 'success']));
        Route::post('/create_compute', 'AdminController@create_compute')->middleware('Admin');
        Route::post('/edit_compute', 'AdminController@edit_api_compute')->middleware('Admin');
        Route::get('/delete_compute', 'AdminController@delete_api_compute')->middleware('Admin');
        Route::get('/delete_flavor', 'BasicController@delete_api_flavor');
        Route::post('/create_flavor', 'BasicController@create_api_flavor');
        Route::post('/edit_flavor', 'BasicController@edit_api_flavor');
        Route::post('/create_network', 'AdminController@create_network')->middleware('Admin');
        Route::get('/delete_network', 'AdminController@delete_network')->middleware('Admin');
        Route::post('/edit_network', 'AdminController@edit_api_network')->middleware('Admin');
        Route::post('/rule_network', 'AdminController@rule_api_network')->middleware('Admin');
        Route::post('/rule_compute', 'AdminController@rule_api_compute')->middleware('Admin');
        Route::post('/create_image', 'AdminController@create_image')->middleware('Admin');
        Route::get('/delete_image', 'AdminController@delete_image')->middleware('Admin');
        Route::get('/getimagestatus', 'AdminController@getimagestatus')->middleware('Admin');
        Route::post('/edit_image', 'AdminController@edit_api_image')->middleware('Admin');
        Route::post('/rule_image', 'AdminController@rule_api_image')->middleware('Admin');
        Route::post('/create_volume', 'BasicController@create_volume');
        Route::get('/reset_volume', 'BasicController@reset_volume');
        Route::get('/delete_volume', 'BasicController@delete_volume');
        Route::post('/edit_volume', 'BasicController@edit_api_volume');
        Route::post('/resize_volume', 'BasicController@resize_api_volume');
        Route::get('/getResource', 'BasicController@getResource');
        Route::get('/delete_user', 'AdminController@delete_user')->middleware('Admin');
        Route::post('/repwd_user', 'AdminController@repwd_user')->middleware('Admin');
        Route::post('/create_user', 'AdminController@create_user')->middleware('Admin');
        Route::get('/remail_user', 'AdminController@remail_user')->middleware('Admin');
        Route::post('/create_instance', 'BasicController@create_instance');
        Route::get('/getInstance', 'BasicController@getInstance');
        Route::get('/delete_instance', 'BasicController@delete_instance');
        Route::post('/edit_instance', 'BasicController@edit_api_instance');
        Route::get('/start_instance', 'BasicController@start_instance');
        Route::get('/stop_instance', 'BasicController@stop_instance');
        Route::get('/restart_instance', 'BasicController@restart_instance');
        Route::post('/boot_instance', 'InstanceController@boot_api_instance');
        Route::get('/systemconfig', 'AdminController@systemconfig');
        Route::get('/connect_volume', 'InstanceController@connect_api_volume');
        Route::get('/unconnect_volume', 'InstanceController@unconnect_api_volume');
        Route::get('/virtual_instance', 'InstanceController@virtual_instance');
        Route::get('/getcpu', 'InstanceController@getcpu');
        Route::get('backinstance', 'InstanceController@backinstance');
        Route::get('/delete_backup', 'InstanceController@delete_backup');
        Route::get('/reback_backup', 'InstanceController@reback_backup');
        Route::post('/create_group', 'AdminController@create_group')->middleware('Admin');
        Route::get('/delete_group', 'AdminController@delete_group')->middleware('Admin');
        Route::post('/edit_group', 'AdminController@edit_api_group')->middleware('Admin');
        Route::get('/remove_group', 'AdminController@remove_group')->middleware('Admin');
        Route::get('/add_group', 'AdminController@add_group')->middleware('Admin');
        Route::post('/import_user', 'AdminController@import_user')->middleware('Admin');
        Route::post('/security', 'AdminController@security_api')->middleware('Admin');
        Route::post('/configauth', 'AdminController@configauth')->middleware('Admin');
        // create_route
        Route::post('/create_route', 'AdminController@create_route')->middleware('Admin');

    });
});
Route::get('/update','InstanceController@update_state');