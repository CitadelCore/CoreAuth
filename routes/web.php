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
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    WebInterfaceController::HandleWebAccess();
});

Route::post('/endpoints', function (Request $request) {
    WebInterfaceController::ReturnEndpoints();
});

Route::post('/endpoints/login', function (Request $request) {
    return HtmlResponseHandler::ShowLoginEndpoint($request);
});

Route::post('/endpoints/logout', function (Request $request) {
    return HtmlResponseHandler::ShowLogoutEndpoint($request);
});

Route::post('/endpoints/changepassword', function (Request $request) {
    return HtmlResponseHandler::ShowChangePasswordEndpoint($request);
});

Route::post('/endpoints/createaccount', function (Request $request) {
    return HtmlResponseHandler::ShowCreateAccountEndpoint($request);
});

Route::post('/endpoints/deleteaccount', function (Request $request) {
    return HtmlResponseHandler::ShowDeleteAccountEndpoint($request);
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

Route::get('/developer/testlogin', function (Request $request) {
    return HtmlResponseHandler::HandleTestLogin($request);
});

Route::post('/developer/testlogin', function (Request $request) {
    return HtmlResponseHandler::HandleTestLogin($request);
});
