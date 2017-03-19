@extends('interface/gui_template')

@section('dialog')
<form method="post">
    <h2 class="sr-only">Change Password</h2>
    <div class="illustration"><i class="icon ion-lock-combination"></i></div>
    <div class="form-group">
        <input class="form-control" type="email" name="email" required="" placeholder="Username" maxlength="30" minlength="1">
    </div>
    <div class="form-group">
        <input class="form-control" type="password" name="password" required="" placeholder="Old Password" maxlength="100" minlength="5">
    </div>
    <input class="form-control" type="password" name="newpassword" required="" placeholder="New Password" maxlength="100" minlength="5">
    <input class="form-control" type="password" name="newpassword_c" required="" placeholder="Confirm New Password" maxlength="100" minlength="5">
    <div class="form-group">
        <button class="btn btn-primary btn-block" type="submit">Change Password</button>
    </div><a href="#" class="forgot">Contact Support</a></form>
@endsection
