<?php
// $Id: node.tpl.php,v 1.4 2009/07/13 23:52:58 andregriffin Exp $
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">

    <?php //print $picture ?>

  <?php if ($page == 0): ?>
    <h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
       <?php endif;?>

<div class="meta">
    <?php if ($submitted):?>
    <div class="dateblock"><?php print format_date($node->created, $type = 'custom', $format = '<\d\iv \c\l\a\s\s="\d">d</\d\iv><\d\iv \c\l\a\s\s="\m">M</\d\iv><\d\iv \c\l\a\s\s="\y">Y</\d\iv>', $timezone = NULL, $langcode = NULL) ?></div>
  <div class="author"><h2><?php print $title ?></h2>Posted by <?php print $name; ?></div>
  <?php endif; ?>
  <?php if ($taxonomy): ?>
      <div class="terms">
        <?php print $terms ?>
      </div>
    <?php endif;?>
    <div style="clear:both;"></div>
   </div>

  <div class="content">
    <?php print $content ?>
  </div>


    <?php if ($links): ?>
      <div class="groups">
        <?php print $links; ?>
      </div>
    <?php endif; ?>

</div>
