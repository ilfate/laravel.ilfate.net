@extends('layout.dark.head')

@section('layout')


ffsdfsefef

<div class="target"></div>

<script>
    $(document).ready(function() {
        var effect = new Effect.Lamp();
       var text = new IL.TextAnimator();
       text.setTarget($('.target'));
       text.setText('Hello world!');
       text.setEffect(effect);
       text.render();
    });
</script>


@stop
