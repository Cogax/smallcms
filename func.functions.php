<?php
/**
 * 		Datei: 					functions.php
 * 		Erstellungsdatum:		25.07.2010
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Verschiedene Funktionen f�r das Frontend
 * 		Autor:					Andreas Gyr
 */

## Mysql Injection Schutz:
function sql($value, $operator = 'no')
{
	// �berfl�ssige Maskierungen entfernen
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}

	// In Anf�hrungszeichen setzen, falls keine Zahl
	// oder ein numerischer String vorliegt:
	if (!is_numeric($value)) {
		if ($operator == 'no') {
			$value = "'" . mysql_real_escape_string($value) . "'";
		} else {
			$value = mysql_real_escape_string($value);
		}
	}

	// Falls leer, einen leeren String simulieren:
	if ($value == "") {
		$value = "''";
	}

	// R�ckgabe der Variabel:
	return $value;
}

## Optionen laden
function option($option_name) {
	global $prefix, $mysql;
	$sql = "SELECT * FROM ".$prefix."option WHERE name = ".sql($option_name).";";
	$mysql->query($sql);
	$option = $mysql->fetchRow();
	return $option['value'];
}
?>