

<script id="template-four-pictures" type="x-tmpl-mustache">
    <div class="question" >

        <div class="row">
            <div class="col-md-12 name">
                {{question.name}}
            </div>
        </div>
        <div class="options">
            <div class="row">
                <div class="col-md-6 answer image" data-id="0" style="background-image: url('{{question.options.0}}')">
                    {{question.options.0}}
                </div>
                <div class="col-md-6 answer image" data-id="1" style="background-image: url('{{question.options.1}}')">
                    {{question.options.1}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 answer image" data-id="2" style="background-image: url('{{question.options.2}}')">
                    {{question.options.2}}
                </div>
                <div class="col-md-6 answer image" data-id="3" style="background-image: url('{{question.options.3}}')">
                    {{question.options.3}}
                </div>
            </div>
        </div>

    </div>

</script>