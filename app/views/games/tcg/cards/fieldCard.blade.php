
<div class="card unit id_{{$card['id']}} {{$isFocus ? 'focus' : ''}}" data-id="{{{$card['id']}}}">
	<div class="name" >{{{$card['name']}}}({{{$card['id']}}})</div>
    <div class="health">
        <i class="fa fa-heart"></i>
        {{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TOTAL_HEALTH]}}}
        <i class="fa fa-heart-o"></i>
        {{{$card['unit']['currentHealth']}}}
    </div>
    <a class="skip {{$isFocus ? '' : 'hidden'}} btn btn-warning btn-xs" >Attack</a>
</div>