<?php

$mapit=json_decode(@file_get_contents("http://mapit.mysociety.org/postcode/".urlencode(GY35TW)));
print_r($mapit);
print_r();

?>