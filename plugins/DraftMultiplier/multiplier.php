<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

// --- LOGIK: KOPIEREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    // Basis-Entwurf laden
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            // Empfänger-Daten aus deiner Tabelle
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", intval($id)));
            
            if ($data) {
                // Wir nehmen das Original und ändern nur die personalisierten Felder
                $copy = $original;
                unset($copy['id']); // Wichtig: ID entfernen für neuen Eintrag
                
                // 1. Personalisierung
                $copy['subject'] = $data['name'] . " - " . $original['subject'];
                $copy['fromline'] = $data['name'] . ' <' . $data['email'] . '>';
                
                // 2. Footer anhängen
                $isHtml = (strip_tags($original['message']) != $original['message']);
                if ($isHtml) {
                    $copy['message'] = $original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($data['footer']));
                } else {
                    $copy['message'] = $original['message'] . "\n\n---\n" . $data['footer'];
                }

                // 3. Status und Zeitstempel
                $copy['status'] = 'draft';
                $copy['entered'] = date('Y-m-d H:i:s');
                $copy['modified'] = date('Y-m-d H:i:s');

                // 4. In die Datenbank schreiben
                $cols = array_keys($copy);
                $vals = array_map(function($v) { 
                    if ($v === null) return "NULL";
                    return "'" . sql_escape($v) . "'"; 
                }, array_values($copy));
                
                $query = sprintf('INSERT INTO %s (%s) VALUES (%s)', $GLOBALS['tables']['message'], implode(',', $cols), implode(',', $vals));
                Sql_Query($query);
                
                $count++;
            }
        }
        echo "<div class='note' style='padding:15px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;'>✅ Success: $count personalized drafts created!</div>";
    }
}

// --- ANZEIGE: FORMULAR ---
echo '<form method="post">';

// Entwurf wählen
echo '<div class="panel"><div class="content"><h3>1. Select Base Draft</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 50', $GLOBALS['tables']['message']));
echo '<select name="draft_id" style="width:100%; max-width:600px; padding:5px;">';
while($d = Sql_Fetch_Assoc($drafts)) {
    echo "<option value='{$d['id']}'>[ID {$d['id']}] " . htmlspecialchars($d['subject']) . "</option>";
}
echo '</select></div></div>';

// Empfänger wählen
echo '<div class="panel"><div class="content"><h3>2. Select Recipients</h3>';
echo '<div style="margin-bottom:10px;">
        <button type="button" onclick="toggleAll(true)" class="btn">Select All</button> 
        <button type="button" onclick="toggleAll(false)" class="btn">Deselect All</button>
      </div>';

$res = Sql_Query("SELECT id, name, email, footer FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common" style="width:100%;">
        <thead><tr><th></th><th>Name</th><th>Email</th><th>Footer</th></tr></thead>
        <tbody>';

while ($row = Sql_Fetch_Assoc($res)) {
    echo "<tr>
            <td><input type='checkbox' name='selected_ids[]' value='{$row['id']}' class='rec-check'></td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td><small>" . htmlspecialchars(substr($row['footer'], 0, 40)) . "...</small></td>
          </tr>";
}
echo '</tbody></table>';

echo '<br><input type="submit" name="run_multiplier" value="Generate Personalized Drafts" class="btn btn-primary" style="padding:10px 20px;">';
echo '</div></div></form>';

echo "<script>
function toggleAll(source) {
    var checkboxes = document.getElementsByClassName('rec-check');
    for(var i=0; i<checkboxes.length; i++) { checkboxes[i].checked = source; }
}
</script></div>";