<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        $errors = array();

        foreach ($selected_ids as $id) {
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", intval($id)));
            
            if ($data) {
                $subject = $data['name'] . " - " . $original['subject'];
                $fromline = $data['name'] . ' <' . $data['email'] . '>';
                
                $isHtml = (strip_tags($original['message']) != $original['message']);
                if ($isHtml) {
                    $message = $original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($data['footer']));
                } else {
                    $message = $original['message'] . "\n\n---\n" . $data['footer'];
                }

                // Stabiler INSERT-Befehl mit ALLEN wichtigen Feldern
                $query = sprintf(
                    'INSERT INTO %s 
                    (subject, fromline, message, entered, modified, status, template, messageformat, embargo, repeatinterval, repeatuntil, footer, sent, htmlformatted, tofield, replyto) 
                    VALUES ("%s", "%s", "%s", NOW(), NOW(), "draft", %d, "%s", NOW(), 0, NOW(), "%s", NOW(), %d, "", "")',
                    $GLOBALS['tables']['message'],
                    sql_escape($subject),
                    sql_escape($fromline),
                    sql_escape($message),
                    intval($original['template']),
                    sql_escape($original['messageformat']),
                    sql_escape($original['footer']),
                    $isHtml ? 1 : 0
                );

                $result = Sql_Query($query);
                
                if ($result) {
                    $count++;
                } else {
                    $errors[] = "Fehler bei ID {$id}: " . mysqli_error($GLOBALS['mysqli']);
                }
            }
        }

        if ($count > 0) {
            echo "<div class='note' style='background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; margin: 20px 0;'>
                    ✅ <strong>Erfolg:</strong> $count personalisierte Entwürfe wurden erstellt!
                  </div>";
        }

        if (!empty($errors)) {
            echo "<div class='note' style='background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; margin: 20px 0;'>
                    <strong>Datenbank-Fehler:</strong><br>" . implode("<br>", $errors) . "
                  </div>";
        }
    }
}

// --- FORMULAR-TEIL (UNVERÄNDERT) ---
echo '<form method="post" id="multiplierForm">';
echo '<div class="panel"><div class="content"><h3>1. Basis-Entwurf wählen</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 50', $GLOBALS['tables']['message']));
echo '<select name="draft_id" style="width:100%; max-width:600px; padding: 8px;">';
while($d = Sql_Fetch_Assoc($drafts)) {
    echo "<option value='{$d['id']}'>[ID {$d['id']}] " . htmlspecialchars($d['subject']) . "</option>";
}
echo '</select></div></div>';

echo '<div class="panel"><div class="content"><h3>2. Empfänger wählen</h3>';
echo '<div style="margin-bottom: 10px;"><button type="button" onclick="toggleAll(true)" class="btn">Alle auswählen</button> <button type="button" onclick="toggleAll(false)" class="btn">Auswahl aufheben</button></div>';
$res = Sql_Query("SELECT id, name, email, footer FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common" style="width: 100%;"><thead><tr><th width="30"></th><th>Name</th><th>Email</th><th>Footer Vorschau</th></tr></thead><tbody>';
if (Sql_Affected_Rows($res)) {
    while ($row = Sql_Fetch_Assoc($res)) {
        echo "<tr><td><input type='checkbox' name='selected_ids[]' value='{$row['id']}' class='rec-check'></td><td><strong>" . htmlspecialchars($row['name']) . "</strong></td><td>" . htmlspecialchars($row['email']) . "</td><td><small>" . htmlspecialchars(substr($row['footer'], 0, 50)) . "</small></td></tr>";
    }
}
echo '</tbody></table><br><input type="submit" name="run_multiplier" value="Entwürfe jetzt erstellen" class="btn btn-primary" style="padding: 10px 20px;"></div></div></form>';
echo "<script>function toggleAll(source) { var checkboxes = document.getElementsByClassName('rec-check'); for(var i=0; i<checkboxes.length; i++) { checkboxes[i].checked = source; } }</script></div>";