<?php
namespace App\Http\Controllers\WebInterface;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebInterface\ResponseHandler;
use App\Http\Controllers\WebInterface\WebInterfaceController;
use App\Http\Controllers\Processors\AuthProcessor;
use App\Http\Controllers\Configuration\OrganizationConfig;
use Illuminate\Support\Facades\Hash;

class HtmlResponseHandler extends Controller {
  static function ShowLoginEndpoint($request) {
    if ($request->has("username") && $request->has("password") && $request->has("callback")) {
      $callback = WebInterfaceController::StripCallbackURL($request->input("callback"));
      return view("interface/interface", ['username' => $request->input("username"), 'password' => $request->input("password"), 'callback' => $callback]);
    } else {
      ResponseHandler::ReturnNotEnoughParameters();
    }
  }

  static function ShowLogoutEndpoint($request) {
    if ($request->has("callback")) {
      $callback = WebInterfaceController::StripCallbackURL($request->input("callback"));
      return view("interface/interface", ['callback' => $callback]);
    } else {
      ResponseHandler::ReturnNotEnoughParameters();
    }
  }

  static function ShowChangePasswordEndpoint($request) {
    return view("interface/cpassword");
  }

  static function ShowDeleteAccountEndpoint($request) {
    return view("interface/deleteaccount");
  }

  static function ShowCreateAccountEndpoint($request) {
    return view("interface/newaccount");
  }

  static function ShowMultiFactorManageEndpoint($request) {
    return view("interface/mfaeenable");
  }


  static function HandleTestLogin($request) {
    if ($request->has("token")) {
      return view("developer/testlogin", ['token' => $request->input("token"), 'incident_id' => "NONE", 'debug' => var_export($request->all(), true)]);
    } elseif ($request->has("incident_id")) {
      return view("developer/testlogin", ['token' => "NONE", 'incident_id' => base64_decode($request->input("incident_id")), 'debug' => var_export($request->all(), true)]);
    } else {
      return view("developer/testlogin", ['token' => "NONE", 'incident_id' => "NONE", 'debug' => var_export($request->all(), true)]);
    }
  }
}
?>
