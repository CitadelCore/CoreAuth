<?php

/**
 * Class for validating the CoreAuth software license.
 * Any modification of this file is prohibited.
 * By the way, don't waste your time trying to crack this.
 * All configuration information is stored in the master server anyway.
 *
 * @author Joseph Marsden <josephmarsden@towerdevs.xyz>
 * @copyright 2017 CoreNIC
 * @license https://central.core/licenses/coreauth.php
*/

namespace App\Http\Controllers\Command;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Configuration\OrganizationConfig;
use \GuzzleHttp\Exception\RequestException;

class CoreLicensor extends Controller {
  static function ValidateLicense() {
    $request = new \GuzzleHttp\Client();
    $config = OrganizationConfig::GetStaticConfig();
    if (isset($config["MasterServer"])) {
      if (isset($config["LicenseSerial"])) {
        if (isset($config["LicenseKey"])) {
          try {
            $response = $request->request(
              'POST',
              $config["MasterServer"] . "/ca/v1/licensor",
              [
                'verify' => false,
                'form_params' => [
                  'Organization' => $config["OrganizationName"],
                  'OrganizationKey' => $config["OrganizationKey"],
                  'LicenseSerial' => $config["LicenseSerial"],
                  'LicenseKey' => $config["LicenseKey"],
                  'ServerHostname' => gethostname(),
                ]
              ]
            );
          } catch (RequestException $e) {
            return "Failed to connect to the Core^2 server: $e. Resolution: Contact TOWER Support.";
          }
          $return = $response->getBody();
          $data = json_decode($return, true);
          if (json_last_error() == JSON_ERROR_NONE) {
            if ($data['type'] == "response") {
              if ($data['attributes']['response_code'] == "key_accepted") {
                return "no_error";
              } else {
                return "Core^2 response code error. Resolution: Contact TOWER Support.";
              }
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
              return "Core^2 response error:" . $data . " Resolution: Contact TOWER Support.";
            }
          } else {
            return "Core^2 internal error:" . $return . " Resolution: Contact TOWER Support.";;
          }
        } else {
          return "No license key provided. Resolution: Set a license key in the server configuration.";
        }
      } else {
        return "No license serial provided. Resolution: Set a license serial in the server configuration.";
      }
    } else {
      return "Core^2 URL is not set. Resolution: Contact TOWER Support.";
    }
  }
}
 ?>
