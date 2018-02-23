<?
require_once('admin/includes/init.php');

try {
  $db = Database::getInstance();
  if (isset($_REQUEST['country'])) { 
    $where = 'country="'.$_REQUEST['country'].'"';
    $array = array($_REQUEST['country']);
  }
  else { 
    $where = '1';
    $array = null;
  }
  $query = 'SELECT citation FROM graphic_travel WHERE ' . $where;

  $stmt = $db->prepare($query);
  $stmt->execute();
  $bib = $stmt->fetchAll();
} catch(PDOException $exception) {
  error_log($exception->getMessage());
  $bib = [];
  }

if (sizeof($bib) > 0) {
  foreach ($bib as $row){
    print '<li>'.$row['citation'].'</li>'.PHP_EOL;
  }
}
else {
  print "No entries found";
}

?>
