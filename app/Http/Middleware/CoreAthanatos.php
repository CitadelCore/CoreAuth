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

class CoreAthanatos {
  public function handle($request, Closure $next) {
    // Begin with validating the CoreAuth license.
    CoreLicensor::ValidateLicense();
    return $next($request);
  }
}

 ?>
