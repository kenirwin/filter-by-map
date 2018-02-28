<?
header("Content-type: text/plain");
require_once('admin/includes/init.php');

$s = new MapSettings(1);
$s->ColorizeUnits();
print_r(get_object_vars($s));



?>