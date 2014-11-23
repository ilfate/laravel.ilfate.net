@extends('layout.tcg.main')

@section('content')

<div class="hero-unit">
    <h1>Save <small>your progress</small></h1>
</div>

<form class="form-horizontal" role="form" method="post" action="/tcg/register/submit">
    @if ($errors->count())
    <?php
        $errorMessages = $errors->all();
    ?>
    <div class="">
        @foreach ($errorMessages as $error)
        <div class="alert alert-danger" role="alert">{{$error}}</div>
        @endforeach
    </div>
    @endif
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" value="{{isset($formDefaults['email'])?$formDefaults['email']:''}}" name="email" id="inputEmail3" placeholder="Email">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">@lang('tcg.password')</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password1" id="inputPassword3" placeholder="Password">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword4" class="col-sm-2 control-label">@lang('tcg.password_confirm')</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password2" id="inputPassword4" placeholder="Password">
        </div>
    </div>
    <div class="form-group">
        <label for="inputName" class="col-sm-2 control-label">@lang('tcg.name')</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="name" value="{{isset($formDefaults['name'])?$formDefaults['name']:$player['name']}}" id="inputName">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">@lang('tcg.sign_up')</button>
        </div>
    </div>
</form>


@stop

@section('sidebar')

<h3>Info</h3>
If you already have an account you can
<a class="btn btn-primary" href="/tcg/login">Log in</a><br><br>

@stop