<?php

/**
 * This middleware initalizes CoreAuth and sets up everything
 * we need to connect to LDAP, databases, RiskEngine etc.
 *
 * @author Joseph Marsden <josephmarsden@towerdevs.xyz>
 * @copyright 2017 CoreNIC
 * @license https://central.core/licenses/coreauth.php
*/

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Command\CoreLicensor;
use App\Http\Controllers\Command\CoreCommand;
use App\Http\Controllers\Command\RiskEngine;
use App\Http\Controllers\WebInterface\ResponseHandler;

class CoreAthanatos {
  public function handle($request, Closure $next) {
    // Begin with validating the CoreAuth license.
    $return = CoreLicensor::ValidateLicense();
    if ($return != "no_error") {
      return response(view('interface/error', ['error'=>$return]));
    }

    // Then boot up the RiskEngine middleware, if the license supports it.
    if (CoreCommand::GetRiskEngineAllowed() == true) {
      $riskengine = new RiskEngine;
      if ($request->has("username") && $request->has("password")) {
        $riskengine->username = $request->input("username");
        $return = $riskengine->SendQuery();
        if ($return == "riskengine_allowed") {
          $request->session()->flash('riskengine_status', 'allowed');
          return $next($request);
        } elseif ($return == "riskengine_warning") {
          $request->session()->flash('riskengine_status', 'warning');
          return $next($request);
        } elseif ($return == "riskengine_2fa") {
          $request->session()->flash('riskengine_status', '2farequired');
          return $next($request);
        } elseif ($return == "riskengine_blocked") {
          $request->session()->flash('riskengine_status', 'blocked');
          return $next($request);
        } else {
          $request->session()->flash('riskengine_status', 'error');
          return $next($request);
        }
      } else {
        return $next($request);
      }
    }
  }
}

 ?>
