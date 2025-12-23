<?php
if (!defined('PHPLISTINIT')) die();

// Fehleranzeige erzwingen (nur für diesen Test)
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo '<div class="container-fluid"><h1>Draft Multiplier: Create Copies</h1>';

// --- LOGIK: KOPIEREN ---
if (isset($_POST['run_multiplier'], $_POST['selected_ids'], $_POST['draft_id'])) {
    $draft_id = (int)$_POST['draft_id'];
    $selected_ids = $_POST['selected_ids']; 
    
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            $data = Sql_Fetch_Assoc_Query(sprintf("SELECT name, email, footer FROM Draft_Multiplier_Data WHERE id = %d", (int)$id));
            
            if ($data) {
                $newSubject = sql_escape($data['name'] . " - " . $original['subject']);
                $newFrom = sql_escape($data['name'] . ' <' . $data['email'] . '>');
                $footerContent = $data['footer'];
                
                $isHtml = (strip_tags($original['message']) != $original['message']);
                if ($isHtml) {
                    $newMessage = sql_escape($original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($footerContent)));
                } else {
                    $newMessage = sql_escape($original['message'] . "\n\n---\n" . $footerContent);
                }

                $query = sprintf(
                    'INSERT INTO %s (subject, fromline, message, entered, modified, status, template, messageformat, embargo, repeatinterval, repeatuntil, footer, sent, htmlformatted) 
                    SELECT "%s", "%s", "%s", NOW(), NOW(), "draft", template, messageformat, NOW(), 0, NOW(), footer, NOW(), htmlformatted 
                    FROM %s WHERE id = %d',
                    $GLOBALS['tables']['message'], $newSubject, $newFrom, $newMessage, $GLOBALS['tables']['message'], $draft_id
                );
                
                Sql_Query($query);
                $count++;
            }
        }
        echo "<div class='note' style='padding:10px; background:#d4edda;'>✅ $count personalized drafts created!</div>";
    }
}

// --- FORMULAR ---
$drafts = Sql_Query(sprintf('SELECT id, subject FROM %s WHERE status = "draft" ORDER BY id DESC LIMIT 20', $GLOBALS['tables']['message']));
$recipients = Sql_Query("SELECT id, name, email FROM Draft_Multiplier_Data ORDER BY name ASC");

echo '<form method="post">
    <div class="panel"><div class="content">
        <h3>1. Select Draft</h3>
        <select name="draft_id" style="width:100%;">';
        while($d = Sql_Fetch_Assoc($drafts)) {
            echo "<option value='{$d['id']}'>[ID {$d['id']}] ".htmlspecialchars($d['subject'])."</option>";
        }
echo '  </select>
    </div></div>
    <div class="panel"><div class="content">
        <h3>2. Select Recipients</h3>
        <table class="common" style="width:100%;">
            <thead><tr><th></th><th>Name</th><th>Email</th></tr></thead>';
            while($r = Sql_Fetch_Assoc($recipients)) {
                echo "<tr>
                    <td><input type='checkbox' name='selected_ids[]' value='{$r['id']}'></td>
                    <td>".htmlspecialchars($r['name'])."</td>
                    <td>".htmlspecialchars($r['email'])."</td>
                </tr>";
            }
echo '  </table>
        <br><input type="submit" name="run_multiplier" value="Generate" class="btn btn-primary">
    </div></div>
</form></div>';