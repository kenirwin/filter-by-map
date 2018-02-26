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
  }
}
