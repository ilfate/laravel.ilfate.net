@extends('layout.guess.main-admin')

@section('content')
<br><br>
<div class="row">
    <div class="col-md-8">
        <form class="form-horizontal" role="form" method="post" action="/GuessSeries/admin/addSeries">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="" name="name" id="inputEmail3" placeholder="Name">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Year</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="year" id="inputPassword3" placeholder="Year">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword4" class="col-sm-2 control-label">Difficulty</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="2" name="difficulty" id="inputPassword4">
                </div>
            </div>
            <div class="form-group">
                <label for="inputName" class="col-sm-2 control-label">studio_id / name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="studio" id="inputName">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


@stop

@section('sidebar')


<h3>Actions</h3>
<a class="btn btn-primary" href="/GuessSeries/admin">Back</a><br><br>


@stop