<?php
/**
 * 		Datei: 					admin/start.option.php
 * 		Erstellungsdatum:		29.09.2011
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Startseite der Einstellungen Kategorie
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);

$link = icon_link('Neue Option', 'input-add', 'index.php?modul=option&action=add');
$style->box('Optionen', $link, 'left');

$text = 'Hier können Sie die Systemeinstellungen sowie spezifische Moduleinstellungen ändern. Zudem bietet sich die möglichkeit
zusätzliche Optionen zu erstellen.';
$style->box('Info', $text);

$output = '';
$mysql->select('mod_name, mod_link, icon_big', $prefix.'modul', 'WHERE navigation_cat = '.sql('4').' AND navigation_show = '.sql('1'));
while ($modul = $mysql->fetchRow()) {
	$output .= '<table style="background-color: #A3BAE9; border:1px dashed #09F; float:left; margin-right:10px;" border="0" cellspacing="5">
  <tr>
    <td align="center"><a href="index.php?modul='.$modul['mod_link'].'"><img src="../images/admin/icons/big/'.$modul['icon_big'].'" border="0" /></a></td>
  </tr>
  <tr>
    <td align="center" class="navi_box_titel"><a href="index.php?modul='.$modul['mod_link'].'">'.$modul['mod_name'].'</a></td>
  </tr>
</table>';
}
$style->box('Übersicht', $output);
?>