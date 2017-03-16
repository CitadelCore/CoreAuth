<?php

/**
 * Class for communicating with the CoreAuth core server.
 * Any modification of this file is prohibited.
 *
 * @author Joseph Marsden <josephmarsden@towerdevs.xyz>
 * @copyright 2017 CoreNIC
 * @license https://central.core/licenses/coreauth.php
*/

namespace App\Http\Controllers\Command;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Configuration\OrganizationConfig;
use \GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Command\CoreLicensor;

class CoreCommand extends Controller {
  static function GetApiKey() {
    return self::GetVariable('apikey');
  }

  static function GetProduction() {
    return self::GetVariable('production');
  }

  static function Get2FAAllowed() {
    return self::GetVariable('allow_2fa');
  }

  static function GetExtSSOAllowed() {
    return self::GetVariable('allow_extsso');
  }

  static function GetRiskEngineAllowed() {
    return self::GetVariable('allow_riskengine');
  }

  static function GetApiAllowed() {
    return self::GetVariable('allow_api');
  }

  static function GetVariable($variable) {
    CoreLicensor::ValidateLicense();
    $request = new \GuzzleHttp\Client();
    $config = OrganizationConfig::GetStaticConfig();
    try {
      $response = $request->request(
        'POST',
        $config["MasterServer"] . "/ca/v1/varstore",
        [
          'verify' => false,
          'form_params' => [
            'VariableName' => $variable,
            'Organization' => $config["OrganizationName"],
            'LicenseSerial' => $config["LicenseSerial"],
            'LicenseKey' => $config["LicenseKey"],
            'ServerHostname' => gethostname(),
          ]
        ]
      );
    } catch (RequestException $e) {
      throw new LicenseException("Failed to connect to the controller server: $e");
    }
    $return = $response->getBody();
    $data = json_decode($return, true);
    if (json_last_error() == JSON_ERROR_NONE) {
      if ($data['type'] == "response") {
      } else {
        if ($data['payload'][$variable] != null) {
          return $data['payload'][$variable];
        } else {
          return false;
        }
      }
    } else {
      return false;
    }
  }
}
?>
