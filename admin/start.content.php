<?php
/**
 * 		Datei: 					admin/start.content.php
 * 		Erstellungsdatum:		29.09.2011
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Startseite der Content Kategorie
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);

$link = icon_link('Neuen Artikel', 'article-add', 'index.php?modul=article&action=add');
$link .= icon_link('Neue Seite', 'file-add', 'index.php?modul=page&action=add');
$style->box('Optionen', $link, 'left');

$text = 'Hier können Sie den gesammten Inhalt der Webseite selbst schreiben, anordnen und designen. Um Ihre Webseite um eine Seite zu
 erweitern müssen Sie erst einen Artikel schreiben. Artikel sind gewisse Text-stücke, welche zusammen die Seite bilden.<br />
 Wenn Sie Artikel bestimmt haben können Sie jedem Panel Ihres Templates einen oder mehrere Artikel zuordnen und deren Reihenfolge bestimmen.';
$style->box('Info', $text);

$output = '';
$mysql->select('mod_name, mod_link, icon_big', $prefix.'modul', 'WHERE navigation_cat = '.sql('2').' AND navigation_show = '.sql('1'));
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