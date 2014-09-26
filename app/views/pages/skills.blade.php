@extends('layout.layout')

@section('content')

<div class="" >
  <pre>
    <img src="/images/star_pic.png"> - I have work experience with this technic.
    <img src="/images/star_pic.png"><img src="/images/star_pic.png"> - I know a lot about this technic.
    <img src="/images/star_pic.png"><img src="/images/star_pic.png"><img src="/images/star_pic.png"> - I im very good with this technic.
    <img src="/images/star_pic.png"><img src="/images/star_pic.png"><img src="/images/star_pic.png"><img src="/images/star_pic.png"> - This technic is my primary skill. </pre>
    <!-- </pre> -->
</div>

<br>
<br>

<table class="table table-condensed table-centrated">
    <thead>
    <tr>
        <th>
            Languages
        </th>
        <th>
            Databases
        </th>
        <th>
            Front-end
        </th>
        <th>
            VCS
        </th>
        <th>
            Servers, OS
        </th>
        <th>
            Others
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><span class="label label-default label-stars" data-value="99">PHP</span></td>
        <td><span class="label label-default label-stars" data-value="85">Mysql</span></td>
        <td><span class="label label-default label-stars" data-value="78">JavaScript</span></td>
        <td><span class="label label-default label-stars" data-value="75">Git</span></td>
        <td><span class="label label-default label-stars" data-value="65">Nginx</span></td>
        <td><span class="label label-default label-stars" data-value="99">PHPUnit</span></td>
    </tr>
    <tr>
        <td><span class="label label-default label-stars" data-value="25">Ruby</span></td>
        <td><span class="label label-default label-stars" data-value="15">Oracle</span></td>
        <td><span class="label label-default label-stars" data-value="70">CSS</span></td>
        <td><span class="label label-default label-stars" data-value="50">SVN</span></td>
        <td><span class="label label-default label-stars" data-value="45">Apache</span></td>
        <td><span class="label label-default label-stars" data-value="80">Scrum</span></td>
    </tr>
    <tr>
        <td><span class="label label-default label-stars" data-value="10">Java</span></td>
        <td><span class="label label-default label-stars" data-value="35">HandlerSoket</span></td>
        <td><span class="label label-default label-stars" data-value="90">Jquery</span></td>
        <td></td>
        <td><span class="label label-default label-stars" data-value="75">Ubuntu</span></td>
        <td><span class="label label-default label-stars" data-value="65">Phing</span></td>
    </tr>
    <tr>
        <td></td>
        <td><span class="label label-default label-stars" data-value="35">Spinx</span></td>
        <td><span class="label label-default label-stars" data-value="65">HTML5</span></td>
        <td></td>
        <td><span class="label label-default label-stars" data-value="90">Memcached</span></td>
        <td><span class="label label-default label-stars" data-value="35">Behat</span></td>
    </tr>
    <tr>
        <td></td>
        <td><span class="label label-default label-stars" data-value="45">Redis</span></td>
        <td><span class="label label-default label-stars" data-value="65">Twitter Bootstrap</span></td>
        <td></td>
        <td></td>
        <td><span class="label label-default label-stars" data-value="15">Selenium</span></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><span class="label label-default label-stars" data-value="70">Photoshop</span></td>
    </tr>
    </tbody>
</table>

<br>
<br>

<a class="btn" href="{{ action('PageController@cv') }}"> << Back to CV</a>

@stop