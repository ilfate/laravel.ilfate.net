

<script id="template-sidebar-stats" type="x-tmpl-mustache">

    <table class="stats-table table table-striped">
      <thead>
        <tr>
            <th class="stats-table-title" colspan="3">Best players today</th>
        </tr>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
        {{#stats}}
          <tr>
              <td>{{key}}</td>
              <td>{{name}}</td>
              <th scope="row">{{points}}</th>
          </tr>
        {{/stats}}
        <tr>
          <td class="stats-table-more-button" colspan="3">
            <a class="btn btn-primary" href="/GuessSeries/stats">More statistic</a>
          </td>
        </tr>
      </tbody>
    </table>

</script>