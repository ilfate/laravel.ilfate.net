
<div class="card unit id_{{$card['id']}} x_{{$x}} y_{{$y}}" data-id="{{{$card['id']}}}" data-x="{{$x}}" data-y="{{$y}}">
	<div class="name" >{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_NAME]}}}({{{$card['id']}}})</div>
    <span class="health">
        <i class="fa fa-heart-o"></i>
        <span class="value">
        {{{$card['unit']['currentHealth']}}}
        </span>
    </span>

    @if (!empty($card['unit']['armor']))
    <span class="armor">
        <i class="fa fa-shield"></i>
        <span class="value">
        {{{$card['unit']['armor']}}}
        </span>
    </span>
    @endif

    <div class="attack">
        <i class="fa fa-gavel"></i>
            <span class="value">
			    {{{$card['unit']['attack'][0]}}}-
                {{{$card['unit']['attack'][1]}}}
            </span>
    </div>

    @if ($card['unit']['keywords'])
    <div class="keywords">
        @foreach ($card['unit']['keywords'] as $keyword)
            <span class="keyword">{{$keyword}}</span>
        @endforeach
    </div>
    @endif

    <a class="skip btn btn-warning btn-xs" >Attack</a>
</div>