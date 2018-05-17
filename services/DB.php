<?php
include BASE_DIR . "/config/database.php";

class DB {
  private $connection;
  private $result;
  private $id;

  public function __construct() {
    global $dbConfig;
    $tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . $dbConfig["DB_HOST"] . ")(PORT = " . $dbConfig["DB_PORT"] . ")))(CONNECT_DATA =(SID = " . $dbConfig["DB_SID"] . ")))";

    $this->connection = oci_connect($dbConfig["DB_USERNAME"], $dbConfig["DB_PASSWORD"], $tns, 'UTF8');
    if (!$this->connection) {
      echo "Connection to database failed!";
      die();
    }
  }

  public function __destruct() {
    if ($this->connection) {
      oci_close($this->connection);
    }
  }

  public function query($sql) {
    try {
      $this->result = oci_parse($this->connection, $sql);
      if (strpos($sql, ':id_out') !== false) {
        oci_bind_by_name($this->result, ":id_out", $this->id);
      }
      oci_execute($this->result);
    } catch (Exception $e) {
      echo $e["message"];
    }
    return $this;
  }

  public function getResult() {
    return $this->result;
  }

  public function getFetchedResult() {
    $arr = Array();
    while ($row = oci_fetch_array($this->result, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS)) {
      $arrRow = Array();
      foreach ($row as $key => $val) {
        $arrRow[strtolower($key)] = $val;
      }
      array_push($arr, $arrRow);
    }
    return $arr;
  }

  public function getId() {
    return $this->id;
  }
}