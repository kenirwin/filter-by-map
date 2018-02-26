<?
require_once('admin/includes/init.php');

$map = new MapSettings($_REQUEST['settings_id']);
$search_term = $map->GetCanonical($_REQUEST['geo_search']);
$rows = $map->SearchResults($search_term,$_REQUEST['settings_id']);
print (MysqlResultsTable($rows));

function MysqlResultsTable($rows, $table_id="results_table") { 
  $html = '';
  foreach ($rows as $row) {
    if (! isset($headers)) {
      $headers = array_keys($row);
    }
    $html .= " <tr>\n";
    foreach ($headers as $k) {
      $html .= '  <td>'.$row[$k].'</td>'.PHP_EOL;
    }
    $html .= " </tr>\n";
  } // end while row
  $headers = array_map('ucwords',$headers);
  $header = join("</th><th>",$headers);
  $header = "<tr><th>$header</th></tr>\n";
  $id = 'id="'.$table_id.'"'; 
  $html = "<table $id><thead>$header</thead><tbody>$html<tbody></table>\n";
  return ($html);
}

?>
