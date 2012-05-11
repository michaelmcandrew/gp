<?php
// $Id: block.tpl.php,v 1.4 2009/07/13 23:52:58 andregriffin Exp $
?>
<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="block block-<?php print $block->module ?>">

  <?php if (!empty($block->subject)): ?>
    <h3><?php print $block->subject ?></h3>
  <?php endif;?>

  <div class="content">
    <?php print $edit_links; ?>
    <?php print $block->content ?>
  </div>

</div>
