<?php
/**
 * 		Datei: 					admin/sql/sql.testmodul.php
 * 		Erstellungsdatum:		02.10.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Sql datei für testmod
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);

$insert = array();
$insert['name'] = sql('Testmodulkategorie');
$mysql->insert($insert, $prefix.'modul_cat');


?>