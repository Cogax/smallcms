<?php
/**
 * 		Datei: 					admin/mod.page.php	
 * 		Erstellungsdatum:		26.07.10
 * 		Letzte bearbeitung:		08.09.10 - Liste-, Form- und Styleklasse eingebaut
 * 		Beschreibung:			Seitenverwaltung
 * 		Autor:					Andreas Gyr
 */
error_reporting(E_ALL);
if (!defined('ADMIN')) die('Diese Seite kann nicht manuell aufgerufen werden!');

// Neue Seite erstellen link
$link = icon_link('Übersicht', 'table', 'index.php?modul=page');
$link .= icon_link('Neuen Artikel', 'article-add', 'index.php?modul=article&action=add');
$link .= icon_link('Neue Seite', 'file-add', 'index.php?modul=page&action=add');
$style->box('Optionen', $link, 'left');

// Javascript Formularprüfung
$js = '';
$js_i = array();


if(formular('add')) {
	// Seite erstellen - Formular 1
	$mysql->select('id', $prefix.'template');
	if($mysql->count() > 0) {
		// Template exisiert
		$mysql->select('id', $prefix.'article');
		if($mysql->count() > 0) {
			// Artikel existieren
			$form= new form('index.php?modul=page&action=add&step=2');
			$form->new_group('Schritt 1');
			$form->description('Sie sind dabei eine neue Seite zu erstellen. Geben Sie Ihrer Seite einen passenden Namen und wählen Sie das Aussehen Ihrer Seite (Template).');
			$form->input('Name:', 'name');
			
			$options = array();
			$mysql->select('*', $prefix.'template');
			while ($template = $mysql->fetchRow()) {
				$options[$template['id']] = $template['name'];
			}
			$form->select('Template', 'template', $options);
			
			$form->new_group('Vorlage');
			$mysql->select('id,name', $prefix.'page');
			if ($mysql->count() > 0) {
				// Vorlagen vorhanden
				$form->description('Du kannst eine Seite als Vorlage laden.');
				$form->checkbox('Vorlage:', 'vorlage', array('Ja'));
				$options = array();
				while ($page = $mysql->fetchRow()) {
					$options[$page['id']] = $page['name'];
				}
				$form->select('Seite:', 'page', $options, 10, 0, 0, 1, 0);
			} else {
				$form->description('Es existiert noch keine Seite, welche als Vorlage verwendet werden kann. Dies ist nicht weiter Schlimm, da Sie gerade Ihre
				erste Seite erstellen.');
			}
			$form->submit('Weiter zu Schritt 2');
			$style->box('Neue Seite', $form->get());
		} else {
			// Es existiert kein Artikel
			$style->box(w_icon().'Warnung!', 'Es existiert noch kein Artikel! Um eine Seite zu erstellen müssen Sie zuerst die Artikel für die Seite schreiben.'.back_overview());
		}
	} else {
		// Es existiert kein Template
		$style->box(e_icon().'Warnung!', 'Es existiert noch kein Template! Um eine Seite zu erstellen müssen Sie zuerst ein Template hinzufügen.'.back_overview());
	}
	
} elseif (formular('add', 2)) {	
	// Seite erstellen - Formular Step 2
	
	// Vorlage?
	$v = false;
	if(isset($_POST['vorlage']) && isset($_POST['vorlage']) == 'Ja') {
		$v = true;
		$mysql->id_select('*', $prefix.'page', sql($_POST['page']));
		$page = $mysql->fetchRow();
		$page_data = unserialize($page['data']);
	}
	
	// Schritt 1 Ergebnisse anzeigen
	$text_step1 = 
	'Folgende Daten wurden aus den vorherigem Schritt übernommen:<br /><strong>Seitenname:</strong> '.$_POST['name'].'<br />
	<strong>Template:</strong> '.template_name($_POST['template']);
	if ($v) {
		$text_step1 .= '<br /><br />Sie haben eine Seite als Vorlage gewählt. Nun sind unter (Schritt 2) die Artikel selektiert, welche auf dieser Seite verwendet werden.
		Sie können nun ganz einfach nur bei einem Platzhalter einen oder mehrere andere Artikel anwählen, somit bleibt der Rest gleich.';
	}
	$style->box('Schritt 1', $text_step1);
	
	// Formular Schritt 2
	$form = new form('index.php?modul=page&action=add&step=3');
	$form->request_hidden(); // Formulardaten weiterleiten
	if($v) {
		$form->input('', 'page', $page['id'], 'hidden');
	}
	
	// Template Platzhalter holen
	$mysql->id_select('*', $prefix.'template', sql($_POST['template']));
	$tpl = $mysql->fetchRow();
	foreach (unserialize($tpl['platzhalter']) as $key => $platzhalter) {
		$form->new_group($platzhalter);
		$mysql->select('id,title', $prefix.'article');
		
		$options = array();
		while ($article = $mysql->fetchRow()) {
			$options[$article['id']] = $article['title'];
		}
		
		$description = 'Hier kannst du die Artikel wählen, welch im Platzhalter "'.$platzhalter.'" angezeigt werden. 
		Die Artikel kannst du beim nächsten Schritt beliebig sortieren.';
		$form->description($description);
		$form->select('Artikel:', $platzhalter.'[]', $options, 10, true, ($v ? $page_data[$platzhalter] : false), 1, 1);
	}
	$form->submit('Weiter zu Schritt 3');
	$style->box('Schritt 2', $form->get());
	
} elseif (formular('add', 3)) {	
	// Seite erstellen - Formular Step 3
	// Schritt 1 Ergebnisse anzeigen
	$text_step1 = 
	'<strong>Name:</strong> '.$_POST['name'].'<br />
	<strong>Template:</strong> '.template_name($_POST['template']);
	$style->box('Schritt 1', $text_step1);
	
	// Vorlage?
	$v = false;
	if (isset($_POST['page'])) {
		$v = true;
		$mysql->id_select('*', $prefix.'page', sql($_POST['page']));
		$page = $mysql->fetchRow();
		$page_data = unserialize($page['data']);
	}
	
	// Formular mit Formularprüfung
	$form = new form('index.php?modul=page&action=add', 'post', 'onsubmit="javascript: return bla()"');
	$form->request_hidden(); // Formulardaten weiterleiten
	// Schritt 2 & 3
	$mysql->id_select('*', $prefix.'template', sql($_POST['template']));
	$tpl = $mysql->fetchRow();
	foreach (unserialize($tpl['platzhalter']) as $key => $platzhalter) {
		$form->new_group($platzhalter);
		$x = count($_POST[$platzhalter]);
		$options = array();
		for ($i=1;$i<=$x;$i++) {
			$options[$i] = $i; 
		}
		foreach ($_POST[$platzhalter] as $article_id) {
			$form->select(article_title($article_id).':', $platzhalter.'['.$article_id.']', $options, 1, false, ($v == true ? array_search($article_id, $page_data[$platzhalter]) : false));
			
			// Js formprüfung
			if(isset($js_i[$platzhalter]) && $js_i[$platzhalter] > 0) {
				$js_i[$platzhalter]++;
			} else {
				$js_i[$platzhalter] = 1;
				$js .= "save['".$platzhalter."'] = new Array();
				";
			}
			$js .= 'save[\''.$platzhalter.'\'][document.getElementById(\''.$platzhalter.'['.$article_id.']'.'\').value] = "a";
			';
		}
	}
	
	$form->submit('Seite erstellen');
	$style->box('Schritt 2 & 3', $form->get());
	
} elseif (entry('add')) {
	// Seite erstellen - Eintrag
	// Platzhalter laden
	$mysql->id_select('*', $prefix.'template', sql($_POST['template']));
	$template = $mysql->fetchRow();
	
	$data = array();
	foreach (unserialize($template['platzhalter']) as $key => $platzhalter) {
		foreach ($_POST[$platzhalter] as $article_id => $sort) {
			$data[$platzhalter][$sort] = $article_id;
		}
	}
	
	$insert = array();
	$insert['name'] = sql($_POST['name']);
	$insert['template'] = sql($_POST['template']);
	$insert['date'] = sql(time());
	$insert['data'] = sql(serialize($data));
	$mysql->insert($insert, $prefix.'page');
	$style->box(p_icon().'Erfolgreich!', 'Die Seite konnte erfolgreich erstellt werden.'.back_overview());
} elseif (formular('edit')) {
	// Seite bearbeiten - Formular 1
	$mysql->id_select('*', $prefix.'page', sql($_GET['id']));
	$page = $mysql->fetchRow();
	
	$form = new form('index.php?modul=page&action=edit&step=2&id='.$_GET['id']);
	$form->new_group('Schritt 1');
	$form->input('Name:', 'name', $page['name']);
	
	$options = array();
	$mysql->select('*', $prefix.'template');
	while ($template = $mysql->fetchRow()) {
		$options[$template['id']] = $template['name'];
	}
	$form->select('Template', 'template', $options, 1, false, $page['template']);
	$form->submit('Weiter zu Schritt 2');
	$style->box('Seite bearbeiten', $form->get());
} elseif (formular('edit', 2)) {	
	// Seite bearbeiten - Formular Step 2	
	
	// Schritt 1 Ergebnisse anzeigen
	$text_step1 = 
	'<strong>Name:</strong> '.$_POST['name'].'<br />
	<strong>Template:</strong> '.template_name($_POST['template']);
	$style->box('Schritt 1', $text_step1);
	
	// Templateinfos laden
	$mysql->id_select('*', $prefix.'template', sql($_POST['template']));
	$tpl = $mysql->fetchRow();
	
	// Seiteninfos laden
	$mysql->id_select('*', $prefix.'page', sql($_GET['id']));
	$page = $mysql->fetchRow();
	$data = unserialize($page['data']);
		
	// Formular Schritt 2
	$form = new form('index.php?modul=page&action=edit&step=3&id='.$_GET['id']);
	$form->request_hidden(); // gesamtes POST hidden
	$form->input('', 'id', $_GET['id'], 'hidden');
	
	// Template Platzhalter holen
	$mysql->id_select('*', $prefix.'template', sql($_POST['template']));
	$tpl = $mysql->fetchRow();
	foreach (unserialize($tpl['platzhalter']) as $key => $platzhalter) {
		$form->new_group($platzhalter);
		$mysql->select('*', $prefix."article");
		
		$options = array();
		$selected = array();
		while ($article = $mysql->fetchRow()) {
			$options[$article['id']] = $article['title'];
			// Selected bestimmen
			if(key_exists($platzhalter, $data)) {
				if(in_array($article['id'], $data[$platzhalter])) {
					$selected[] = $article['id'];
				}
			}
		}
		
		$description = 'Hier kannst du die Artikel wählen, welch im Platzhalter "'.$platzhalter.'" angezeigt werden. 
		Die Artikel kannst du beim nächsten Schritt beliebig sortieren.';
		$form->description($description);
		$form->select('Artikel:', $platzhalter.'[]', $options, 10, true, $selected);
	}
	$form->submit('Weiter zu Schritt 3');
	$style->box('Schritt 2', $form->get());
	
} elseif (formular('edit', 3)) {	
	// Seite bearbeiten - Formular Step 3
	$mysql->id_select('*', $prefix.'page', sql($_GET['id']));
	$page = $mysql->fetchRow();
	$data = unserialize($page['data']);
	
	// Schritt 1 Ergebnisse anzeigen
	$text_step1 = 
	'<strong>Name:</strong> '.$page['name'].'<br />
	<strong>Template:</strong> '.template_name($page['template']);
	$style->box('Schritt 1', $text_step1);
	
	// Formular mit Formularprüfung
	$form = new form('index.php?modul=page&action=edit&id='.$_GET['id'], 'post', 'onsubmit="javascript: return bla()"');
	$form->request_hidden();
	$form->input('', 'id', $_POST['id'], 'hidden');
	
	// Schritt 2 & 3
	$mysql->id_select('*', $prefix.'template', sql($page['template']));
	$tpl = $mysql->fetchRow();
	foreach (unserialize($tpl['platzhalter']) as $key => $platzhalter) {
		$form->new_group($platzhalter);
		$x = count($_POST[$platzhalter]);
		$options = array();
		for ($i=1;$i<=$x;$i++) {
			$options[$i] = $i; 
		}
		foreach ($_POST[$platzhalter] as $article_id) {
			$form->select(article_title($article_id).':', $platzhalter.'['.$article_id.']', $options, 1, false, array_search($article_id, $data[$platzhalter]));
			
			// Js formprüfung
			if(isset($js_i[$platzhalter]) && $js_i[$platzhalter] > 0) {
				$js_i[$platzhalter]++;
			} else {
				$js_i[$platzhalter] = 1;
				$js .= "save['".$platzhalter."'] = new Array();
				";
			}
			$js .= 'save[\''.$platzhalter.'\'][document.getElementById(\''.$platzhalter.'['.$article_id.']'.'\').value] = "a";
			';
		}
	}
	
	$form->submit('Seite bearbeiten');
	$style->box('Schritt 2 & 3', $form->get());
	
} elseif (entry('edit')) {
	// Seite bearbeiten - Eintrag
	
	// Seiteninfos der eben erstellten Seite laden^^
	$mysql->id_select('*', $prefix.'page', sql($_POST['id']));
	$page = $mysql->fetchRow();
	
	// Platzhalter laden
	$mysql->id_select('*', $prefix.'template', sql($page['template']));
	$template = $mysql->fetchRow();
	
	$data = array();
	foreach (unserialize($template['platzhalter']) as $key => $platzhalter) {
		foreach ($_POST[$platzhalter] as $article_id => $sort) {
			$data[$platzhalter][$sort] = $article_id;
		}
	}
	
	$update = array();
	$update['name'] = sql($_POST['name']);
	$update['template'] = sql($_POST['template']);
	$update['data'] = sql(serialize($data));
	$mysql->update($update, $prefix.'page', sql($_GET['id']));
	$style->box(p_icon().'Erfolgreich!', 'Die Seite konnte erfolgreich bearbeitet werden.'.back_overview());
	
} elseif (delete()) {
	// Seite löschen
	$mysql->delete($prefix.'page', sql($_GET['id']));
	$style->box(p_icon().'Erfolgreich!', 'Die Seite konnte erfolgreich gelöscht werden.'.back_overview());
} elseif(view()) {
	// Seitenübersicht
	$list = new liste('Seitenübersicht');
	$columns = array();
	$columns['ID'] = 30;
	$columns['Name'] = '';
	$columns['Template'] = '';
	$columns['Datum'] = '';
	$columns['Optionen'] = '';
	$list->add_columns($columns);
	
	$mysql->select('*', $prefix.'page', 'ORDER BY id ASC');
	while($page = $mysql->fetchRow()) {
		$data = array();
		$data[] = $page['id'];
		$data[] = $page['name'];
		$data[] = template_name($page['template']);
		$data[] = date("d.m.y", $page['date']);
		$data[] = option_icon('edit', INDEX.'&id='.$page['id']).option_icon('delete', INDEX.'&id='.$page['id']);
		$list->add_row($data);
		
	}
	$style->add($list->get());
	
}

// Js Formularprüfung
$ifs = '';
foreach ($js_i as $platzhalter => $if) {
	$ifs .= 
	"if (count(save['".$platzhalter."']) != ".$js_i[$platzhalter].") { 
		alert('Im Platzhalter ".$platzhalter." wurden gleiche Sortierungszahlen gefunden!'); 
		return false; 
	}";
}
	
