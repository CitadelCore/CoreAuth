<?php
/**
 * LDAP connector for CoreAuth
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
use Adldap\Laravel\Facades\Adldap;

class LdapConnector extends Controller implements AuthConnector {
  public $userdata;
  public $token;
  public $config;
  protected $provider;

  public function __construct() {
    //$providers = [
      //'default' => [
      //  'domain_controllers' => OrganizationConfig::GetConfig()["LdapDomainControllers"],
      //  'base_dn' => OrganizationConfig::GetConfig()["LdapBaseDN"],
      //  'admin_username' => OrganizationConfig::GetConfig()["LdapAdminUsername"],
      //  'admin_password' => OrganizationConfig::GetConfig()["LdapAdminPassword"],
      //  'account_prefix' => OrganizationConfig::GetConfig()["LdapAccountPrefix"],
      //  'account_suffix' => OrganizationConfig::GetConfig()["LdapAccountSuffix"],
      //  'admin_account_suffix' => OrganizationConfig::GetConfig()["LdapAdminAccountSuffix"],
      //  'port' => OrganizationConfig::GetConfig()["LdapPort"],
      //  'follow_referrals' => OrganizationConfig::GetConfig()["LdapFollowReferrals"],
      //  'use_ssl' => OrganizationConfig::GetConfig()["LdapUseSSL"],
      //  'use_tls' => OrganizationConfig::GetConfig()["LdapUseTLS"],
      //  'timeout' => OrganizationConfig::GetConfig()["LdapTimeout"],
      //  'version' => 3,
      //  'custom_options' => [],
      //],
    //];
    //$this->provider = $adldap;
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
    $user = Adldap::auth()->attempt($username, $password);
    $userinfo = Adldap::search()->users()->find($username);
    if ($userinfo !== null) {
      if ($user == true) {
        if (isset($userinfo['disabled'])) {
          if ($user['disabled'] == 0) {
            if (isset($userinfo['mfa_enabled'])) {
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
            }
          } else {
              return "user_disabled";
          }
        } else {
          if (isset($userinfo['mfa_enabled'])) {
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
          }
        }
      }
    } else {
      return "error";
    }
  }

  /**
   * User data function.
   * This function takes a username,
   * and returns an array with user information.
   *
   * @param string $username is the username of the user you want to get data from.
   * @return object is the AdLdap2 object that is returned.
   */
  public function GetUser($username) {
    $userinfo = Adldap::search()->users()->find($username);
    if ($userinfo !== null) {
      return $userinfo;
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
    $userinfo = Adldap::search()->users()->find($username);
    $user = Adldap::auth()->attempt($username, $password);
    if ($user == true) {
      $userinfo->setPassword = $password;
      $userinfo->save();
      return true;
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
    $userinfo = Adldap::search()->users()->find($username);
    if ($userinfo !== null) {
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
    $userinfo = Adldap::search()->users()->find($username);
    if (isset($userinfo['mfa_enabled'])) {
      if ($userinfo['mfa_enabled'] == 1) {
        return true;
      } else {
        if ($userinfo['mfa_token'] == "none") {
          return "no_token_exists";
        } else {
          return "token_not_confirmed";
        }
      }
    } else {
      return false;
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
    $userinfo = Adldap::search()->users()->find($username);
    if (isset($userinfo['mfa_enabled'])) {
      if ($userinfo['mfa_enabled'] == 1) {
        $userinfo->mfa_token = "none";
        $userinfo->mfa_enabled = 0;
        $userinfo->save();
        return true;
      } else {
        return false;
      }
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
    $userinfo = Adldap::search()->users()->find($username);
    if (isset($userinfo['mfa_enabled'])) {
    if ($userinfo['mfa_enabled'] == 1) {
      return "already_enabled";
    } else {
      if ($userinfo['mfa_token'] == "none") {
        return "no_token_exists";
      } else {
        $userinfo->mfa_enabled = 1;
        $userinfo->save();
        return true;
      }
     }
   } else {
    return false;
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
    $userinfo = Adldap::search()->users()->find($username);
    if (isset($userinfo['mfa_enabled'])) {
      if ($userinfo['mfa_token'] == "none") {
        $userinfo->mfa_token = $mfatoken;
        $userinfo->save();
        return true;
      } else {
        return "already_enabled";
      }
    } else {
      return false;
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
    $userinfo = Adldap::search()->users()->find($username);
    if (isset($userinfo['disabled'])) {
      if ($userinfo['disabled'] == 1) {
        return "already_locked";
      } else {
        $userinfo->disabled = 1;
        $userinfo->save();
        return true;
      }
    } else {
      return false;
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
    $userinfo = Adldap::search()->users()->find($username);
    if (isset($userinfo['disabled'])) {
      if ($user['disabled'] == 0) {
        return "already_unlocked";
      } else {
        $userinfo->disabled = 0;
        $userinfo->save();
        return true;
      }
    } else {
      return false;
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
    $user = Adldap::make()->user([
      'cn' => "CitadelCore",
      'gn' => "Joseph",
      'sn' => "Marsden",
      //'cn' => $data['username'],
      //'gn' => $data['username'],
      //'sn' => $data['username'],
      //'mail' => $data['username'],
      //'ou' => "Users",
      //'userPassword' => $data['password'],
    ]);
    //$user->setCommonName($data['username']);
    //$user->setAccountName($data['username']);
    //$user->setFirstName($data['username']);
    //$user->setLastName($data['username']);
    //$user->setEmail($data['username']);
    //$user->setAttribute("risk_level", 0);
    //$user->setAttribute("mfa_token", "none");
    //$user->setAttribute("mfa_enabled", 0);
    $user->save();
    //$user->setUserAccountControl(512);
    //$user->setPassword($data['password']);
    //$user->save();
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
    $userinfo = Adldap::search()->users()->find($username);
    $userinfo->delete();
    return true;
  }
}
 ?>
