

<div class="field">
<!--    <div class="cells">-->
    @for($y = 0; $y < $field['width']; $y++)
        @for($x = 0; $x < $field['width']; $x++)

            <div class="cell x_{{$x}} y_{{$y}}" style="top: {{ $y * 75 }}px;left: {{$x * 75}}px" data-x="{{$x}}" data-y="{{$y}}">

            </div>
        @endfor
    @endfor
<!--    </div>-->
    <div class="objects" >
    </div>
    <div class="units" >
    </div>
    <div class="clear" ></div>
</div>