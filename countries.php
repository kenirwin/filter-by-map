<?
require_once('admin/includes/init.php');

function CountryCounts() {
try {
  $db = Database::getInstance();
  $query = 'SELECT alpha_3 as code, count(*) as n FROM graphic_travel,`countries` WHERE countries.country_name = graphic_travel.country group by alpha_3'; 
  $stmt = $db->prepare($query);
  $stmt->execute();
  $codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  //  print_r($codes);
} catch(PDOException $exception) {
  error_log($exception->getMessage());
  $codes = array();
  }
return $codes;

}

function ColorizeCountries () {
  $codes = CountryCounts();
  foreach ($codes as $k => $a) {
    if ($a['n'] == 1) { $level = 'LOW'; }
    else { $level = 'HIGH'; }
    print $a['code'] . " : { fillKey: '$level', numberOfCites: " . $a['n'] ."},".PHP_EOL;
  }
}

