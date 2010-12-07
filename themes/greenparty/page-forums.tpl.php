<?php
// $Id: page.tpl.php,v 1.4 2009/07/13 23:52:58 andregriffin Exp $
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" >
<head>
<title><?php print $head_title ?></title>
<?php print $head ?><?php print $styles ?><?php print $scripts ?>
<!--[if lte IE 7]><?php print greenparty_get_ie_styles(); ?><![endif]-->
<!--If Less Than or Equal (lte) to IE 7-->
</head>
<body id="discussions">
<div id="wp">
  <div id="hd">
    <div id="mast">
      <?php if ($logo): ?>
      <img src="<?php print check_url($logo); ?>" alt="<?php print check_plain($site_name); ?>" id="logo" />
      <?php endif; ?>
      <?php if ($site_name): ?>
      <a href="<?php print check_url($front_page); ?>" title="<?php print check_plain($site_name); ?>"><?php print check_plain($site_name); ?></a>
      <?php endif; ?>
    </div>
    <div id="utility-nav">
	<?php if ($logged_in): ?><p>Welcome <?php global $user; print $user->name; ?></p><?php endif; ?>
      <?php print $utilities; ?>
      <?php if ($search_box): ?>
      <?php print $search_box ?>
      <?php endif; ?>
    </div>
    <?php print $mainnav; ?>
  </div>
  <!-- /#hd -->

  <div id="sub-nav"><?php print $subnav; ?></div>

  <div id="ct">
    <div id="pri">
      <?php print $breadcrumb; ?>
      <?php if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif; ?>
      <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block"><ul class="tabs primary">'. $tabs .'</ul>'; endif; ?>
      <?php if ($tabs2): print '<ul class="tabs secondary">'. $tabs2 .'</ul>'; endif; ?>
      <?php if ($tabs): print '<span class="clear"></span></div>'; endif; ?>
      <?php if ($show_messages && $messages): print $messages; endif; ?>
      <?php print $help; ?> <?php print $content ?></div>
    <div id="sec" class="sidebar">
		<?php print $secondary ?>
	</div>
    <div id="ter" class="sidebar">
		<?php print $tertiary ?>
	</div>
  </div>
  <div id="ft"><?php print $footer_message . $footer ?> <?php print $feed_icons ?></div>
  <?php print $dev; ?> </div>
<?php print $closure ?>
</body>
</html>
