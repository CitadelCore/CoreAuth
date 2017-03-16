<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreAuth</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/CoreNIC-Header.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <nav class="navbar navbar-default navigation-clean">
        <div class="container">
            <div class="navbar-header"><a class="navbar-brand navbar-link">CoreAuth SSO</a>
                <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            </div>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav navbar-right"></ul>
            </div>
        </div>
    </nav>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/testlogin.js"></script>
</body>
<div>Token: {{ $token }}</div>
<div>Incident ID: {{ $incident_id }}</div>
<div>Debug: {{ $debug }}</div>
<form action="https://localhost:4434/endpoints/login" id="testlogin" method="post">
  <div class="form-group">
    <label for="username">Username:</label>
    <input type="email" class="form-control" name="username" id="username"></input>
  </div>
  <div class="form-group">
    <label for="password">Password:</label>
    <input type="password" class="form-control" name="password" id="password"></input>
  </div>
  <div class="form-group">
    <label for="disabled">Disabled:</label>
    <input class="form-control" name="disabled" id="disabled" value="0"></input>
  </div>
  <input type="hidden" name="callback" id="callback" value="https://localhost:4434/developer/testlogin"></input>
  <button>Submit</button>
</form>
</html>
