
<div class="deck_builder-card-container card-id-{{$card['cardId']}}" data-cardid="{{$card['cardId']}}">
@foreach ($card['ids'] as $cardId)
    @if (empty($mode) || $mode != 'empty' || (!empty($show) && in_array($cardId, $show)))
        @if (empty($hide) || !in_array($cardId, $hide))
            <div class="non-game-card-in-list id_{{$cardId}}" data-id="{{$cardId}}">
                <div class="image" style="background-image:url('/images/game/tcg/{{$card['image']}}')"></div>
            </div>
        @endif
    @endif
@endforeach

</div>
