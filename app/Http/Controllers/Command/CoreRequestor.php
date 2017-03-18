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
use Illuminate\Support\Facades\Log;

class CoreRequestor extends Controller {
  protected function PostServer($params, $type) {
    $request = new \GuzzleHttp\Client();
    $config = OrganizationConfig::GetStaticConfig();
    if (isset($config["MasterServer"])) {
      if (isset($config["LicenseSerial"])) {
        if (isset($config["LicenseKey"])) {
          try {
            $response = $request->request(
              'POST',
              $config["MasterServer"] . "/ca/v1/" . $type,
              [
                'verify' => false,
                'form_params' => $params,
              ]
            );
          } catch (RequestException $e) {
            return "conn_error";
          }
          $return = $response->getBody();
          $data = json_decode($return, true);
          if (json_last_error() == JSON_ERROR_NONE) {
            if ($data['type'] == "response") {
              return $data;
            } elseif ($data['type'] == "error") {
              return $data;
            } else {
              return array("attributes"=>array("response_code"=>"type_error"));
            }
          } else {
            return array("attributes"=>array("response_code"=>"json_error"));
          }
        } else {
          return array("attributes"=>array("response_code"=>"no_licence"));
        }
      } else {
        return array("attributes"=>array("response_code"=>"no_serial"));
      }
    } else {
      return array("attributes"=>array("response_code"=>"no_url_set"));
    }
  }
}
