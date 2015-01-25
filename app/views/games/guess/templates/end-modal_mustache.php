

<script id="template-end-modal" type="x-tmpl-mustache">

    <div class="end-modal col-md-4 col-md-offset-4" >

        <form id="MENameForm" class=" ajax result-text" method="post" action="/GuessSeries/saveName">
            <input type="text" class="form-control" name="name" />
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <button class="btn btn-primary" type="submit">Save my name</button>
        </form>

    </div>
    <div class="clear"></div>
</script>