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
              if ($data['attributes']['response_code'] == "key_accepted") {
                return true;
              } else {
                throw new LicenseException("Master Server response code error.");
              }
            } elseif ($data['type'] == "error") {
              if ($data['attributes']['error_code'] == "org_error") {
                throw new LicenseException("The organization you entered does not exist.");
              } elseif ($data['attributes']['error_code'] == "serial_error") {
                throw new LicenseException("Invalid license serial.");
              } elseif ($data['attributes']['error_code'] == "key_error") {
                throw new LicenseException("Invalid license key.");
              } elseif ($data['attributes']['error_code'] == "key_expired") {
                throw new LicenseException("License key expired.");
              } elseif ($data['attributes']['error_code'] == "key_disabled") {
                throw new LicenseException("License key disabled.");
              } elseif ($data['attributes']['error_code'] == "key_noslots") {
                throw new LicenseException("No license slots left.");
              } elseif ($data['attributes']['error_code'] == "serverhn_error") {
                throw new LicenseException("Server hostname does not match.");
              } elseif ($data['attributes']['error_code'] == "param_error") {
                throw new LicenseException("Not enough parameters provided.");
              } elseif ($data['attributes']['error_code'] == "security_error") {
                throw new LicenseException("Master Server security error. Please contact TOWER Support.");
              } else {
                throw new LicenseException("Master Server response code error.");
              }
            } else {
              throw new LicenseException("Master Server response error:" . $data);
            }
          } else {
            throw new LicenseException("Master Server internal error:" . $return);
          }
        } else {
          throw new LicenseException("License Key is not set.");
        }
      } else {
        throw new LicenseException("License Serial is not set.");
      }
    } else {
      throw new LicenseException("Master Server URL is not set.");
    }
  }
}
 ?>
