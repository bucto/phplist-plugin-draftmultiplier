<?php
if (!defined('PHPLISTINIT')) die();

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

// --- LOGIK: KOPIEREN ---
if (isset($_POST['run_multiplier'], $_POST['selected_ids'], $_POST['draft_id'])) {
    $draft_id = (int)$_POST['draft_id'];
    $selected_ids = $_POST['selected_ids']; 
    
    // Original laden
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", (int)$id));
            
            if ($data) {
                // Felder vorbereiten basierend auf deiner Tabellenstruktur
                $newSubject = sql_escape($data['name'] . " - " . $original['subject']);
                $newFrom = sql_escape($data['name'] . ' <' . $data['email'] . '>');
                $footerContent = $data['footer'];
                
                // HTML oder Text?
                $isHtml = ($original['htmlformatted'] == 1);
                if ($isHtml) {
                    $newMessage = sql_escape($original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($footerContent)));
                    $newTextMessage = sql_escape($original['textmessage'] . "\n\n---\n" . $footerContent);
                } else {
                    $newMessage = sql_escape($original['message'] . "\n\n---\n" . $footerContent);
                    $newTextMessage = $newMessage;
                }

                // INSERT INTO ... SELECT (angepasst an DEINE Spalten)
                $query = sprintf(
                    'INSERT INTO %s 
                    (uuid, subject, fromfield, tofield, replyto, message, textmessage, footer, entered, modified, embargo, repeatinterval, repeatuntil, status, htmlformatted, sendformat, template, owner) 
                    SELECT UUID(), "%s", "%s", tofield, replyto, "%s", "%s", footer, NOW(), NOW(), NOW(), 0, NOW(), "draft", htmlformatted, sendformat, template, owner 
                    FROM %s WHERE id = %d',
                    $GLOBALS['tables']['message'],
                    $newSubject, 
                    $newFrom, 
                    $newMessage,
                    $newTextMessage,
                    $GLOBALS['tables']['message'], 
                    $draft_id
                );
                
                Sql_Query($query);
                $count++;
            }
        }
        echo "<div class='note' style='padding:15px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;'>✅ Success: $count personalized drafts created!</div>";
    }
}

// --- FORMULAR ---
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 50', $GLOBALS['tables']['message']));
$recipients = Sql_Query("SELECT id, name, email FROM Draft_Multiplier_Data ORDER BY name ASC");

echo '<form method="post">
    <div class="panel"><div class="content">
        <h3>1. Select Base Draft</h3>
        <select name="draft_id" style="width:100%; max-width:600px; padding:5px;">';
        while($d = Sql_Fetch_Assoc($drafts)) {
            echo "<option value='{$d['id']}'>[ID {$d['id']}] ".htmlspecialchars($d['subject'])."</option>";
        }
echo '  </select>
    </div></div>
    <div class="panel"><div class="content">
        <h3>2. Select Recipients</h3>
        <table class="common" style="width:100%;">
            <thead><tr><th width="30"></th><th>Name</th><th>Email</th></tr></thead><tbody>';
            while($r = Sql_Fetch_Assoc($recipients)) {
                echo "<tr>
                    <td><input type='checkbox' name='selected_ids[]' value='{$r['id']}'></td>
                    <td>".htmlspecialchars($r['name'])."</td>
                    <td>".htmlspecialchars($r['email'])."</td>
                </tr>";
            }
echo '  </tbody></table>
        <br><input type="submit" name="run_multiplier" value="Generate Personalized Drafts" class="btn btn-primary" style="padding:10px 20px;">
    </div></div>
</form></div>';