
<script id="template-field-unit" type="x-tmpl-mustache">
    <div class="card unit id_{{card.id}} x_{{x}} y_{{y}}" data-id="{{card.id}}" data-x="{{x}}" data-y="{{y}}"  data-active="false">
        <div class="name" >{{card.unit.config.name}}({{card.id}})</div>
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

        <div class="attack">
            <i class="fa fa-gavel"></i>
                <span class="value">
                    {{card.unit.attack.0}}-
                    {{card.unit.attack.1}}
                </span>
        </div>

        <div class="keywords">
        {{#card.unit.keywords}}
            <span class="keyword">{{.}}</span>
        {{/card.unit.keywords}}
        </div>

        <div class="info" data-toggle="popover" data-trigger="hover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">
            <i class="fa fa-info-circle"></i>
        </div>

        <a class="skip btn btn-warning btn-xs" >Attack</a>
    </div>
</script>