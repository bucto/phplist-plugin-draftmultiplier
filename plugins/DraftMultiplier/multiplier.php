<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

// --- LOGIK: KOPIEREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    // Basis-Daten des Originals holen
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", intval($id)));
            
            if ($data) {
                // Betreff und Absender vorbereiten
                $newSubject = sql_escape($data['name'] . " - " . $original['subject']);
                $newFrom = sql_escape($data['name'] . ' <' . $data['email'] . '>');
                
                // Footer vorbereiten
                $isHtml = (strip_tags($original['message']) != $original['message']);
                $footerContent = $data['footer'];
                if ($isHtml) {
                    $newMessage = sql_escape($original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($footerContent)));
                } else {
                    $newMessage = sql_escape($original['message'] . "\n\n---\n" . $footerContent);
                }

                // SCHRITT: Direkte Kopie per SQL (Sicherste Methode gegen White Screen)
                // Wir erzeugen eine neue Zeile, indem wir fast alles aus dem Original übernehmen
                Sql_Query(sprintf(
                    'INSERT INTO %s (subject, fromline, message, entered, modified, status, template, messageformat, embargo, repeatinterval, repeatuntil, footer, sent, htmlformatted) 
                    SELECT "%s", "%s", "%s", NOW(), NOW(), "draft", template, messageformat, NOW(), 0, NOW(), footer, NOW(), htmlformatted 
                    FROM %s WHERE id = %d',
                    $GLOBALS['tables']['message'],
                    $newSubject,
                    $newFrom,
                    $newMessage,
                    $GLOBALS['tables']['message'],
                    $draft_id
                ));
                
                $count++;
            }
        }
        echo "<div class='note' style='padding:15px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;'>✅ Success: $count personalized drafts created!</div>";
    }
}

// --- ANZEIGE: FORMULAR ---
echo '<form method="post">';
echo '<div class="panel"><div class="content"><h3>1. Select Base Draft</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 50', $GLOBALS['tables']['message']));
echo '<select name="draft_id" style="width:100%; max-width:600px; padding:5px;">';
while($d = Sql_Fetch_Assoc($drafts)) {
    echo "<option value='{$d['id']}'>[ID {$d['id']}] " . htmlspecialchars($d['subject']) . "</option>";
}
echo '</select></div></div>';

echo '<div class="panel"><div class="content"><h3>2. Select Recipients</h3>';
$res = Sql_Query("SELECT id, name, email, footer FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common" style="width:100%;">
        <thead><tr><th></th><th>Name</th><th>Email</th><th>Footer</th></tr></thead>
        <tbody>';
while ($row = Sql_Fetch_Assoc($res)) {
    echo "<tr>
            <td><input type='checkbox' name='selected_ids[]' value='{$row['id']}' class='rec-check'></td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td><small>" . htmlspecialchars(substr($row['footer'] ?? '', 0, 40)) . "...</small></td>
          </tr>";
}
echo '</tbody></table>';
echo '<br><input type="submit" name="run_multiplier" value="Generate Personalized Copies" class="btn btn-primary" style="padding:10px 20px;">';
echo '</div></div></form>';
echo "<script>function toggleAll(s){var c=document.getElementsByClassName('rec-check');for(var i=0;i<c.length;i++){c[i].checked=s;}}</script></div>";