

<a id="start-game" class="btn btn-primary btn-lg btn-block"> Start </a>

<div class="game-container" style="display:none">

	<div class="turn-area"></div>
	<div class="row">
		<div class="col-md-2">
			<div>Points - <span class="points-amount">0</span>
			</div>
		</div>
	    <div class="col-md-10 timer">
	    	<div class="progress">
			  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
			  	<div class="seconds">
	        		<div class="text"></div>
	        	</div>
			    <span class="sr-only">40% Complete (success)</span>
			  </div>
			</div>
	        	
	        
	    </div>
	</div>
</div>



<script>
	$(document).ready(function() {
        Guess.Game.init({{$firstQuestion}});
	});
</script>