

<script id="template-single-picture" type="x-tmpl-mustache">
    <div class="question" >

        <div class="image single-picture" style="background-image: url('{{question.picture}}')">
        </div>
        <div class="options">
            <div class="row">
                <div class="col-md-6 answer" data-id="0">
                    {{question.options.0}}
                </div>
                <div class="col-md-6 answer" data-id="1">
                    {{question.options.1}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 answer" data-id="2">
                    {{question.options.2}}
                </div>
                <div class="col-md-6 answer" data-id="3">
                    {{question.options.3}}
                </div>
            </div>
        </div>

    </div>

</script>