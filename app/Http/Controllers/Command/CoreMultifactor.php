<?php

/**
 * This file contains the code required to communicate with
 * the Core^2 multi-factor authentication backend.
 *
 * @author Joseph Marsden <josephmarsden@towerdevs.xyz>
 * @copyright 2017 CoreNIC
 * @license https://central.core/licenses/coreauth.php
*/

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Configuration\OrganizationConfig;
use App\Http\Controllers\WebInterface\ResponseHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Command\CoreRequestor;
use Illuminate\Support\Facades\Log;

class CoreMultifactor extends CoreRequestor {
  public $username;
  public $token;

  public function ProvisionMfa() {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
    ];

    if (CoreCommand::Get2FAAllowed() == true) {
      $result = $this->PostServer($params, "mfaprov");
      if ($result['attributes']['response_code'] == "2fa_provisioned") {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function EnableMfa() {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
      'Token' => $this->token,
    ];

    if (CoreCommand::Get2FAAllowed() == true) {
      $result = $this->PostServer($params, "mfaenbl");
      if ($result['attributes']['response_code'] == "2fa_enabled") {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function PurgeMfa() {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
    ];

    if (CoreCommand::Get2FAAllowed() == true) {
      $result = $this->PostServer($params, "mfaprge");
      if ($result['attributes']['response_code'] == "2fa_purged") {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function MfaInfo() {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
    ];

    if (CoreCommand::Get2FAAllowed() == true) {
      $result = $this->PostServer($params, "mfainfo");
      if ($result['attributes']['response_code'] == "query_accepted") {
        return $result['payload'];
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function CheckToken() {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
      'Token' => $this->token,
    ];

    if (CoreCommand::Get2FAAllowed() == true) {
      $result = $this->PostServer($params, "mfacheck");
      if ($result['attributes']['response_code'] == "2fa_valid") {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

?>
