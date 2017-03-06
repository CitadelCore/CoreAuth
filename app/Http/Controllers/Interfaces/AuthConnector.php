<?php
namespace App\Http\Controllers\Interfaces;

interface AuthConnector {
  public function Authenticate($username, $password);
  public function GetUser($username);
  public function ChangePassword($username, $password);
  public function DoesUserExist($username);
  public function DoesUserUse2FA($username);
  public function Revoke2FA($username);
  public function Enforce2FA($username);
  public function Enable2FA($username, $mfatoken);
  public function LockAccount($username);
  public function UnlockAccount($username);
  public function CreateAccount($data);
  public function DeleteAccount($username);
  // public function UpdateAccount($username, $data);
  // public function IsAccountDisabled($username);
}
?>
