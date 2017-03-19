@extends('interface/gui_template')

@section('dialog')
<form method="post">
    <h2 class="sr-only">Enable Multi-Factor</h2>
    <div class="illustration"><i class="icon ion-iphone"></i></div>
    <p class="text-center text-primary">Multi-Factor Authentication</p>
    <div class="form-group"></div>
    <div class="form-group"></div>
    <div class="form-group">
        <button class="btn btn-primary btn-block" type="submit">Enable Multi-Factor</button>
    </div><a href="#" class="forgot">Contact Support</a></form>
@endsection
