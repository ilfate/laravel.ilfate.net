

<script id="template-single-picture" type="x-tmpl-mustache">
    <div class="question" >

        <div class="image single-picture" style="background-image: url('/images/game/guess/{{question.picture}}')">
            <div class="single-picture-overlay"></div>
        </div>
        <div class="options names">
            <div class="row">
                <div class="col-md-6 answer name id-0" data-id="0">
                    <div class="block">
                    {{question.options.0}}
                    </div>
                </div>
                <div class="col-md-6 answer name id-1" data-id="1">
                    <div class="block">
                    {{question.options.1}}
                    </div>
                </div>
            </div>
            <div class="row name-options-second-row">
                <div class="col-md-6 answer name id-2" data-id="2">
                    <div class="block">
                    {{question.options.2}}
                    </div>
                </div>
                <div class="col-md-6 answer name id-3" data-id="3">
                    <div class="block">
                    {{question.options.3}}
                    </div>
                </div>
            </div>
        </div>

    </div>

</script>