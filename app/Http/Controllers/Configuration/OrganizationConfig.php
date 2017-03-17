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
      "OrganizationName" => "CoreNIC", // Your organization's name.
      "OrganizationKey" => "2a9fb648e7e180f44a078496b2d599c61784c5cdd028b8859bd79516300d", // Your organization's key.

      //"MasterServer" => "https://central.auth.core:43106", // Do not change.
      "MasterServer" => "https://localhost:43106", // For development only!

      "LicenseSerial" => "1",
      "LicenseKey" => "047ce94623f0aa4ecc4721de1f76f08ce6574052ecebb697b345d3007cd1",
    ];
  }
}
?>
