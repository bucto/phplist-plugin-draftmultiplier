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
                $footer_text = $data['footer'];
                
                if ($isHtml) {
                    $message = $original['message'] . '<br><br><hr><br>' . nl2br(htmlspecialchars($footer_text));
                } else {
                    $message = $original['message'] . "\n\n---\n" . $footer_text;
                }

                // Wir bauen ein Array für die Spalten, um sicherzugehen, dass Sonderzeichen korrekt behandelt werden
                $query = sprintf(
                    'INSERT INTO %s 
                    (subject, fromline, message, entered, modified, status, template, messageformat, embargo, repeatinterval, repeatuntil, footer, sent, htmlformatted) 
                    VALUES ("%s", "%s", "%s", NOW(), NOW(), "draft", %d, "%s", NOW(), 0, NOW(), "%s", NOW(), %d)',
                    $GLOBALS['tables']['message'],
                    sql_escape($subject),
                    sql_escape($fromline),
                    sql_escape($message),
                    intval($original['template']),
                    sql_escape($original['messageformat']),
                    sql_escape($original['footer']), // Das ist der SYSTEM-Footer
                    $isHtml ? 1 : 0
                );

                // Ausführen und Fehler detailliert abfangen
                if (Sql_Query($query)) {
                    $count++;
                } else {
                    // Wenn Sql_Query fehlschlägt, versuchen wir den Fehler direkt aus dem mysqli Objekt zu fischen
                    $dbError = mysqli_error($GLOBALS['database_connection']); 
                    $errors[] = "ID {$id} (" . htmlspecialchars($data['name']) . "): " . ($dbError ? $dbError : "Unbekannter SQL-Fehler (Check permissions)");
                }
            }
        }

        if ($count > 0) {
            echo "<div class='note' style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px;'>✅ $count Entwürfe erstellt.</div>";
        }

        if (!empty($errors)) {
            echo "<div class='note' style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin-top:10px;'>
                    <strong>Details zu den Fehlern:</strong><br>" . implode("<br>", $errors) . "
                  </div>";
        }
    }
}
// ... Rest der Datei (Formular) bleibt gleich