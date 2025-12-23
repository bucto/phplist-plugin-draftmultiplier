<?php
if (!defined('PHPLISTINIT')) die();

// --- LOGIK: KOPIEREN AUSFÜHREN ---
if (isset($_POST['run_multiplier']) && isset($_POST['selected_ids']) && isset($_POST['draft_id'])) {
    $draft_id = intval($_POST['draft_id']);
    $selected_ids = $_POST['selected_ids']; 
    
    $original = Sql_Fetch_Assoc_Query(sprintf('SELECT * FROM %s WHERE id = %d', $GLOBALS['tables']['message'], $draft_id));
    
    if ($original) {
        $count = 0;
        foreach ($selected_ids as $id) {
            // Daten des Empfängers aus deiner Tabelle holen
            $data = Sql_Fetch_Assoc_Query("SELECT name, footer, email FROM Draft_Multiplier_Data WHERE id = " . intval($id));
            
            if ($data) {
                $copy = $original;
                unset($copy['id']); // ID entfernen für neuen Eintrag
                
                // 1. Betreff personalisieren
                $copy['subject'] = $data['name'] . " - " . $original['subject'];
                
                // 2. Absender (From Line) anpassen
                // phpList erwartet hier oft das Format: "Name <email@domain.com>"
                $copy['fromline'] = sprintf('%s <%s>', sql_escape($data['name']), sql_escape($data['email']));
                
                // 3. Footer an die Nachricht (message) anhängen
                // Wir fügen zwei Zeilenumbrüche und eine Trennlinie hinzu
                $footer_content = "\n\n---\n" . $data['footer'];
                $copy['message'] = $original['message'] . $footer_content;
                
                // Zeitstempel aktualisieren
                $copy['entered'] = date('Y-m-d H:i:s');
                $copy['modified'] = date('Y-m-d H:i:s');
                
                // In die Datenbank schreiben
                $cols = array_keys($copy);
                $vals = array_map(function($v) { 
                    return $v === null ? "NULL" : "'" . sql_escape($v) . "'"; 
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
        echo "<div class='note' style='color:green; font-weight:bold;'>Success: $count personalized drafts created!</div>";
    }
}

// --- (Der Rest der Datei für das Formular bleibt gleich wie in Version 1.1.1) ---
// ... (Anzeige-Code von vorhin einfügen)