<div class="rounded_block" >
  <span class="text">
   {{{ $text }}}
  </span>
    <? if(isset($background)) { ?>

        <img src="<?= $background ?>">
    <? } else { ?>
        <div class="back"></div>
    <? }  ?>

</div>