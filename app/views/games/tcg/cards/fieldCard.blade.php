
<div class="card unit id_{{$card['id']}} {{$isFocus ? 'focus' : ''}}" data-id="{{{$card['id']}}}">
	<div class="name" >{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_NAME]}}}({{{$card['id']}}})</div>
    <div class="health-total">
        <i class="fa fa-heart"></i>
        <span class="value">
        {{{$card['unit']['maxHealth']}}}
        </span>
    </div>
    <div class="health">
        <i class="fa fa-heart-o"></i>
        <span class="value">
        {{{$card['unit']['currentHealth']}}}
        </span>
    </div>
    @if (!empty($card['unit']['armor']))
    <div class="armor">
        <i class="fa fa-shield"></i>
        <span class="value">
        {{{$card['unit']['armor']}}}
        </span>
    </div>
    @endif
    <div class="attack">
        <i class="fa fa-gavel"></i>
            <span class="value">
			    {{{$card['unit']['attack'][0]}}}-
                {{{$card['unit']['attack'][1]}}}
            </span>
    </div>
    <a class="skip {{$isFocus ? '' : 'hidden'}} btn btn-warning btn-xs" >Attack</a>
</div>