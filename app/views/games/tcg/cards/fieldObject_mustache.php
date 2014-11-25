<?php /*
 <div class="name" >{{card.unit.config.name}}({{card.id}})</div>

 <div class="keywords">
                {{#card.unit.keywords}}
                    <span class="keyword">{{.}}</span>
                {{/card.unit.keywords}}
            </div>
 */ ?>

<script id="template-field-object" type="x-tmpl-mustache">
    <div class="object id_{{object.id}} x_{{object.x}} y_{{object.y}}" data-id="{{object.id}}" data-x="{{object.x}}" data-y="{{object.y}}" data-active="false"
    style="background-image:url('/images/game/tcg/{{object.config.image}}')">

    </div>
</script>