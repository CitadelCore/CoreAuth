<?php

/**
 * This file contains the code required to communicate with
 * the Core^2 security backend, if you have a subscription for it.
 * This also acts as middleware to enforce the RiskEngine policies.
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

class RiskEngine extends CoreRequestor {
  public $username;
  public $status;

  public function SendAppend($vname, $vdata) {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
      'Risk' => $vname,
      'RiskData' => $vdata,
    ];

    if (CoreCommand::GetRiskEngineAllowed() == true) {
      $result = $this->PostServer($params, "re/append");
      if (isset($result['attributes']['response_code'])) {
        if ($result['attributes']['response_code'] == "riskengine_success") {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function SendQuery() {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $this->username,
    ];

    if (CoreCommand::GetRiskEngineAllowed() == true) {
      $result = $this->PostServer($params, "re/query");
      return $result['attributes']['response_code'];
    } else {
      return false;
    }
  }

  public function UpdateStatus() {
    $config = OrganizationConfig::GetStaticConfig();
    if (isset($this->status)) {
      if (CoreCommand::GetRiskEngineAllowed() == false) {
        $this->status = "disabled";
      }
    } else {
      $this->status = "disabled";
    }
  }

}

 ?>
