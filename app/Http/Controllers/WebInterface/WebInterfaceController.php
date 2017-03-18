<?php
namespace App\Http\Controllers\WebInterface;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebInterface\ResponseHandler;
use App\Http\Controllers\Processors\AuthProcessor;
use App\Http\Controllers\Configuration\OrganizationConfig;
use Illuminate\Support\Facades\Hash;

class WebInterfaceController extends Controller {
  static function StripCallbackURL($str) {
    $str = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $str);
    $str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $str);
    $str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $str);
    $str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#is', '$1>', $str);
    $str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#is', '$1>', $str);
    $str = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#ius', '$1>', $str);
    return $str;
  }

  static function PostLoginEndpoint($request) {
    if ($request->isMethod("post")) {
      if ($request->has("username") && $request->has("password")) {
        AuthProcessor::BeginCycle($request->input("username"), $request->input("password"), OrganizationConfig::GetConfig()["ApiKey"], null, $request);
      } else {
        ResponseHandler::ReturnNotEnoughParameters();
      }
  } else {
    ResponseHandler::ReturnNotPost();
  }
}

 static function HandleLoginEndpoint($request) {
    if ($request->isMethod("post")) {
      if ($request->has("data")) {
        $data = json_decode($request->input("data"), true);
        if (json_last_error() == JSON_ERROR_NONE) {
          if (isset($data["username"]) && isset($data["password"]) && isset($data["apikey"])) {
            AuthProcessor::BeginCycle($data["username"], $data["password"], $data["apikey"], $request);
          } else {
            ResponseHandler::ReturnNotEnoughParameters();
          }
        } else {
          ResponseHandler::ReturnInvalidSyntax();
        }
      } else {
        ResponseHandler::ReturnNoCommand();
      }
    } else {
      ResponseHandler::ReturnNotPost();
    }
  }

  static function HandleLogoutEndpoint($request) {
    AuthProcessor::Logout();
  }

  static function HandleCreateAccountEndpoint($request) {
    if ($request->isMethod("post")) {
      if ($request->has("data")) {
        $data = json_decode($request->input("data"), true);
        if (json_last_error() == JSON_ERROR_NONE) {
          AuthProcessor::CreateAccount($data, $data['apikey'], $request);
        } else {
          ResponseHandler::ReturnInvalidSyntax();
        }
      } else {
        ResponseHandler::ReturnNoCommand();
      }
    } else {
      ResponseHandler::ReturnNotPost();
    }
  }

  static function HandleChangePasswordEndpoint($request) {
    if ($request->isMethod("post")) {
      if ($request->has("data")) {
        $data = json_decode($request->input("data"), true);
        if (json_last_error() == JSON_ERROR_NONE) {
          if (isset($data["username"]) && isset($data["password"]) && isset($data["newpassword"]) && isset($data["apikey"])) {
            AuthProcessor::ChangePassword($data["username"], $data["password"], $data["newpassword"], $data["apikey"], $request);
          } else {
            ResponseHandler::ReturnNotEnoughParameters();
          }
        } else {
          ResponseHandler::ReturnInvalidSyntax();
        }
      } else {
        ResponseHandler::ReturnNoCommand();
      }
    } else {
      ResponseHandler::ReturnNotPost();
    }
  }

  static function HandleDeleteAccountEndpoint($request) {
    if ($request->isMethod("post")) {
      if ($request->has("data")) {
        $data = json_decode($request->input("data"), true);
        if (json_last_error() == JSON_ERROR_NONE) {
          if (isset($data["username"]) && isset($data["password"])) {
            AuthProcessor::DeleteAccount($data["username"], $data["password"], $data["apikey"], $request);
          } else {
            ResponseHandler::ReturnNotEnoughParameters();
          }
        } else {
          ResponseHandler::ReturnInvalidSyntax();
        }
      } else {
        ResponseHandler::ReturnNoCommand();
      }
    } else {
      ResponseHandler::ReturnNotPost();
    }
  }

  static function ReturnEndpoints() {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"Endpoints available: login, logout, multifactor, createaccount, deleteaccount, updateaccount", "response_code"=>"200A"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function ReturnApiEndpoints() {
    $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"Endpoints available: login, logout, changepassword, createaccount, deleteaccount, updateaccount", "response_code"=>"200A"));
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  static function HandleWebAccess() {
    echo "Not authorized.";
  }

  static function HandleHashTest($request) {
    if (OrganizationConfig::GetConfig()['Production'] == false) {
      $data = json_decode($request->input("data"), true);
      $hashedpw = Hash::make($data["password"]);
      $response = array("type"=>"response", "id"=>"1", "attributes"=>array("response_friendly"=>"Hashed Password: $hashedpw", "response_code"=>"200A"));
      header('Content-Type: application/json');
      echo json_encode($response);
    } else {
      ResponseHandler::ReturnNotAvailable();
    }
  }
}
 ?>
