

<script id="template-four-pictures" type="x-tmpl-mustache">
    <div class="question four-images" >

        <div class="row">
            <div class="col-md-12 name">
                <span>{{question.name}}</span>
            </div>
        </div>
        <div class="options">
            <div class="row">
                <div class="col-md-6 col-sm-6 answer image id-0" data-id="0" style="background-image: url('/images/game/guess/{{question.options.0}}')">
                    <div class="four-images-overlay"></div>
                </div>
                <div class="col-md-6 col-sm-6 answer image id-1" data-id="1" style="background-image: url('/images/game/guess/{{question.options.1}}')">
                    <div class="four-images-overlay"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 answer image id-2" data-id="2" style="background-image: url('/images/game/guess/{{question.options.2}}')">
                    <div class="four-images-overlay"></div>
                </div>
                <div class="col-md-6 col-sm-6 answer image id-3" data-id="3" style="background-image: url('/images/game/guess/{{question.options.3}}')">
                    <div class="four-images-overlay"></div>
                </div>
            </div>
        </div>

    </div>

</script>