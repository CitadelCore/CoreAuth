<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoreAuth</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/CoreNIC-Header.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body onload="submitQuery()">
    <nav class="navbar navbar-default navigation-clean">
        <div class="container">
            <div class="navbar-header"><a class="navbar-brand navbar-link" href="index.php">CoreAuth SSO</a>
                <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            </div>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav navbar-right"></ul>
            </div>
        </div>
    </nav>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center">Please wait. CoreAuth is signing you in.</h1></div>
            </div>
        </div>
    </div>
    <h2 class="text-center">Verifying your credentials with the server.</h2>
    <div class="modal fade" role="dialog" id="error_modal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">CoreAuth </h4></div>
                <div class="modal-body">
                    <p>An internal error has occured and you are unable to be logged in. This incident has been logged and will be reported.</p>
                    <p class="text-danger">Incident identifier: <a id="error_ic"></a></p>
                </div>
                <div class="modal-footer">
                    <a href="{{ $callback }}"><button class="btn btn-default" type="button">Return to sign-in</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="re_warning" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">CoreAuth RiskEngine</h4></div>
                <div class="modal-body">
                    <p>RiskEngine has detected a security discrepancy. You will be allowed to continue your sign-in, but this incident has been logged and will be reported.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="document.getElementById('callback_form').submit();"> Continue</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="re_challenge" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">CoreAuth RiskEngine</h4></div>
                <div class="modal-body">
                    <p>Because you may be logging in from a new location, or there is something RiskEngine dosen't recognize, you are required to enter the 6-digit PIN from your mobile authentication app.</p>
                    <input type="text" required="" placeholder="Authentication PIN"
                    maxlength="6" minlength="6">
                </div>
                <div class="modal-footer">
                    <a href="{{ $callback }}"><button class="btn btn-default" type="button">Return to sign-in</button></a>
                    <button class="btn btn-primary" type="button"> Continue</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="re_error" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">CoreAuth RiskEngine</h4></div>
                <div class="modal-body">
                    <p>RiskEngine has prohibited your login due to a security discrepancy. This incident has been logged and will be reported. Please try logging in later.</p>
                    <p class="text-danger">Incident identifier: <a id="re_error_ic"></a></p>
                </div>
                <div class="modal-footer">
                    <a href="{{ $callback }}"><button class="btn btn-default" type="button">Return to sign-in</button></a>
                </div>
            </div>
        </div>
    </div>
    <form id="metaform">
      <input type="hidden" value="{{ $callback }}" id="callback"></input>
      <input type="hidden" value="{{ $username }}" id="username"></input>
      <input type="hidden" value="{{ $password }}" id="password"></input>
      <input type="hidden" id="mfatoken"></input>
    </form>
    <form id="callback_form" method="post" action="{{ $callback }}">
      {{ csrf_field() }}
      <input type="hidden" name="token" id="token"></input>
    </form>
    <form id="callback_error_form" method="post" action="{{ $callback }}">
      {{ csrf_field() }}
      <input type="hidden" name="incident_id" id="incident_id"></input>
    </form>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/interface_login.js"></script>
    <script type="text/javascript">
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    </script>
</body>

</html>
