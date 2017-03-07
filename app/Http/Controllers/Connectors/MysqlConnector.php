<?php

/**
 * MySQL connector for CoreAuth
 * This class handles the user account information processing,
 * and communication between CoreAuth and the storage backend.
 *
 * @author Joseph Marsden <josephmarsden@towerdevs.xyz>
 * @copyright 2017 CoreNIC
 * @license https://central.core/licenses/coreauth.php
 */
namespace App\Http\Controllers\Connectors;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\AuthConnector;
use App\Http\Controllers\WebInterface\ResponseHandler;
use App\Http\Controllers\Configuration\OrganizationConfig;
use Illuminate\Support\Facades\Hash;
use App\Models\Accounts;

class MysqlConnector extends Controller implements AuthConnector {
  public $userdata;
  public $token;

  public function __construct() {

  }

/**
 * User authentication function.
 * This function takes a username and password,
 * and checks if the user can be authenticated successfully.
 *
 * @param string $username is the username of the user you want to authenticate.
 * @param string $password is the password of the user you want to authenticate.
 * @return string is the dynamic return "code".
 */
  public function Authenticate($username, $password) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user !== null) {
      if ($user['disabled'] == 0) {
        if (Hash::check($password, $user['password']) == true) {
          if ($user['mfa_enabled'] == 0) {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
              $agent = $_SERVER['HTTP_USER_AGENT'];
            } else {
              $agent = "noagent";
            }
            $userip = $_SERVER['REMOTE_ADDR'];
            $day = date("z");
            $org = OrganizationConfig::GetConfig()['OrganizationName'];
            $apikey = OrganizationConfig::GetConfig()['ApiKey'];
            $fullstring = $agent . $userip . $day . $password . $username . $org . $apikey;
            $this->token = (hash('sha512', $fullstring));
            return "accepted";
          } else {
            return "mfa_required";
          }
        } else {
          return "incorrect_pass";
        }
      } else {
        return "user_disabled";
      }
    } else {
      return "no_user";
    }
  }

  /**
   * User data function.
   * This function takes a username,
   * and returns an array with user information.
   *
   * @param string $username is the username of the user you want to get data from.
   * @return object is the Eloquent object that is returned.
   */
  public function GetUser($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user !== null) {
      return $user;
    } else {
      return false;
    }
  }

  /**
   * Change password function.
   * This function takes a username and a new password,
   * and changes the user's password to that password.
   *
   * @param string $username is the username of the user you want to change password of.
   * @param string $password is the new password you want to change the password to.
   * @return boolean is if the password change was successful.
   */
  public function ChangePassword($username, $password) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user !== null) {
      if (Hash::check($password, $user['password'])) {
        return false;
      } else {
        $password = Hash::make($password);
        $user->password = $password;
        $user->save();
        return true;
      }
    } else {
      return false;
    }
  }

  /**
   * User existance function.
   * This function takes a username,
   * and checks if that user exists.
   *
   * @param string $username is the username of the user you want to check exists.
   * @return boolean is if the user exists or not.
   */
  public function DoesUserExist($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user !== null) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * User 2FA existance function.
   * This function takes a username,
   * and checks if that user uses 2FA.
   *
   * @param string $username is the username of the user you want to check has 2FA enabled.
   * @return string is the dynamic return code.
   */
  public function DoesUserUse2FA($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user['mfa_enabled'] == 1) {
      return true;
    } else {
      if ($user['mfa_token'] == null) {
        return "no_token_exists";
      } else {
        return "token_not_confirmed";
      }
    }
  }

  /**
   * User 2FA revoking function.
   * This function takes a username,
   * and revokes the user's 2FA token.
   *
   * @param string $username is the username of the user you want to revoke 2FA on.
   * @return boolean is the return status.
   */
  public function Revoke2FA($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user['mfa_enabled'] == 1) {
      $user->mfa_token = null;
      $user->mfa_enabled = 0;
      $user->save();
      return true;
    } else {
      return false;
    }
  }

  /**
   * User 2FA enforcement function.
   * This function takes a username,
   * and starts enforcement of that user's 2FA token.
   *
   * @param string $username is the username of the user you want to enforce 2FA on.
   * @return string is the dynamic return code.
   */
  public function Enforce2FA($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user['mfa_enabled'] == 1) {
      return "already_enabled";
    } else {
      if ($user['mfa_token'] == null) {
        return "no_token_exists";
      } else {
        $user->mfa_enabled = 1;
        $user->save();
        return true;
      }
    }
  }

  /**
   * User 2FA enablement function.
   * This function takes a username,
   * and enables 2FA on that user.
   * Please note that the token also needs to
   * be enforced before it will be required.
   *
   * @param string $username is the username of the user you want to enable 2FA on.
   * @return string is the dynamic return code.
   */
  public function Enable2FA($username, $mfatoken) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user['mfa_token'] == null) {
      $user->mfa_token = $mfatoken;
      $user->save();
      return true;
    } else {
      return "already_enabled";
    }
  }

  /**
   * User account locking function.
   * This function takes a username,
   * and locks that user's account.
   *
   * @param string $username is the username of the user you want to lock the account of.
   * @return string is the dynamic return code.
   */
  public function LockAccount($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user['disabled'] == 1) {
      return "already_locked";
    } else {
      $user->disabled = 1;
      $user->save();
      return true;
    }
  }

  /**
   * User account unlocking function.
   * This function takes a username,
   * and unlocks that user's account.
   *
   * @param string $username is the username of the user you want to unlock the account of.
   * @return string is the dynamic return code.
   */
  public function UnlockAccount($username) {
    $user = Accounts::where('username', $username)->get()->first();
    if ($user['disabled'] == 0) {
      return "already_unlocked";
    } else {
      $user->disabled = 0;
      $user->save();
      return true;
    }
  }

  /**
   * User account creation function.
   * This function takes a username, password, and disabled status
   * and creates a user account with those parameters.
   *
   * @param string $username is the username of the user you want to create.
   * @param string $password is the password of the user you want to create.
   * @param integer $disabled is the disabled status of the user you want to create.
   * @return boolean is the return status.
   */
  public function CreateAccount($data) {
    $user = new Accounts;
    $user->username = $data['username'];
    $user->password = $data['password'];
    $user->disabled = $data['disabled'];
    $user->risk_level = 0;
    $user->mfa_token = null;
    $user->mfa_enabled = 0;
    $user->save();
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent = $_SERVER['HTTP_USER_AGENT'];
    } else {
      $agent = "noagent";
    }
    $userip = $_SERVER['REMOTE_ADDR'];
    $day = date("z");
    $org = OrganizationConfig::GetConfig()['OrganizationName'];
    $apikey = OrganizationConfig::GetConfig()['ApiKey'];
    $fullstring = $agent . $userip . $day . $data['password'] . $data['username'] . $org . $apikey;
    $this->token = (hash('sha512', $fullstring));
    return true;
  }

  /**
   * User account deletion function.
   * This function takes a username,
   * and deletes that user account
   *
   * @param string $username is the username of the user you want to delete.
   * @return boolean is the return status.
   */
  public function DeleteAccount($username) {
    $user = Accounts::where('username', $username)->get()->first();
    $user->delete();
    return true;
  }

/**
* Deprecated until further notice.
*  public function UpdateAccount($username, $data) {
*    $user = Accounts::where('username', $username)->get()->first();
*    foreach ($data as $key => $value) {
*      if ($key !== "password" && $key !== "mfa_token" && $key !== "risk_level" && $key !== "mfa_enabled" && $key !== "disabled") {
*        $user->$key = $value;
*      }
*    }
*    return true;
*  }
*/
  public function IsAccountDisabled($username) {
  $user = Accounts::where('username', $username)->get()->first();
    if ($user['disabled'] == 0) {
      return false;
    } else {
      return true;
    }
  }
}
 ?>
