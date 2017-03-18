<?php
namespace App\Http\Controllers\Processors;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Configuration\OrganizationConfig;
use App\Http\Controllers\WebInterface\ResponseHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Command\RiskEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthProcessor extends Controller {
  static function BeginCycle($username, $password, $apikey, $callback = "https://localhost:4434/error", $request) {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
        $re = new RiskEngine;
        $re->username = $username;
        $re->status = $request->session()->get('riskengine_status', 'disabled');
        $connector = OrganizationConfig::GetConfig()['AuthProcessor'];
        $result = $connector->Authenticate($username, $password);
        if ($result == "accepted") {
          $re->UpdateStatus();
          if ($re->status == "allowed") {
            $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
            $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
            if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
            Log::info("User " . $username . " logged in successfully.");
            ResponseHandler::ReturnLoginAccepted($connector->token);
          } elseif ($re->status == "warning") {
            $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
            $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
            if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
            ResponseHandler::ReturnRiskEngineWarning("1", $connector->token);
            Log::warning("User " . $username . " logged in successfully, but warned by RiskEngine.");
          } elseif ($re->status == "2farequired") {
            ResponseHandler::ReturnMfaRequired();
            Log::warning("User " . $username . " hit RiskEngine MFA challenge.");
          } elseif ($re->status == "blocked") {
            ResponseHandler::ReturnRiskEngineError("1");
            Log::alert("User " . $username . " login attempt blocked by RiskEngine.");
          } elseif ($re->status == "error") {
            ResponseHandler::ReturnRiskEngineError("1");
            Log::alert("User " . $username . " triggered a RiskEngine error.");
          } elseif ($re->status == "disabled") {
            Log::info("User " . $username . " logged in successfully, bypassing RiskEngine because it is disabled.");
            ResponseHandler::ReturnLoginAccepted($connector->token);
          } else {
            ResponseHandler::ReturnInternalError();
            Log::critical("Encountered a response error while trying to log in the user " . $username . ".");
          }
        } elseif ($result == "incorrect_pass") {
          $re->SendAppend("login_deniedon", strtotime(date('y-m-d h:m:s')));
          Log::info("User " . $username . " entered an incorrect password.");
          ResponseHandler::ReturnIncorrectPass();
        } elseif ($result == "user_disabled") {
          Log::info("User " . $username . " could not be logged in because the account is disabled.");
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "no_user") {
          Log::info("User " . $username . " could not be logged in because the account does not exist.");
          ResponseHandler::ReturnUserNotExist();
        } else {
          Log::critical("Encountered a internal error while trying to log in the user " . $username . ".");
          ResponseHandler::ReturnInternalError();
        }
    } else {
      Log::error("Login attempt blocked for user " . $username . " because an invalid API key was provided.");
      ResponseHandler::ReturnInvalidApiKey();
    }
  }

  static function CreateAccount($data, $apikey, $callback = "https://localhost:4434/error", $request) {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
        if (isset($data['username']) && isset($data['password']) && isset($data['disabled'])) {
          $validator = Validator::make([
            'username' => $data['username'],
            'password' => $data['password'],
            'disabled' => $data['disabled'],
          ],
          [
            'username' => 'required|min:1|max:100|email|unique:accounts',
            'password' => 'required|min:5|max:100|unique:accounts',
            'disabled' => 'required',
          ]);
          if ($validator->fails()) {
            Log::info("Encountered a validation error while trying to create an account.");
            ResponseHandler::ReturnInvalidData();
          } else {
            $data['password'] = Hash::make($data['password']);
            $connector = OrganizationConfig::GetConfig()['AuthProcessor'];
            $connector->CreateAccount($data);
            Log::info("Created a new account with the username " . $data['username'] . ".");
            ResponseHandler::ReturnAccountCreated($connector->token);
          }
        } else {
          Log::info("Account creation attempt error because not enough parameters were provided.");
          ResponseHandler::ReturnNotEnoughParameters();
        }
    } else {
      Log::error("Account creation attempt blocked because an invalid API key was provided.");
      ResponseHandler::ReturnInvalidApiKey();
    }
  }

  static function ChangePassword($username, $password, $newpassword, $apikey, $callback = "https://localhost:4434/error", $request) {
    $re = new RiskEngine;
    $re->username = $username;
    $re->status = $request->session()->get('riskengine_status', 'disabled');
    $connector = OrganizationConfig::GetConfig()['AuthProcessor'];
    $result = $connector->Authenticate($username, $password);
    if ($result == "accepted") {
      $re->UpdateStatus();
      if ($re->status == "allowed") {
        $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
        $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
        if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
        $validator = Validator::make([
          'password' => $password],
          [
          'password' => 'required|min:5|max:100|unique:accounts,password',
        ]);
        if ($validator->fails()) {
          Log::info("Encountered a validation error while trying to change the password for the user " . $username . ".");
          ResponseHandler::ReturnInvalidData();
        } else {
          if ($connector->ChangePassword($username, $newpassword) == true) {
            Log::info("Changed the password for the user " . $username . ".");
            ResponseHandler::ReturnPasswordChanged();
          } else {
            Log::info("Encountered a validation error while trying to change the password for the user " . $username . ".");
            ResponseHandler::ReturnInvalidData();
          }
        }
      } elseif ($re->status == "warning") {
        $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
        $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
        if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
        $validator = Validator::make([
          'password' => $password],
          [
          'password' => 'required|min:5|max:100|unique:accounts,password',
        ]);
        if ($validator->fails()) {
          ResponseHandler::ReturnInvalidData();
        } else {
          if ($connector->ChangePassword($username, $newpassword) == true) {
            ResponseHandler::ReturnPasswordChanged();
          } else {
            ResponseHandler::ReturnInvalidData();
          }
        }
      } elseif ($re->status == "2farequired") {
        ResponseHandler::ReturnMfaRequired();
      } elseif ($re->status == "blocked") {
        ResponseHandler::ReturnRiskEngineError("1");
      } elseif ($re->status == "error") {
        ResponseHandler::ReturnRiskEngineError("1");
      } elseif ($re->status == "disabled") {
        $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
        $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
        if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
        $validator = Validator::make([
          'password' => $password],
          [
          'password' => 'required|min:5|max:100|unique:accounts,password',
        ]);
        if ($validator->fails()) {
          ResponseHandler::ReturnInvalidData();
        } else {
          if ($connector->ChangePassword($username, $newpassword) == true) {
            ResponseHandler::ReturnPasswordChanged();
          } else {
            ResponseHandler::ReturnInvalidData();
          }
        }
      } else {
        ResponseHandler::ReturnInternalError();
      }
    } elseif ($result == "incorrect_pass") {
      $re->SendAppend("login_deniedon", strtotime(date('y-m-d h:m:s')));
      ResponseHandler::ReturnIncorrectPass();
    } elseif ($result == "user_disabled") {
      ResponseHandler::ReturnUserDisabled();
    } elseif ($result == "no_user") {
      ResponseHandler::ReturnUserNotExist();
    } else {
      ResponseHandler::ReturnInternalError();
    }
  }

 static function DeleteAccount($username, $password, $apikey, $callback = "https://localhost:4434/error", $request) {
   if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
     $re = new RiskEngine;
     $re->username = $username;
     $re->status = $request->session()->get('riskengine_status', 'disabled');
     $connector = OrganizationConfig::GetConfig()['AuthProcessor'];
     $result = $connector->Authenticate($username, $password);
     if ($result == "accepted") {
       $re->UpdateStatus();
       if ($re->status == "allowed") {
         $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
         $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
         if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
         $connector->DeleteAccount($username);
       } elseif ($re->status == "warning") {
         $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
         $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
         if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
         $connector->DeleteAccount($username);
       } elseif ($re->status == "2farequired") {
         ResponseHandler::ReturnMfaRequired();
       } elseif ($re->status == "blocked") {
         ResponseHandler::ReturnRiskEngineError("1");
       } elseif ($re->status == "error") {
         ResponseHandler::ReturnRiskEngineError("1");
       } elseif ($re->status == "disabled") {
         $re->SendAppend("login_acceptedon", strtotime(date('y-m-d h:m:s')));
         $re->SendAppend("ip_loggedin", $_SERVER['REMOTE_ADDR']);
         if (isset($_SERVER['HTTP_USER_AGENT'])) { $re->SendAppend("recent_browser", $_SERVER['HTTP_USER_AGENT']); };
         $connector->DeleteAccount($username);
       } else {
         ResponseHandler::ReturnInternalError();
       }
     } elseif ($result == "incorrect_pass") {
       $re->SendAppend("login_deniedon", strtotime(date('y-m-d h:m:s')));
       ResponseHandler::ReturnIncorrectPass();
     } elseif ($result == "user_disabled") {
       ResponseHandler::ReturnUserDisabled();
     } elseif ($result == "no_user") {
       ResponseHandler::ReturnUserNotExist();
     } else {
       ResponseHandler::ReturnInternalError();
     }
   } else {
     ResponseHandler::ReturnInvalidApiKey();
   }
  }

  static function Logout($callback = "https://localhost:4434/error") {
    ResponseHandler::ReturnLogout();
  }
}

?>
