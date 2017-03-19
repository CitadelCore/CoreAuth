@extends('interface/gui_template')

@section('dialog')
<form method="post">
    <h2 class="sr-only">Create Account</h2>
    <div class="illustration"><i class="icon ion-android-contacts"></i></div>
    <div class="form-group">
        <input class="form-control" type="email" name="email" required="" placeholder="Username" maxlength="30" minlength="1">
    </div>
    <div class="form-group">
        <input class="form-control" type="password" name="password" required="" placeholder="Password" maxlength="100" minlength="5">
    </div>
    <input class="form-control" type="password" name="password_c" required="" placeholder="Confirm Password" maxlength="100" minlength="5">
    <div class="form-group">
        <button class="btn btn-primary btn-block" type="submit">Create Account</button>
    </div><a href="#" class="forgot">Contact Support</a></form>
<div class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">An error occured</h4></div>
            <div class="modal-body">
                <p>An error occured while creating your CoreAuth account. The error was:</p>
                <p>error_text </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div
@endsection
