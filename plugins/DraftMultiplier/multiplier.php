<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid"><h1>Draft Multiplier Pro</h1>';

// --- LOGIK: KOPIEREN AUSFÜHREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; // Array der IDs aus der Tabelle
    
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            $data = Sql_Fetch_Assoc_Query("SELECT name, footer, email FROM Draft_Multiplier_Data WHERE id = " . intval($id));
            
            $copy = $original;
            unset($copy['id']);
            $copy['subject'] = $data['name'] . " - " . $original['subject'];
            // Wir ersetzen einen Platzhalter [FOOTER] oder hängen ihn einfach an
            $copy['message'] = $original['message'] . "\n\n" . $data['footer'];
            $copy['entered'] = date('Y-m-d H:i:s');
            
            $cols = array_keys($copy);
            $vals = array_map(function($v) { return "'" . sql_escape($v) . "'"; }, array_values($copy));
            Sql_Query(sprintf('INSERT INTO %s (%s) VALUES (%s)', $GLOBALS['tables']['message'], implode(',', $cols), implode(',', $vals)));
            $count++;
        }
        echo "<div class='note'>HURRA: $count personalisierte Entwürfe wurden erstellt!</div>";
    }
}

// --- ANZEIGE: FORMULAR ---
echo '<form method="post" id="multiplierForm">';

// 1. Entwurf-Auswahl
echo '<div class="panel"><div class="content"><h3>1. Basis-Entwurf wählen</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC', $GLOBALS['tables']['message']));
echo '<select name="draft_id" style="width:100%; max-width:500px;">';
while($d = Sql_Fetch_Assoc($drafts)) echo "<option value='{$d['id']}'>[ID {$d['id']}] {$d['subject']}</option>";
echo '</select></div></div>';

// 2. Empfänger-Auswahl mit Checkboxen
echo '<div class="panel"><div class="content"><h3>2. Empfänger wählen</h3>';
echo '<button type="button" onclick="toggleAll(true)" class="btn">Alle auswählen</button> ';
echo '<button type="button" onclick="toggleAll(false)" class="btn">Auswahl aufheben</button><br><br>';

$res = Sql_Query("SELECT * FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common">
        <thead><tr><th></th><th>Name</th><th>Email</th><th>Footer Vorschau</th></tr></thead>
        <tbody>';
while ($row = Sql_Fetch_Assoc($res)) {
    echo "<tr>
            <td><input type='checkbox' name='selected_ids[]' value='{$row['id']}' class='rec-check'></td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td><small>".htmlspecialchars(substr($row['footer'], 0, 50))."...</small></td>
          </tr>";
}
echo '</tbody></table>';

echo '<br><input type="submit" name="run_multiplier" value="Markierte Entwürfe erstellen" class="btn btn-primary" style="background-color: #2c3e50; color: white; padding: 10px 20px;">';
echo '</div></div></form>';

// JavaScript für Select/Deselect All
echo "
<script>
function toggleAll(source) {
    checkboxes = document.getElementsByClassName('rec-check');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source;
    }
}
</script>";

echo '</div>';