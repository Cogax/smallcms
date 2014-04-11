<?php
/**
 * 		Datei: 					admin/start.markt.php
 * 		Erstellungsdatum:		29.09.2011
 * 		Letzte bearbeitung:		-
 * 		Beschreibung:			Startseite der Markt Kategorie
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);

$link = icon_link('Neuen Artikel', 'article-add', 'index.php?modul=article&action=add');
$link .= icon_link('Neue Seite', 'file-add', 'index.php?modul=page&action=add');
$style->box('Optionen', $link, 'left');

$text = 'Dies ist der Bereich für die Kunden- und Produkteverwaltung etc. Die gesamten Zusatzmodule für die Buchhaltung finden Sie hier.';
$style->box('Info', $text);

$output = '';
$mysql->select('id', $prefix.'admin_navigation_cat', 'WHERE name = '.sql('Markt'));
$cat = $mysql->fetchRow();
$mysql->select('mod_name, mod_link, icon_big', $prefix.'modul', 'WHERE navigation_cat = '.sql($cat['id']).' AND navigation_show = '.sql('1'));
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