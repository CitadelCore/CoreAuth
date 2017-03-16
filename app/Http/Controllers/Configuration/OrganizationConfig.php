<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Configuration;
use App\Http\Controllers\Command\CoreCommand;

class OrganizationConfig extends Controller implements Configuration {
  static function GetConfig() {
    return [
      "AuthProcessor" => "mysql",
      "ApiKey" => CoreCommand::GetApiKey(),

      "Production" => CoreCommand::GetProduction(),

      // LDAP (Lightweight Directory Access Protocol) settings below
      // Special configuration required for OpenLDAP.
      // Active Directory should work out of the box.
      "LdapType"                 => "openldap",
      "LdapBaseDN"               => "dc=core,dc=towerdevs,dc=xyz",
      "LdapDomainControllers"    => ["core.towerdevs.xyz"],
      "LdapAdminUsername"        => "cn=admin,dc=core,dc=towerdevs,dc=xyz", // Needs to be full Base DN for OpenLDAP!
      "LdapAdminPassword"        => "YCZuICPMNBe50mwgOCyMON9bT",
      "LdapAccountPrefix"        => "",
      "LdapAccountSuffix"        => "",
      "LdapAdminAccountSuffix"   => "",
      "LdapPort"                 => 389,
      "LdapFollowReferrals"      => false,
      "LdapUseSSL"               => false,
      "LdapUseTLS"               => true,
      "LdapTimeout"              => 5,
    ];
  }

  static function GetStaticConfig() {
    return [
      "OrganizationName" => "CoreNIC",
      //"MasterServer" => "https://central.auth.core:43106", // Do not change.
      "MasterServer" => "https://localhost:43106", // For development only!
      "LicenseSerial" => "6",
      "LicenseKey" => "a8f41506f89413add419db39e1b8a9db0e9f3f2166c54195c1bf27563e85",
    ];
  }
}
?>
