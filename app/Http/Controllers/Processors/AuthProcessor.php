<?php
namespace App\Http\Controllers\Processors;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Configuration\OrganizationConfig;
use App\Http\Controllers\Connectors\MysqlConnector;
use App\Http\Controllers\Connectors\LdapConnector;
use App\Http\Controllers\WebInterface\ResponseHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthProcessor extends Controller {
  static function BeginCycle($username, $password, $apikey, $callback = "https://localhost:4434/error") {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
      if (OrganizationConfig::GetConfig()['AuthProcessor'] == "mysql") {
        $connector = new MysqlConnector;
        $result = $connector->Authenticate($username, $password);
        if ($result == "accepted") {
          ResponseHandler::ReturnLoginAccepted($connector->token);
        } elseif ($result == "mfa_required") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "incorrect_pass") {
          ResponseHandler::ReturnIncorrectPass();
        } elseif ($result == "user_disabled") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "no_user") {
          ResponseHandler::ReturnUserNotExist();
        } else {
          ResponseHandler::ReturnInternalError();
        }
      } elseif (OrganizationConfig::GetConfig()['AuthProcessor'] == "ldap") {
          $connector = new LdapConnector;
          $result = $connector->Authenticate($username, $password);
          if ($result == "accepted") {
            ResponseHandler::ReturnLoginAccepted($connector->token);
          } elseif ($result == "mfa_required") {
            ResponseHandler::ReturnUserDisabled();
          } elseif ($result == "incorrect_pass") {
            ResponseHandler::ReturnIncorrectPass();
          } elseif ($result == "user_disabled") {
            ResponseHandler::ReturnUserDisabled();
          } elseif ($result == "no_user") {
            ResponseHandler::ReturnUserNotExist();
          } else {
            ResponseHandler::ReturnInternalError();
          }
      } else {
        ResponseHandler::ReturnInternalError();
      }
    } else {
      ResponseHandler::ReturnInvalidApiKey();
    }
  }

  static function CreateAccount($data, $apikey, $callback = "https://localhost:4434/error") {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
      if (OrganizationConfig::GetConfig()['AuthProcessor'] == "mysql") {
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
            ResponseHandler::ReturnInvalidData();
          } else {
            $data['password'] = Hash::make($data['password']);
            $connector = new MysqlConnector;
            $connector->CreateAccount($data);
            ResponseHandler::ReturnAccountCreated($connector->token);
          }
        } else {
          ResponseHandler::ReturnNotEnoughParameters();
        }
      } elseif (OrganizationConfig::GetConfig()['AuthProcessor'] == "ldap") {
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
              // var_dump($validator->messages()->all());
              ResponseHandler::ReturnInvalidData();
            } else {
              // $data['password'] = Hash::make($data['password']);
              $connector = new LdapConnector();
              if (OrganizationConfig::GetConfig()['LdapType'] == "openldap") {
                $basedn = OrganizationConfig::GetConfig()['LdapBaseDN'];
                $data['username'] = explode("@", $data['username'])[0];
                $connector->CreateAccount($data);
                ResponseHandler::ReturnPasswordChanged();
              } else {
                $connector->CreateAccount($data);
                ResponseHandler::ReturnPasswordChanged();
              }
            }
          } else {
            ResponseHandler::ReturnNotEnoughParameters();
          }
      } else {
        ResponseHandler::ReturnInternalError();
      }
    } else {
      ResponseHandler::ReturnInvalidApiKey();
    }
  }

  static function ChangePassword($username, $password, $newpassword, $apikey, $callback = "https://localhost:4434/error") {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
      if (OrganizationConfig::GetConfig()['AuthProcessor'] == "mysql") {
        $connector = new MysqlConnector;
        $result = $connector->Authenticate($username, $password);
        if ($result == "accepted") {
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
        } elseif ($result == "mfa_required") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "incorrect_pass") {
          ResponseHandler::ReturnIncorrectPass();
        } elseif ($result == "user_disabled") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "no_user") {
          ResponseHandler::ReturnUserNotExist();
        } else {
          ResponseHandler::ReturnInternalError();
        }
      } elseif (OrganizationConfig::GetConfig()['AuthProcessor'] == "ldap") {
          $connector = new MysqlConnector;
          $result = $connector->Authenticate($username, $password);
          if ($result == "accepted") {
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
          } elseif ($result == "mfa_required") {
            ResponseHandler::ReturnUserDisabled();
          } elseif ($result == "incorrect_pass") {
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
}

 static function DeleteAccount($username, $password, $apikey, $callback = "https://localhost:4434/error") {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
      if (OrganizationConfig::GetConfig()['AuthProcessor'] == "mysql") {
        $connector = new MysqlConnector;
        $result = $connector->Authenticate($username, $password);
        if ($result == "accepted") {
          $connector->DeleteAccount($username);
          ResponseHandler::ReturnAccountDeleted();
        } elseif ($result == "mfa_required") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "incorrect_pass") {
          ResponseHandler::ReturnIncorrectPass();
        } elseif ($result == "user_disabled") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "no_user") {
          ResponseHandler::ReturnUserNotExist();
        } else {
          ResponseHandler::ReturnInternalError();
        }
      } elseif (OrganizationConfig::GetConfig()['AuthProcessor'] == "ldap") {
          $connector = new LdapConnector;
          $result = $connector->Authenticate($username, $password);
          if ($result == "accepted") {
            $connector->DeleteAccount($username);
            ResponseHandler::ReturnAccountDeleted();
          } elseif ($result == "mfa_required") {
            ResponseHandler::ReturnUserDisabled();
          } elseif ($result == "incorrect_pass") {
            ResponseHandler::ReturnIncorrectPass();
          } elseif ($result == "user_disabled") {
            ResponseHandler::ReturnUserDisabled();
          } elseif ($result == "no_user") {
            ResponseHandler::ReturnUserNotExist();
          } else {
            ResponseHandler::ReturnInternalError();
          }
      } else {
        ResponseHandler::ReturnInternalError();
      }
    } else {
      ResponseHandler::ReturnInvalidApiKey();
    }
  }

  /**static function UpdateAccount($username, $data, $apikey, $callback = "https://localhost:4434/error") {
    if (OrganizationConfig::GetConfig()['ApiKey'] == $apikey) {
      if (OrganizationConfig::GetConfig()['AuthProcessor'] == "mysql") {
        $connector = new MysqlConnector;
        $result = $connector->Authenticate($username, $password);
        if ($result == "accepted") {
          $connector->UpdateAccount($username, $data);
        } elseif ($result == "mfa_required") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "incorrect_pass") {
          ResponseHandler::ReturnIncorrectPass();
        } elseif ($result == "user_disabled") {
          ResponseHandler::ReturnUserDisabled();
        } elseif ($result == "no_user") {
          ResponseHandler::ReturnUserNotExist();
        } else {
          ResponseHandler::ReturnInternalError();
        }
      } else {
        ResponseHandler::ReturnInternalError();
      }
    } else {
      ResponseHandler::ReturnInvalidApiKey();
    }
  }**/

  static function Logout($callback = "https://localhost:4434/error") {
    ResponseHandler::ReturnLogout();
  }
}

?>
