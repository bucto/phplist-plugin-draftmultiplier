<?php
if (!defined('PHPLISTINIT')) die();

// WICHTIG: phpList Kern-Funktionen laden, falls sie noch nicht da sind
if (is_file(dirname(__FILE__) . '/../../admin/adminattributes.php')) {
    include_once dirname(__FILE__) . '/../../admin/adminattributes.php';
}

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

// --- LOGIK: KOPIEREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    // Prüfen, ob die Funktion copyMessage existiert
    if (!function_exists('copyMessage')) {
        echo '<div class="note" style="color:red;">Error: System function copyMessage not found. Please contact support.</div>';
    } else {
        $count = 0;
        foreach ($selected_ids as $id) {
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", intval($id)));
            
            if ($data) {
                // 1. Die interne phpList Kopier-Funktion nutzen
                $new_message_id = copyMessage($draft_id);
                
                if ($new_message_id) {
                    // 2. Personalisierung vorbereiten
                    $subject = $data['name'] . " - " . $_POST['original_subject_hidden'];
                    $fromline = $data['name'] . ' <' . $data['email'] . '>';
                    
                    // Nachrichteninhalt laden
                    $msg_data = Sql_Fetch_Assoc_Query(sprintf('SELECT message FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $new_message_id));
                    $original_text = $msg_data['message'];
                    
                    $isHtml = (strip_tags($original_text) != $original_text);
                    if ($isHtml) {
                        $new_message = $original_text . '<br><br><hr><br>' . nl2br(htmlspecialchars($data['footer']));
                    } else {
                        $new_message = $original_text . "\n\n---\n" . $data['footer'];
                    }

                    // 3. Update der Kopie
                    Sql_Query(sprintf(
                        'UPDATE %s SET subject = "%s", fromline = "%s", message = "%s", status = "draft", modified = NOW() WHERE id = %d',
                        $GLOBALS['tables']['message'],
                        sql_escape($subject),
                        sql_escape($fromline),
                        sql_escape($new_message),
                        $new_message_id
                    ));
                    
                    $count++;
                }
            }
        }
        echo "<div class='note' style='padding:15px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;'>✅ Success: $count personalized drafts created!</div>";
    }
}

// --- ANZEIGE: FORMULAR ---
echo '<form method="post">';

echo '<div class="panel"><div class="content"><h3>1. Select Base Draft</h3>';
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 50', $GLOBALS['tables']['message']));
echo '<select name="draft_id" id="draft_select" style="width:100%; max-width:600px; padding:5px;" onchange="updateSubject()">';
while($d = Sql_Fetch_Assoc($drafts)) {
    echo "<option value='{$d['id']}' data-subject='".htmlspecialchars($d['subject'])."'>[ID {$d['id']}] " . htmlspecialchars($d['subject']) . "</option>";
}
echo '</select>';
echo '<input type="hidden" name="original_subject_hidden" id="original_subject_hidden" value="">';
echo '</div></div>';

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
            <td><small>" . htmlspecialchars(substr($row['footer'] ?? '', 0, 40)) . "...</small></td>
          </tr>";
}
echo '</tbody></table>';

echo '<br><input type="submit" name="run_multiplier" value="Generate Personalized Copies" class="btn btn-primary" style="padding:10px 20px;">';
echo '</div></div></form>';

echo "<script>
function toggleAll(source) {
    var checkboxes = document.getElementsByClassName('rec-check');
    for(var i=0; i<checkboxes.length; i++) { checkboxes[i].checked = source; }
}
function updateSubject() {
    var sel = document.getElementById('draft_select');
    if(sel.options[sel.selectedIndex]) {
        var sub = sel.options[sel.selectedIndex].getAttribute('data-subject');
        document.getElementById('original_subject_hidden').value = sub;
    }
}
updateSubject();
</script></div>";