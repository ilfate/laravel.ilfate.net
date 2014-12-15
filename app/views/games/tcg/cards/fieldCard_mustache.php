<?php /*
 <div class="name" >{{card.unit.config.name}}({{card.id}})</div>

 <div class="keywords">
                {{#card.unit.keywords}}
                    <span class="keyword">{{.}}</span>
                {{/card.unit.keywords}}
            </div>

 */ ?>

<script id="template-field-unit" type="x-tmpl-mustache">
    <div class="card field-unit id_{{card.id}} x_{{x}} y_{{y}} {{isEnemy}}" 
            data-move="{{card.unit.moveType}}" 
            data-cardid="{{card.cardId}}" 
            data-id="{{card.id}}" 
            data-x="{{x}}" 
            data-y="{{y}}" 
            data-attackrange="{{card.unit.attackRange}}" 
            data-attacktype="{{card.unit.attackType}}" 
            data-active="false">
        <div class="image" style="background-image:url('/images/game/tcg/{{card.image}}')"></div>
        <div class="field-unit-attack-zone not-hidden attack-type-{{card.unit.attackType}}-{{card.unit.attackRange}}" ></div>
        <div class="unit-info">
            <span class="move">
                <i class="fa fa-shield"></i>
                <span class="value">
                {{card.unit.moveSteps}}
                </span>
            </span>
            <span class="attack-range">
                <i class="fa fa-gavel"></i>
                <span class="value">
                {{card.unit.attackRange}}
                </span>
            </span>
        </div>
        
        <div class="middle-panel">
            <div class="attack">
                <i class="fa fa-gavel"></i>
                    <span class="value">
                        {{card.unit.attack.0}}-
                        {{card.unit.attack.1}}
                    </span>
                </div>
            
        </div>
        <div class='health-panel'>
            <span class="health">
                <i class="fa fa-heart-o"></i>
                <span class="value">
                {{card.unit.currentHealth}}
                </span>
            </span>

            <span class="armor">
                <i class="fa fa-shield"></i>
                <span class="value">
                {{card.unit.armor}}
                </span>
            </span>
        </div>

        <a class="skip btn btn-warning btn-xs" >Attack</a>
    </div>
</script>