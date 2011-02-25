<?php

$drupal_directory = "/m/htdocs/gp";  // wherever Drupal is
chdir($drupal_directory);
require_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

gpew_user_import_import()

?>