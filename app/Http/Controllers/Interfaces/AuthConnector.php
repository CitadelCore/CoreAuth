<?php
namespace App\Http\Controllers\Interfaces;

interface AuthConnector {
  public function Authenticate($username, $password);
  public function GetUser($username);
  public function ChangePassword($username, $password);
  public function DoesUserExist($username);
  public function LockAccount($username);
  public function UnlockAccount($username);
  public function CreateAccount($data);
  public function DeleteAccount($username);
}
?>
