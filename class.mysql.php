<?php
/**
 * 		Datei: 					class.mysql.php
 * 		Erstellungsdatum:		25.07.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			MySQL Klasse
 * 		Autor:					Andreas Gyr
 */

class mysql {
	private $connection = NULL;
	public $result;
	private $counter=NULL;
	public $result_counter = 0;
	public $x;
	public $errors = true;
	
	// MySQL Verbindung öffnen
	public function __construct($host=NULL, $database=NULL, $user=NULL, $pass=NULL, $errors = 'on'){
		if($errors == 'off') {
			$this->errors = false;
		}
		$this->connection = mysql_connect($host,$user,$pass,TRUE);
		mysql_select_db($database, $this->connection);
	}
	
	// MySQL Verbindung schliessen
	public function disconnect() {
	if (is_resource($this->connection))				
	    mysql_close($this->connection);
	}
	
	// MySQL Anfrage
	public function query($query) {
		if($this->errors) {
			$this->result = mysql_query($query,$this->connection)
				or die('Error: '.mysql_error($this->connection).'<br /><br />'.$query);
		} else {
			$this->result = @mysql_query($query,$this->connection);
		}
		$this->counter=NULL;
		return true;
	}
		
	public function insert($insert_array, $table) {
		$sql = "INSERT INTO ".$table." SET ";
		$c = count($insert_array); $i = 1;
		foreach ($insert_array as $field => $value) {
			$sql .= $field." = ".$value;
			if ($c == $i) {
				$sql .= ";";
			} else {
				$sql .= ", ";
			}
			$i++;
		}
		$this->query($sql);
		return $this->result;
	}
	
	public function update($update_array, $table, $id) {
		$sql = "UPDATE ".$table." SET ";
		$c = count($update_array); $i = 1;
		foreach ($update_array as $field => $value) {
			$sql .= $field." = ".$value;
			if ($c != $i) {
				$sql .= ", ";
			}
			$i++;
		}
		$sql .= " WHERE id = ".$id.";";
		$this->query($sql);
		return $this->result;
	}
	
	public function id_select($fields, $table, $id) {
		$sql = "SELECT ".$fields." FROM ".$table." WHERE id = ".$id.";";
		$this->query($sql);
		return $this->result;
	}
	
	public function select($fields, $table, $zusatz = '') {
		$this->result_counter++;
		$sql = "SELECT ".$fields." FROM ".$table." ".$zusatz.";";
	    $this->query($sql);
	    return $this->result;
	}
	
	public function delete($table, $id) {
		$sql = "DELETE FROM ".$table." WHERE id = ".$id.";";
		$this->query($sql);
		return $this->result;
	}
	
	public function get_from_id($field, $table, $id) {
		$sql = "SELECT ".$field." FROM ".$table." WHERE id = ".$id.";";
		$this->query($sql);
		$row = $this->fetchRow();
		return $row[$field];
	}
	
	public function fetchRow($result = false) {
		return mysql_fetch_assoc($result ? $result : $this->result);
	}
	
	public function insert_id() {
		return mysql_insert_id($this->connection);
	}
	
	public function count($result = false) {
		if($result && is_resource($result)) {
			$this->counter=mysql_num_rows($result);
		} elseif($this->counter==NULL && is_resource($this->result)) {
			$this->counter=mysql_num_rows($this->result);
		}
	
		return $this->counter;
	}
}
?>