<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Configuration;

class OrganizationConfig extends Controller implements Configuration {
  static function GetConfig() {
    return [
      "OrganizationName" => "CoreNIC",
      "AuthProcessor" => "mysql",
      "ApiKey" => "rcmsHGV05hliWhsJJYF1OhcHo",
      "MasterServer" => "https://central.auth.core:43105",
      "Production" => false,

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
}
?>
