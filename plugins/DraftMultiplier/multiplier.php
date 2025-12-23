<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

// --- LOGIK: KOPIEREN AUSFÜHREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    // Basis-Entwurf laden
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            // Empfänger-Daten aus deiner Tabelle Draft_Multiplier_Data
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", intval($id)));
            
            if ($data) {
                // Betreff und Absender vorbereiten
                $subject = sql_escape($data['name'] . " - " . $original['subject']);
                $fromline = sql_escape($data['name'] . ' <' . $data['email'] . '>');
                
                // Footer-Logik (HTML vs Plain Text)
                $isHtml = (strip_tags($original['message']) != $original['message']);
                if ($isHtml) {
                    $message = sql_escape($original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($data['footer'])));
                } else {
                    $message = sql_escape($original['message'] . "\n\n---\n" . $data['footer']);
                }

                // Den neuen Entwurf einfügen
                // Wir setzen alle kritischen phpList-Felder auf sichere Standardwerte
                Sql_Query(sprintf(
                    'INSERT INTO %s (subject, fromline, message, entered, modified, status, template, messageformat, embargo, repeatinterval, repeatuntil, footer) 
                     VALUES ("%s", "%s", "%s", NOW(), NOW(), "draft", %d, "%s", NOW(), 0, NOW(), "%s")',
                    $GLOBALS['tables']['message'],
                    $subject,
                    $fromline,
                    $message,
                    intval($original['template']),
                    sql_escape($original['messageformat']),
                    sql_escape($original['footer']) // Dies ist der systemweite Footer von phpList
                ));
                
                $count++;
            }
        }
        echo "<div class='note' style='background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; margin: 20px 0;'>
                ✅ <strong>Success:</strong> $count personalized drafts created!
              </div>";
    }
}

// --- ANZEIGE: FORMULAR ---
echo '<form method="post" id="multiplierForm">';

// 1. Entwurf-Auswahl
echo '<div class="panel"><div class="content"><h3>1. Select Base Draft</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 50', $GLOBALS['tables']['message']));
echo '<select name="draft_id" style="width:100%; max-width:600px; padding: 8px;">';
while($d = Sql_Fetch_Assoc($drafts)) {
    echo "<option value='{$d['id']}'>[ID {$d['id']}] " . htmlspecialchars($d['subject']) . "</option>";
}
echo '</select></div></div>';

// 2. Empfänger-Auswahl
echo '<div class="panel"><div class="content"><h3>2. Select Recipients</h3>';
echo '<div style="margin-bottom: 10px;">
        <button type="button" onclick="toggleAll(true)" class="btn">Select All</button> 
        <button type="button" onclick="toggleAll(false)" class="btn">Deselect All</button>
      </div>';

$res = Sql_Query("SELECT id, name, email, footer FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common" style="width: 100%;">
        <thead><tr><th width="30"></th><th>Name</th><th>Email</th><th>Footer Preview</th></tr></thead>
        <tbody>';

if (Sql_Affected_Rows($res)) {
    while ($row = Sql_Fetch_Assoc($res)) {
        $preview = htmlspecialchars(substr($row['footer'], 0, 50)) . (strlen($row['footer']) > 50 ? '...' : '');
        echo "<tr>
                <td><input type='checkbox' name='selected_ids[]' value='{$row['id']}' class='rec-check'></td>
                <td><strong>" . htmlspecialchars($row['name']) . "</strong></td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td><small style='color:#666;'>" . $preview . "</small></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No data found in Draft_Multiplier_Data table.</td></tr>";
}
echo '</tbody></table>';

echo '<br><input type="submit" name="run_multiplier" value="Generate Personalized Drafts" class="btn btn-primary" style="padding: 10px 20px; cursor: pointer;">';
echo '</div></div></form>';

echo "<script>
function toggleAll(source) {
    var checkboxes = document.getElementsByClassName('rec-check');
    for(var i=0; i<checkboxes.length; i++) { checkboxes[i].checked = source; }
}
</script></div>";