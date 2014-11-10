<script id="template-hand-card" type="x-tmpl-mustache">

<div class="card my-card id_{{card.id}}" data-id="{{{card.id}}}" data-spelltype="{{{card.spell.config.type}}}">
    {{#isDeploy}}
	<div class="unit">
		<div class="name" >{{{card.unit.config.name}}}({{{card.id}}})</div>
		<div class="health-total">
			<i class="fa fa-heart"></i>
            <span class="value" >
			    {{{card.unit.config.totalHealth}}}
            </span>
		</div>
        <div class="attack">
            <i class="fa fa-gavel"></i>
            <span class="value">
			    {{{card.unit.config.attack.0}}}-
                {{{card.unit.config.attack.1}}}
            </span>
        </div>
		<p>
			{{{card.unit.config.text}}}
		</p>
	</div>
    {{/isDeploy}}
	<div class="spell">
		<a class="cast btn btn-warning btn-xs" style="display:none">Cast</a>
		<div class="name" >{{{card.spell.config.name}}}({{{card.id}}})</div>
		<p>
			{{{card.spell.config.text}}}
		</p>
	</div>
</div>

</script>