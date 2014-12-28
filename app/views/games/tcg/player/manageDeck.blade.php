@extends('layout.tcg.main')

@section('content')

<div class="hero-unit">
    <h1>
        @if ($deck)
        @lang('tcg.edit_deck_title_1') <small>@lang('tcg.edit_deck_title_2')</small>
        @else
        @lang('tcg.create_deck_title_1') <small>@lang('tcg.create_deck_title_2')</small>
        @endif
    </h1>
</div>

<form class="form-horizontal" role="form" method="post" action="/tcg/{{$deck ? 'changeDeck' : 'createDeck'}}/submit">
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
        <label for="inputName" class="col-sm-2 control-label">@lang('tcg.name')</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="name" value="{{isset($deck['name'])?$deck['name']:''}}" id="inputName">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-success">@lang('tcg.deck_manage_save')</button>
        </div>
    </div>
    @if (!empty($deckId))
        <input type="hidden" name="deckId" value="{{$deckId}}" />
    @endif
    @foreach ($kings as $kingCard)
        @if ($deck && $kingCard['cardId'] == $deck['kingId'])
            @include('games.tcg.cards.nonGameCard', array('card' => $kingCard, 'mode' => 'form', 'selected' => true))
        @else
            @include('games.tcg.cards.nonGameCard', array('card' => $kingCard, 'mode' => 'form'))
        @endif

    @endforeach
</form>

<script>
    $(document).ready(function() {
        initFormCard();
    });
</script>

@stop

@section('sidebar')

<h3>Info</h3>

@if ($deck)
    <a href="/tcg/deck/{{$deckId}}" class="btn btn-primary">@lang('tcg.back')</a>
@endif

@stop