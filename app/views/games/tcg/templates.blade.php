@include('games.tcg.cards.fieldCard_mustache')
@include('games.tcg.cards.fieldObject_mustache')
@include('games.tcg.cards.orderCard_mustache')
@include('games.tcg.cards.card_mustache')


<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="result-title">
                    Your base belong to enemy now!
                </h2>
                <p id="gameStats" class="result-text">
                    You survived - <span class="result-numbers" id="turnsSurvived"></span> turns!<br>
                    You killed - <span class="result-numbers" id="unitsKilled"></span> units!<br>
                    You earned - <span class="result-numbers" id="pointsEarned"></span> points!<br>
                </p>
                @if (empty($userName))
                <br>
                <p id="MENameFormContainer">
                <form id="MENameForm" class="result-text" method="post" action="{{ action('MathEffectController@saveName') }}">
                    <input type="text" name="name" />
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">s

                    <button class="btn btn-primary" type="submit">Save my name</button>
                </form>
                </p>
                @endif
            </div>

            <!-- dialog buttons -->
            <div class="modal-footer"><a href="{{ action('MathEffectController@index') }}" type="button" class="btn btn-primary">Restart</a></div>
        </div>
    </div>
</div>