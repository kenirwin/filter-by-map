<?php
 
/**
 * MapSettings class
 */

class MapSettings
{
  public function __construct($id) {
    try {
      $db = Database::getInstance();
      $query = "SELECT * FROM `settings` WHERE `id` = ?";
      $stmt = $db->prepare($query);
      $stmt->execute(array($id));
      $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->table_name = $row[0]['table_name'];
      $this->unit_fieldname = $row[0]['unit_fieldname'];
      $this->match_unit_fieldname = $row[0]['match_unit_fieldname'];
      $this->basemap_id = $row[0]['basemap_id'];
      $this->color_scheme_id = $row[0]['color_scheme_id'];
      $this->item_label_singular = $row[0]['item_label_singular'];      
      $this->item_label_plural = $row[0]['item_label_plural'];
    } catch(PDOException $exception) {
      error_log($exception->getMessage());
    }
    try { 
      $query = "SELECT * FROM basemaps WHERE id = ?";
      $stmt = $db->prepare($query);
      $stmt->execute(array($this->basemap_id));
      $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->map_name = $row[0]['name'];
      $this->map_file = $row[0]['filename'];
      $this->units_table = $row[0]['units_table'];
      $this->units_name_field = $row[0]['units_name_field'];
      $this->return_field = $row[0]['return_field'];
    } catch(PDOException $exception) {
      error_log($exception->getMessage());
    }
    try { 
      $query = "SELECT * FROM color_schemes WHERE id = ?";
      $stmt = $db->prepare($query);
      $stmt->execute(array($this->color_scheme_id));
      $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->colors = preg_split('/,/', $row[0]['colors']);
      $this->background_color = $row[0]['background'];
      $this->nodata_color = $row[0]['nodata'];
      $this->mouseover_color = $row[0]['mouseover'];
    } catch(PDOException $exception) {
      error_log($exception->getMessage());
    }
    $this->ColorizeUnits();
  }

  private function UnitCounts() {
    try {
      $db = Database::getInstance();
      $query = 'SELECT '.$this->return_field.' as code, count(*) as n FROM '. $this->table_name .','.$this->units_table.' WHERE '.$this->units_table.'.'.$this->match_unit_fieldname.' = '.$this->table_name .'.'. $this->unit_fieldname .' group by '.$this->return_field;
      $stmt = $db->query($query);
      $codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
      //  print_r($codes);
    } catch(PDOException $exception) {
      error_log($exception->getMessage());
      $codes = array();
    }
    return $codes;
  }


  public function ColorizeUnits() {
    try
      { 
	$db = Database::getInstance();
	$codes = $this->UnitCounts();
	
	foreach ($codes as $i => $a) {
	  if (! isset($min)) { $min = $a['n']; }
	  elseif ($a['n'] < $min) { $min = $a['n']; }
	  if (! isset($max)) { $max = $a['n']; }
	  elseif ($a['n'] > $max) { $max = $a['n']; }
	}
	//  print_r($codes);

	$cats = sizeof($this->colors);
	$divisor = floor($max/$cats);
	
	$fills = "defaultFill: '".$this->nodata_color."',";
	for ($i=0;$i<$cats;$i++) {
	  $fills .= "\trank$i: '".$this->colors[$i]."',".PHP_EOL;
	}
	
	$fill_keys = '';
	foreach ($codes as $k => $a) {
	  $count = $a['n'];
	  $rank = (ceil($count/$divisor)-1);
	  if ($rank > $cats - 1) { $level = "rank".($cats-1); }
	  else { $level = "rank".$rank; }
	  $fill_keys .= $a['code'] . " : { fillKey: '$level', numberOfCites: " . $count."},".PHP_EOL;
	}
	$this->fills = $fills;
	$this->fillKeys = $fill_keys;
      } catch(PDOException $exception) {
      error_log($exception->getMessage());
    }

  }
  
  public function GetCanonical($term) {
    	$db = Database::getInstance();    
	$query = 'SELECT canonical_name FROM unit_aliases WHERE alias = ?';
	$stmt = $db->prepare($query);
	$stmt->execute(array($term)); 
	if ($stmt->rowCount() > 0) {
	  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  return ($rows[0]['canonical_name']);
	}
	else {
	  return $term;
	}
  }

  public function SearchResults($geo_search, $settings_id) {
    try
      { 
	$db = Database::getInstance();    
	$query = 'SELECT ' . $this->table_name . '.* FROM '. $this->table_name .','. $this->units_table .' WHERE '. $this->units_table .'.'. $this->units_name_field .' = ? AND '.$this->unit_fieldname .' = '. $this->match_unit_fieldname;
	$stmt = $db->prepare($query);
	$stmt->execute(array($geo_search));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $rows; 
      } 
    catch(PDOException $exception) 
      {
	error_log($exception->getMessage());
      }
  }

}




