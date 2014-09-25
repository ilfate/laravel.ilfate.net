@extends('layout.head')

@section('layout')

<?//= Csrf::createInput() ?>
<? //$this->render('menu.tpl') ?>

@include('menu')



<div class="container main">
    <div class="row">
        <div class="col-md-9">
            <div class="main-content-well well well-small ">
                @yield('content')
            </div>
        </div>
        <div class="col-md-3">
            @include('sidebar')
        </div>
    </div>
</div>

@include('modal.modal')
<?//= Js::getHtml() ?>

@stop

