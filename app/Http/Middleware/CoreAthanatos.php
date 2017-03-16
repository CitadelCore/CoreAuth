<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Command\CoreLicensor;

class CoreAthanatos {
  public function handle($request, Closure $next) {
    // Begin with validating the CoreAuth license.
    CoreLicensor::ValidateLicense();
  }
}

 ?>
