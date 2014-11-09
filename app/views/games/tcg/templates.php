<script id="template" type="x-tmpl-mustache">
Hello {{ name }}!

@if (!empty($card['unit']['armor']))
        <span class="armor">
            <i class="fa fa-shield"></i>
            <span class="value">
            {{{$card['unit']['armor']}}}
            </span>
        </span>
        @endif

        @if ($card['unit']['keywords'])
        <div class="keywords">
            @foreach ($card['unit']['keywords'] as $keyword)
                <span class="keyword">{{$keyword}}</span>
            @endforeach
        </div>
        @endif
</script>

<script id="template-field-unit" type="x-tmpl-mustache">
    <div class="card unit id_{{card.id}} x_{{x}} y_{{y}}" data-id="{{card.id}}" data-x="{{x}}" data-y="{{y}}">
        <div class="name" >{{card.unit.config.name}}({{card.id}})</div>
        <span class="health">
            <i class="fa fa-heart-o"></i>
            <span class="value">
            {{card.unit.currentHealth}}
            </span>
        </span>



        <div class="attack">
            <i class="fa fa-gavel"></i>
                <span class="value">
                    {{card.unit.attack.0}}-
                    {{card.unit.attack.1}}
                </span>
        </div>



        <a class="skip btn btn-warning btn-xs" >Attack</a>
    </div>
</script>


<?php /*
function loadUser() {
    var template = $('#template').html();
    Mustache.parse(template);   // optional, speeds up future uses
    var rendered = Mustache.render(template, {name: "Luke"});
    $('#target').html(rendered);
}
 */ ?>
