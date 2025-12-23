<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid">';

// --- LOGIK: KOPIEREN AUSFÜHREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    // Basis-Entwurf laden
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            // Empfänger-Daten aus der Plugin-Tabelle holen
            $data = Sql_Fetch_Assoc_Query("SELECT name, footer, email FROM Draft_Multiplier_Data WHERE id = " . intval($id));
            
            if ($data) {
                $copy = $original;
                unset($copy['id']); // ID entfernen, damit MySQL eine neue vergibt
                
                // 1. Betreff personalisieren
                $copy['subject'] = $data['name'] . " - " . $original['subject'];
                
                // 2. Absender (From Line) mit Name & Email aus deiner Tabelle setzen
                $copy['fromline'] = sprintf('%s <%s>', $data['name'], $data['email']);
                
                // 3. Footer anhängen (HTML Erkennung)
                // Prüfen ob die Nachricht HTML-Tags enthält
                $isHtml = (strip_tags($original['message']) != $original['message']);
                
                if ($isHtml) {
                    $footer_content = '<br><br><hr><br>' . nl2br(htmlspecialchars($data['footer']));
                    $copy['message'] = $original['message'] . $footer_content;
                } else {
                    $footer_content = "\n\n---\n" . $data['footer'];
                    $copy['message'] = $original['message'] . $footer_content;
                }
                
                // Zeitstempel und Status sicherstellen
                $copy['entered'] = date('Y-m-d H:i:s');
                $copy['modified'] = date('Y-m-d H:i:s');
                $copy['status'] = 'draft'; 

                // SQL Query zum Einfügen aufbauen
                $cols = array_keys($copy);
                $vals = array_map(function($v) { 
                    if ($v === null) return "NULL";
                    return "'" . sql_escape($v) . "'"; 
                }, array_values($copy));
                
                Sql_Query(sprintf(
                    'INSERT INTO %s (%s) VALUES (%s)',
                    $GLOBALS['tables']['message'], 
                    implode(',', $cols), 
                    implode(',', $vals)
                ));
                $count++;
            }
        }
        echo "<div class='note' style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>
                ✅ <strong>Success:</strong> $count personalized drafts have been created!
              </div>";
    }
}

// --- ANZEIGE: FORMULAR ---
echo '<form method="post" id="multiplierForm">';

// 1. Entwurf-Auswahl
echo '<div class="panel"><div class="content"><h3>1. Select Base Draft</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC', $GLOBALS['tables']['message']));
echo '<select name="draft_id" style="width:100%; max-width:600px; padding: 8px; border: 1px solid #ccc;">';
while($d = Sql_Fetch_Assoc($drafts)) {
    echo "<option value='{$d['id']}'>[ID {$d['id']}] " . htmlspecialchars($d['subject']) . "</option>";
}
echo '</select></div></div>';

// 2. Empfänger-Auswahl mit Checkboxen
echo '<div class="panel"><div class="content"><h3>2. Select Recipients</h3>';
echo '<div style="margin-bottom: 10px;">';
echo '<button type="button" onclick="toggleAll(true)" class="btn">Select All</button> ';
echo '<button type="button" onclick="toggleAll(false)" class="btn">Deselect All</button>';
echo '</div>';

$res = Sql_Query("SELECT * FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common" style="width: 100%;">
        <thead><tr><th width="30"></th><th>Name (Subject Prefix)</th><th>Email (From Line)</th><th>Footer Preview</th></tr></thead>
        <tbody>';

if (Sql_Affected_Rows($res)) {
    while ($row = Sql_Fetch_Assoc($res)) {
        $footer_preview = htmlspecialchars(substr($row['footer'], 0, 60)) . (strlen($row['footer']) > 60 ? '...' : '');
        echo "<tr>
                <td><input type='checkbox' name='selected_ids[]' value='{$row['id']}' class='rec-check'></td>
                <td><strong>" . htmlspecialchars($row['name']) . "</strong></td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td><small style='color: #666;'>" . $footer_preview . "</small></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No recipients found. Please add them in 'Manage Recipients' first.</td></tr>";
}
echo '</tbody></table>';

echo '<br><div style="text-align: right; border-top: 1px solid #eee; padding-top: 20px;">';
echo '<input type="submit" name="run_multiplier" value="Generate Personalized Drafts" class="btn btn-primary" style="font-size: 1.1em; padding: 10px 25px;">';
echo '</div>';
echo '</div></div></form>';

// JavaScript für Select/Deselect All
echo "
<script>
function toggleAll(source) {
    var checkboxes = document.getElementsByClassName('rec-check');
    for(var i=0; i<checkboxes.length; i++) {
        checkboxes[i].checked = source;
    }
}
</script>";

echo '</div>';