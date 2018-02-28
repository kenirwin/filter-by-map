<?
require_once('includes/init.php');

$map = new MapSettings($_REQUEST['settings_id']);
$search_term = $map->GetCanonical($_REQUEST['geo_search']);
$object = new stdClass();
$map->SearchResults($search_term,$_REQUEST['settings_id']);
$object->dict = $map->results_table_columns;
$object->data = $map->data;
print (json_encode($object));
?>
