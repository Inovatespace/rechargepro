<?php
// include
include('FileIcon.inc.php');

// get the file
$file = new FileIcon('41.jpg');

// print the icon plus some data
echo $file -> displayIcon() . '  ' .  $file -> getName() . ' - ' . $file -> getSize() . '<br />';

// set the icon url
$file -> setIconUrl('icons/');


// get second file
$file = new FileIcon('1.xml');

// print the icon plus some data
echo $file -> displayIcon() . '  ' .  $file -> getName() . ' - ' . $file -> getSize() . '<br />';

?>