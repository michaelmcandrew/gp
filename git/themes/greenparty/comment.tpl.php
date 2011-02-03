<?php
// $Id: comment.tpl.php,v 1.4 2009/07/13 23:52:58 andregriffin Exp $
?>
<div class="comment<?php print ($comment->new) ? ' comment-new' : ''; print ' '. $status; print ' '. $zebra; ?>">

  <div class="comment-bar">
    <?php if ($submitted): ?>
      <span class="submitted"><?php print $submitted; ?></span>
    <?php endif; ?>

    <?php if ($comment->new) : ?>
      <span class="new"><?php print drupal_ucfirst($new) ?></span>
    <?php endif; ?>
  </div>

  <?php print $picture ?>

  <h3 class="comment-title"><?php print $title ?></h3> <!--redundant comment titles hidden by css-->

  <div class="content">
    <?php print $content ?>
    <?php if ($signature): ?>
      <div>—</div>
      <?php print $signature ?>
    <?php endif; ?>
  </div>

  <?php if ($links): ?>
    <div class="links">
      <?php print $links ?>
    </div>
  <?php endif; ?>

</div>