$javascript = "<script type=\"text/javascript\">
function count (mixed_var, mode) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Waldo Malqui Silva
    // +      bugfixed by: Soren Hansen
    // +      input by: merabi
    // +      improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: count([[0,0],[0,-4]], 'COUNT_RECURSIVE');
    // *     returns 1: 6
    // *     example 2: count({'one' : [1,2,3,4,5]}, 'COUNT_RECURSIVE');
    // *     returns 2: 6

    var key, cnt = 0;

    if (mixed_var === null){
        return 0;
    } else if (mixed_var.constructor !== Array && mixed_var.constructor !== Object){
        return 1;
    }

    if (mode === 'COUNT_RECURSIVE') {
        mode = 1;
    }
    if (mode != 1) {
        mode = 0;
    }

    for (key in mixed_var){
        if (mixed_var.hasOwnProperty(key)) {
            cnt++;
            if ( mode==1 && mixed_var[key] && (mixed_var[key].constructor === Array || mixed_var[key].constructor === Object) ){
                cnt += this.count(mixed_var[key], 1);
            }
        }
    }

    return cnt;
}
function bla() { 
	var save = new Array();
	".$js."
	".$ifs."
	return true; 
}
/*
function dump(arr,level) {
	var dumped_text = \"\";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = \"\";
	for(var j=0;j<level+1;j++) level_padding += \"    \";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + \"'\" + item + \"' ...\n\";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + \"'\" + item + \"' => \" + value + \"\n\";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = \"===>\"+arr+\"<===(\"+typeof(arr)+\")\";
	}
	return dumped_text;
}*/
</script>";
$style->add($javascript);
?>