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
    return self::SendCommand('apikey');
  }

  static function GetProduction() {
    return self::SendCommand('production');
  }

  static function Get2FAAllowed() {
    return self::SendCommand('allow_2fa');
  }

  static function GetExtSSOAllowed() {
    return self::SendCommand('allow_extsso');
  }

  static function GetRiskEngineAllowed() {
    return self::SendCommand('allow_riskengine');
  }

  static function GetApiAllowed() {
    return self::SendCommand('allow_api');
  }

  static function SendCommand($param) {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'VariableName' => $param,
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
    ];

    $urlsuffix = "varstore";

    return self::ServerCommand($params, $urlsuffix, $param);
  }

  static function RemoveUser($username) {
    $config = OrganizationConfig::GetStaticConfig();
    $params = [
      'VariableName' => $param,
      'Organization' => $config["OrganizationName"],
      'OrganizationKey' => $config["OrganizationKey"],
      'LicenseSerial' => $config["LicenseSerial"],
      'LicenseKey' => $config["LicenseKey"],
      'ServerHostname' => gethostname(),
      'Username' => $username,
    ];

    $urlsuffix = "deluser";

    return self::ServerCommand($params, $urlsuffix);
  }

  static function ServerCommand($params, $urlsuffix, $variable = "none") {
    CoreLicensor::ValidateLicense();
    $request = new \GuzzleHttp\Client();
    $config = OrganizationConfig::GetStaticConfig();
    try {
      $response = $request->request(
        'POST',
        $config["MasterServer"] . "/ca/v1/$urlsuffix",
        [
          'verify' => false,
          'form_params' => $params,
        ]
      );
    } catch (RequestException $e) {
      return "Failed to connect to the Core^2 server: $e. Resolution: Contact TOWER Support.";
    }
    $return = $response->getBody();
    $data = json_decode($return, true);
    if (json_last_error() == JSON_ERROR_NONE) {
      if ($data['type'] == "response") {
        if ($data['payload'] != null) {
          return $data['payload'][$variable];
        } elseif ($data['attributes']['response_code'] != null) {
          return $data['attributes']['response_code'];
        } elseif ($data['type'] == "error") {
          if ($data['attributes']['error_code'] == "org_error") {
            return "An invalid organization is configured. Resolution: Verify the organization name in the server configuration.";
          } elseif ($data['attributes']['error_code'] == "org_key_error") {
            return "An invalid organization key is configured. Resolution: Verify the organization key in the server configuration.";
          } elseif ($data['attributes']['error_code'] == "serial_error") {
            return "An invalid license serial is configured. Resolution: Verify the license serial in the server configuration.";
          } elseif ($data['attributes']['error_code'] == "key_error") {
            return "An invalid license key is configured. Resolution: Verify the license key in the server configuration.";
          } elseif ($data['attributes']['error_code'] == "key_expired") {
            return "The license key is expired. Resolution: Renew the license key.";
          } elseif ($data['attributes']['error_code'] == "key_disabled") {
            return "License key disabled. Resolution: Contact TOWER Support to have your license reactivated.";
          } elseif ($data['attributes']['error_code'] == "key_noslots") {
            return "No license slots left. Resolution: Remove a server to free up a license slot, or purchase another slot.";
          } elseif ($data['attributes']['error_code'] == "serverhn_error") {
            return "Server hostname does not match. Resolution: Contact TOWER Support.";
          } elseif ($data['attributes']['error_code'] == "param_error") {
            return "Not enough parameters provided. Resolution: Contact TOWER Support.";
          } elseif ($data['attributes']['error_code'] == "security_error") {
            return "A security error occured and CoreAuth cannot verify the status of the license. Resolution: Contact TOWER Support.";
          } else {
            return "Core^2 response code error. Resolution: Contact TOWER Support.";
          }
        } else {
          throw new LicenseException("Error retrieving the value of a system variable.");
        }
        } else {
          throw new LicenseException("Error retrieving the value of a system variable.");
        }
    } else {
      throw new LicenseException("Error retrieving the value of a system variable.");
    }
  }
}
?>
