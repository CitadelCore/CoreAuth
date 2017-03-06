<?php
namespace App\Http\Controllers\WebInterface;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebInterface\ResponseHandler;
use App\Http\Controllers\Processors\AuthProcessor;
use App\Http\Controllers\Configuration\OrganizationConfig;
use Illuminate\Support\Facades\Hash;

class WebInterfaceController extends Controller {
  static function HandleRequest($request) {

  }

  static function HandleLoginEndpoint($request) {
    if ($request->isMethod("post")) {
      if ($request->has("data")) {
        $data = json_decode($request->input("data"), true);
        if (json_last_error() == JSON_ERROR_NONE) {
          if (isset($data["username"]) && isset($data["password"]) && isset($data["apikey"])) {
            AuthProcessor::BeginCycle($data["username"], $data["password"], $data["apikey"]);
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
          AuthProcessor::CreateAccount($data, $data['apikey']);
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
            AuthProcessor::ChangePassword($data["username"], $data["password"], $data["newpassword"], $data["apikey"]);
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
            AuthProcessor::DeleteAccount($data["username"], $data["password"], $data["apikey"]);
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
