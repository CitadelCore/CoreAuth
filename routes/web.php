<?php

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

use App\Http\Controllers\WebInterface\WebInterfaceController;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    WebInterfaceController::HandleWebAccess();
});

Route::post('/endpoints', function (Request $request) {
    WebInterfaceController::ReturnEndpoints();
});

Route::post('/endpoints/login', function (Request $request) {
    WebInterfaceController::HandleWebLoginCallback($request);
});

Route::post('/endpoints/logout', function (Request $request) {
    WebInterfaceController::HandleWebLogoutCallback($request);
});

Route::post('/endpoints/changepassword', function (Request $request) {
    WebInterfaceController::HandleChangePasswordCallback($request);
});

Route::post('/endpoints/createaccount', function (Request $request) {
    WebInterfaceController::HandleCreateAccountCallback($request);
});

Route::post('/endpoints/deleteaccount', function (Request $request) {
    WebInterfaceController::HandleDeleteAccountCallback($request);
});

Route::post('/endpoints/updateaccount', function (Request $request) {
    WebInterfaceController::HandleUpdateAccountCallback($request);
});


Route::get('/endpoints/api', function (Request $request) {
    WebInterfaceController::ReturnApiEndpoints();
});

Route::post('/endpoints/api/login', function (Request $request) {
    WebInterfaceController::HandleLoginEndpoint($request);
});

Route::post('/endpoints/api/logout', function (Request $request) {
    WebInterfaceController::HandleLogoutEndpoint($request);
});

Route::post('/endpoints/api/changepassword', function (Request $request) {
    WebInterfaceController::HandleChangePasswordEndpoint($request);
});

Route::post('/endpoints/api/createaccount', function (Request $request) {
    WebInterfaceController::HandleCreateAccountEndpoint($request);
});

Route::post('/endpoints/api/deleteaccount', function (Request $request) {
    WebInterfaceController::HandleDeleteAccountEndpoint($request);
});

Route::post('/endpoints/api/updateaccount', function (Request $request) {
    WebInterfaceController::HandleUpdateAccountEndpoint($request);
});

Route::post('/developer/hashtest', function (Request $request) {
    WebInterfaceController::HandleHashTest($request);
});
