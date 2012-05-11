<?php
// $Id: maintenance-page.tpl.php,v 1.4 2009/07/13 23:52:58 andregriffin Exp $
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" >
  <head>
    <title><?php print $head_title ?></title>
    <?php print $head ?>
    <?php print $styles ?>
    <?php print $scripts ?>
    <!--[if lte IE 7]><?php print framework_get_ie_styles(); ?><![endif]--> <!--If Less Than or Equal (lte) to IE 7-->
  </head>
  <body<?php print framework_body_class($left, $right); ?>>
    <!-- Layout -->
    <div class="container"> <!-- add "showgrid" class to display grid -->
  
      <div id="header" class="clearfix">
        <?php print $header; ?>

        <?php if ($logo): ?>
          <a href="<?php print check_url($front_page); ?>" title="<?php print check_plain($site_name); ?>">
            <img src="<?php print check_url($logo); ?>" alt="<?php print check_plain($site_name); ?>" id="logo" />
          </a>
        <?php endif; ?>

        <div id="sitename">
					<?php if ($site_name): ?>
            <h1><a href="<?php print check_url($front_page); ?>" title="<?php print check_plain($site_name); ?>"><?php print check_plain($site_name); ?></a></h1>
          <?php endif; ?>
  
          <?php if ($site_slogan): ?>
            <span id="siteslogan"><?php print check_plain($site_slogan); ?></span>
          <?php endif; ?>
        </div> <!-- /#sitename -->
      
        <?php if ($search_box): ?><?php print $search_box ?><?php endif; ?>
      </div> <!-- /#header -->

      <div id="nav">
        <?php if ($nav): ?>
          <?php print $nav ?>
        <?php endif; ?>

        <?php if (!$nav): ?> <!-- if block in $nav, overrides default $primary and $secondary links -->

          <?php if (isset($primary_links)) : ?>
            <?php print theme('links', $primary_links, array('class' => 'links primary-links')) ?>
          <?php endif; ?>
          <?php if (isset($secondary_links)) : ?>
            <div id="secondary-links"><?php print theme('links', $secondary_links, array('class' => 'links secondary-links')) ?></div>
          <?php endif; ?>

        <?php endif; ?>
      </div> <!-- /#nav -->

			<?php if ($left): ?>
        <div id="sidebar-left" class="sidebar">
          <?php print $left ?>
        </div> <!-- /#sidebar-left -->
      <?php endif; ?>

      <div id="main">
				<?php if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif; ?>
        <?php print $help; ?>
        <?php print $messages; ?>
        <?php print $content ?>
      </div> <!-- /#main -->
  
      <?php if ($right): ?>
        <div id="sidebar-right" class="sidebar">
          <?php print $right ?>
        </div> <!-- /#sidebar-right -->
      <?php endif; ?>

      <div id="footer" class="clear">
        <?php print $footer_message . $footer ?>
        <?php print $feed_icons ?>
      </div> <!-- /#footer -->

    </div> <!-- /.container -->
    <!-- /layout -->

  <?php print $closure ?>

  </body>
</html>