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
use App\Http\Controllers\WebInterface\HtmlResponseHandler;
use App\Http\Controllers\WebInterface\ApiController;
use Illuminate\Http\Request;

Route::match(['get', 'post'], '/', function (Request $request) {
    WebInterfaceController::HandleWebAccess();
});

Route::match(['get', 'post'], '/endpoints', function (Request $request) {
    WebInterfaceController::ReturnEndpoints();
});

Route::match(['get', 'post'], '/endpoints/login', function (Request $request) {
    return HtmlResponseHandler::ShowLoginEndpoint($request);
});

Route::match(['get', 'post'], '/endpoints/logout', function (Request $request) {
    return HtmlResponseHandler::ShowLogoutEndpoint($request);
});

Route::match(['get', 'post'], '/endpoints/changepassword', function (Request $request) {
    return HtmlResponseHandler::ShowChangePasswordEndpoint($request);
});

Route::match(['get', 'post'], '/endpoints/createaccount', function (Request $request) {
    return HtmlResponseHandler::ShowCreateAccountEndpoint($request);
});

Route::match(['get', 'post'], '/endpoints/deleteaccount', function (Request $request) {
    return HtmlResponseHandler::ShowDeleteAccountEndpoint($request);
});

Route::match(['get', 'post'], '/endpoints/managemfa', function (Request $request) {
    return HtmlResponseHandler::ShowMultiFactorManageEndpoint($request);
});

Route::post('/endpoints/login/post', function (Request $request) {
    return WebInterfaceController::PostLoginEndpoint($request);
});

Route::post('/endpoints/logout/post', function (Request $request) {
    return WebInterfaceController::PostLogoutEndpoint($request);
});

Route::post('/endpoints/changepassword/post', function (Request $request) {
    return WebInterfaceController::PostChangePasswordEndpoint($request);
});

Route::post('/endpoints/createaccount/post', function (Request $request) {
    return WebInterfaceController::PostCreateAccountEndpoint($request);
});

Route::post('/endpoints/deleteaccount/post', function (Request $request) {
    return WebInterfaceController::PostDeleteAccountEndpoint($request);
});


Route::get('/endpoints/api', function (Request $request) {
    ApiController::ReturnApiEndpoints();
});

Route::post('/endpoints/api/login', function (Request $request) {
    ApiController::HandleLoginEndpoint($request);
});

Route::post('/endpoints/api/logout', function (Request $request) {
    ApiController::HandleLogoutEndpoint($request);
});

Route::post('/endpoints/api/changepassword', function (Request $request) {
    ApiController::HandleChangePasswordEndpoint($request);
});

Route::post('/endpoints/api/createaccount', function (Request $request) {
    ApiController::HandleCreateAccountEndpoint($request);
});

Route::post('/endpoints/api/deleteaccount', function (Request $request) {
    ApiController::HandleDeleteAccountEndpoint($request);
});

Route::post('/developer/hashtest', function (Request $request) {
    ApiController::HandleHashTest($request);
});

Route::get('/developer/testlogin', function (Request $request) {
    return HtmlResponseHandler::HandleTestLogin($request);
});

Route::post('/developer/testlogin', function (Request $request) {
    return HtmlResponseHandler::HandleTestLogin($request);
});
