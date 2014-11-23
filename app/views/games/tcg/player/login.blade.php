@extends('layout.tcg.main')

@section('content')

<div class="hero-unit">
    <h1>Login <small></small></h1>
</div>

<form class="form-horizontal" role="form" method="post" action="/tcg/login/submit">
    @if (isset($formErrors))
    <div class="">
        @foreach ($formErrors as $error)
            <div class="alert alert-{{$error['type']}}" role="alert">{{$error['message']}}</div>
        @endforeach
    </div>
    @endif
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" name="email" id="inputEmail3" placeholder="Email">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password" id="inputPassword3" placeholder="Password">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Log In</button>
        </div>
    </div>
</form>


@stop

@section('sidebar')

<h3>Info</h3>
To create a new account
<a class="btn btn-primary" href="/tcg/register">Register</a><br><br>

@stop