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
      $this->return_field = $row[0]['return_field'];
    } catch(PDOException $exception) {
      error_log($exception->getMessage());
    }
    try { 
      $query = "SELECT colors FROM color_schemes WHERE id = ?";
      $stmt = $db->prepare($query);
      $stmt->execute(array($this->color_scheme_id));
      $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->colors = preg_split('/,/', $row[0]['colors']);
    } catch(PDOException $exception) {
      error_log($exception->getMessage());
    }
  }

  public function UnitCounts() {
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
}




