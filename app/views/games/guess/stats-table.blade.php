


<table class="table table-striped">
    <thead>
        <tr>
            <th class="table-title" colspan="3">{{$title}}</th>
        </tr>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Points</th>
        </tr>
    </thead>
    <tbody>
        @foreach($table as $row)
        <tr>
            <td>{{$row['key']}}</td>
            <td>{{{$row['name'] ?: '-----'}}}</td>
            <td>{{$row['points']}}</td>
            @endforeach
        </tr>
    </tbody>
</table>


