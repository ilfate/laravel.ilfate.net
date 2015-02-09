

<script id="template-end-modal" type="x-tmpl-mustache">



    <span class="points-amount">
        <span class="left"></span>
        <span class="number">{{number}}</span>
        <span class="right"></span>
    </span>
    <br>
    <span class="rest-stats-text"></span>
    {{#userName}}
    <br><span class="modal-user-name"></span>
    {{/userName}}
    <br>

    {{^userName}}
    <div class="clear"></div>
    <div class="end-modal col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4" >

        <form id="MENameForm" class=" ajax result-text" method="post" action="/GuessSeries/saveName">
            <input type="text" class="form-control" name="name" />
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <button class="btn btn-primary" type="submit">Save my name</button>
        </form>

    </div>
    {{/userName}}
    <div class="clear"></div>
    <div>Plz share the game with your friends!</div>
    <div class="facebook-placeholder col-md-2 col-md-offset-5 col-sm-2 col-sm-offset-5">
    </div>
    <div class="clear"></div>
    <a class="btn btn-primary restart-button" href="/GuessSeries" style="display: inline;">Restart</a>
</script>