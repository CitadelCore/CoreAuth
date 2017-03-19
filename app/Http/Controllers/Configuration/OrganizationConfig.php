<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Configuration;
use App\Http\Controllers\Command\CoreCommand;

use App\Http\Controllers\Connectors\MysqlConnector;
use App\Http\Controllers\Connectors\LdapConnector;

class OrganizationConfig extends Controller implements Configuration {
  static function GetStaticConfig() {
    return [

      // Organization configuration
      "OrganizationName" => "CoreNIC", // Your organization's name.
      //"OrganizationKey" => "c86c83d2102d148edf2061809eadee9e605f2fe01a0afbf9980c81a06509", // Your organization's key.
      "OrganizationKey" => "91aefd4f3bee695e2939836cccbd8519e5d0dbe4a52b2ec7e67fbab8f29f",

      // License information.
      "LicenseSerial" => "1",
      //"LicenseKey" => "008fc7f4690a8e05e1215dd2fa164ce8418902517aa06d635198f7fe02ad",
      "LicenseKey" => "eb8266c37e5b2f7c23104b2b37abb66bc7d2259a46f1705ba1d033305dab",

      // Do not change the below settings under any circumstances, or your support will be voided.
      "MasterServer" => "https://auth.towerdevs.xyz", // Do not change.
      //"MasterServer" => "https://localhost:43106", // For development only!
    ];
  }

  static function GetConfig() {
    return [
      // Set this to the class of your authentication connector.
      "AuthProcessor" => new MysqlConnector,

      // Set this only if you want to switch to the non-commercial Free edition.
      // Please note that a lot of features are disabled in this edition.
      "EnableCommunity" => false,

      // Do not change the below settings under any circumstances, or your support will be voided.
      "ApiKey" => CoreCommand::GetApiKey(),
      "Production" => CoreCommand::GetProduction(),
    ];
  }

}
?>
