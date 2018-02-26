<?
require_once('admin/includes/init.php');

function UnitCounts($data_table, $data_unit_fieldname, $match_target_table, $match_target_fieldname, $match_target_returnfield) {
  /*
    e.g.:
    $data_table = graphic_travel
    $data_unit_fieldname = country
    $match_target_table = countries
    $match_target_fieldname = country_name
    $match_target_returnfield = alpha3
   */
try {
  $db = Database::getInstance();
  //print_r(func_get_args());
  //  $query = 'SELECT ? as code, count(*) as n FROM ?,? WHERE ? = ? group by ?'; 
  $query = "SELECT $match_target_returnfield as code, count(*) as n FROM $data_table,$match_target_table WHERE $match_target_table.$match_target_fieldname = $data_table.$data_unit_fieldname group by $match_target_returnfield";
  //  print $query;
  //  $stmt = $db->prepare($query);
  //$stmt->execute(array($match_target_returnfield, $data_table, $match_target_table, $match_target_table.'.'.$match_target_fieldname, $data_table.'.'.$data_unit_fieldname,$match_target_returnfield));
  $stmt = $db->query($query);
  $codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  //  print_r($codes);
} catch(PDOException $exception) {
  error_log($exception->getMessage());
  $codes = array();
  }
return $codes;
}



function ColorizeUnits ($settings_id) {
try{ 
  $db = Database::getInstance();
  $query = 'SELECT table_name, unit_fieldname, match_unit_fieldname, units_table, return_field FROM `settings`,basemaps WHERE basemaps.id = settings.basemap_id and settings.id = ?';
  $stmt = $db->prepare($query);
  $stmt->execute(array($settings_id));
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  extract($rows[0]);
  $codes = UnitCounts($table_name, $unit_fieldname, $units_table, $match_unit_fieldname, $return_field);

  foreach ($codes as $i => $a) {
    if (! isset($min)) { $min = $a['n']; }
    elseif ($a['n'] < $min) { $min = $a['n']; }
    if (! isset($max)) { $max = $a['n']; }
    elseif ($a['n'] > $max) { $max = $a['n']; }
  }
  //  print_r($codes);

  $query = "SELECT colors from settings,color_schemes WHERE color_schemes.id = settings.color_scheme_id and settings.id = ?";
  $stmt = $db->prepare($query);
  $stmt->execute(array($settings_id));
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $colors = preg_split('/,/', $rows[0]['colors']);
  $cats = sizeof($colors);
  $divisor = floor($max/$cats);
  print "DIV: $divisor";

  $fills = '';
  for ($i=0;$i<$cats;$i++) {
    $fills .= "\trank$i: '".$colors[$i]."',".PHP_EOL;
  }
  
  $fill_keys = '';
  foreach ($codes as $k => $a) {
    $count = $a['n'];
    $rank = (ceil($count/$divisor)-1);
    if ($rank > $cats - 1) { $level = "rank".($cats-1); }
    else { $level = "rank".$rank; }
    $fill_keys .= $a['code'] . " : { fillKey: '$level', numberOfCites: " . $count."},".PHP_EOL;
  }
  return (array('fills'=>$fills, 'fill_keys'=>$fill_keys));


} catch(PDOException $exception) {
  error_log($exception->getMessage());
  }

}
