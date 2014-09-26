@extends('layout.head')

@section('layout')

<?//= Csrf::createInput() ?>
<? //$this->render('menu.tpl') ?>

@include('menu')

<div class="container main">
    <div class="row">
        <div class="span12">
            <div class="main-content-well well well-small ">
                @yield('content')
            </div>
        </div>

    </div>
</div>

@include('modal.modal')
<?//= Js::getHtml() ?>

@stop

