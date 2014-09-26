@extends('layout.layout')

@section('content')

<div class="page-header">
    <h1>Starred Labels <small>Mini snippet for ranking labels!</small></h1>
</div>


<div class="row">
    <div class="span7 offset1">
        <h3>Demo:</h3>
        <p>
            I can rate <span class="label label-stars" data-value="75">something</span> with stars!<br>
            I can rate book <span class="label label-stars" data-value="100">"Song of Ice and Fire"</span> with all four stars!
            I will give <span class="label label-stars" data-value="88">Tirion Lannister</span> three and half star!
            <span class="label label-stars" data-value="12">Sansa Stark</span> will get half of a star from me.
            And <span class="label label-stars" data-value="59">Jaime Lannister</span> will stand with his two and a quarter of star for being so ambiguous.<br>
            You can rang a <span class="label label-stars" data-value="38">small</span> word or a <span class="label label-stars" data-value="90">very-very-very really big one</span>!<br>
            <br>
            An example could be found at <a href="{{ action('PageController@skills') }}" >my skills page</a>.
        </p>
        <br>
        <h3>How it works:</h3>
        <p>
            Ok, if you liked "Starred Labels" and want to use them on your site here all you need:<br>
        </p>
        <p>
            <strong>First</strong> of all you need be sure that you have all required components. They are:
            <strong>Jqery</strong> and <strong>Twitter Bootstrap</strong>.<br>
            <em>If you dont want to use Bootstrap, you just need to copy all "<strong>label</strong>" css styles from there to your project styles.</em>
        </p>
        <p>
            <strong>HTML</strong>
        <pre>&lt;span class="label label-stars" data-value="75"&gt;something&lt;/span&gt;</pre>
        Attribute "data-value" can be from 0 to 100. That represents stars. Value of one hundred will be all four stars.
        Fifty will be two stars, etc.
        </p>
        <p>
            <strong>JS</strong><br>
            You need to add some onload js.
<pre>$(document).ready(function(){
  $('.label-stars').starred();
});</pre>
        And you also need to define <strong>starred</strong> function.
<pre>$.fn.starred = function()
{
  $(this).each(function(event, el){
    el = $(el);
    var value = el.data('value');
    if(el.prev().hasClass('before-stars')) return ;
    el.before('&lt;div class="container-stars"&gt;&lt;/div&gt;')
    .appendTo(el.prev()).before('&lt;div class="before-stars label"&gt;&lt;/div&gt;');

    el.prev().css({width: el.css('width'), height : el.css('height')});
    var star_div = '&lt;div class="star"&gt;&lt;div class="under-star"&gt;&lt;/div&gt;&lt;div class="img-star"&gt;&lt;/div&gt;&lt;/div&gt;';
    el.prev().append(star_div + star_div + star_div + star_div);

    for(var i = 0; i < 4; i++) {
      if(value < (i+1)*25) {
        var star = el.prev().find('.star .under-star').eq(i);
        if(value < i*25) {
          star.width('0px');
        } else {
          star.width(Math.floor(((value - i*25)/25)*18) + 'px');
        }
      }
    }
	});
};</pre>
        </p>
        <p>
            <strong>CSS</strong><br>
            And also you need to add this css styles to your site.
    <pre>/**** label-stars ****/
.label-stars { min-width: 72px; text-align: center; }

.before-stars { display:none; position: absolute; margin: 2px 0 0 0; min-width: 72px; }

.container-stars:hover .before-stars{ display:block; }
.container-stars { display:inline-block; }

.star{ float:left; width: 12.5%; padding-left: 12.5%; height: 16px; margin: -1px 0 0 0; }
.under-star { background-color: #F89406; width: 18px; height: inherit; position: absolute; margin: 0 0 0 -9px; /* 9px = half image width */ }
.img-star { background-image: url('../images/star.png'); width: 18px; height: inherit; position: absolute; margin: 0 0 0 -9px; /* 9px = half image width */ }</pre>
        </p>
        <br>
        And a little bit more demos:<br>
        <span class="label label-stars" data-value="10">3,1415926535</span><br>
        <span class="label label-stars" data-value="20">3,1415926535897</span><br>
        <span class="label label-stars" data-value="30">3,1415926535897932</span><br>
        <span class="label label-stars" data-value="40">3,1415926535897932384</span><br>
        <span class="label label-stars" data-value="50">3,1415926535897932384626</span><br>
        <span class="label label-stars" data-value="60">3,1415926535897932384626433</span><br>
        <span class="label label-stars" data-value="70">3,1415926535897932384626433832</span><br>
        <span class="label label-stars" data-value="80">3,1415926535897932384626433832795</span><br>
        <span class="label label-stars" data-value="90">3,1415926535897932384626433832795028</span><br>
        <span class="label label-stars" data-value="100">3,141592653589793238462643383279502884</span><br>
    </div>

</div>

@stop