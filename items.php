<?
require_once('admin/includes/init.php');

$map = new MapSettings($_REQUEST['settings_id']);
$rows = $map->SearchResults($_REQUEST['geo_search'],$_REQUEST['settings_id']);
print (MysqlResultsTable($rows));

function MysqlResultsTable($rows, $table_id="results_table") { 
  $html = '';
  foreach ($rows as $row) {
    if (! isset($headers)) {
      $headers = array_keys($row);
    }
    $html .= " <tr>\n";
    foreach ($headers as $k) {
      $html .= "  <td class=$k>$row[$k]</td>\n";
    }
    $html .= " </tr>\n";
  } // end while row
  $header = join("</th><th>",$headers);
  $header = "<tr><th>$header</th></tr>\n";
  $id = 'id="'.$table_id.'"'; 
  $html = "<table $id><thead>$header</thead><tbody>$html<tbody></table>\n";
  return ($html);
}

?>
