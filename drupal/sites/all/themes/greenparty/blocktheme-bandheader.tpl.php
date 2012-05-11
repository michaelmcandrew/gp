<?php
// $Id: blocktheme-bandheader.tpl.php,v 1 2010/10/14 23:52:58 pevans $
?>
<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block-bandheader block block-<?php print $block->module ?>">

  <?php if (!empty($block->subject)): ?>
    <h3><?php print $block->subject ?></h3>
  <?php endif;?>

  <div class="content">
    <?php print $edit_links; ?>
    <?php print $block->content ?>
  </div>

</div>
