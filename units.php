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



function ColorizeUnits () {
try{ 
  $db = Database::getInstance();
  $query = 'SELECT table_name, unit_fieldname, match_unit_fieldname, units_table, return_field FROM `settings`,basemaps WHERE basemaps.id = settings.basemap_id and settings.id = ?';
  $stmt = $db->prepare($query);
  $stmt->execute(array(1));
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

  foreach ($codes as $k => $a) {
    if ($a['n'] < 100) { $level = 'LOW'; }
    elseif ($a['n'] < 1000) { $level = 'MEDIUM'; } 
    else { $level = 'HIGH'; }
    print $a['code'] . " : { fillKey: '$level', numberOfCites: " . $a['n'] ."},".PHP_EOL;
  }



} catch(PDOException $exception) {
  error_log($exception->getMessage());
  }

}
