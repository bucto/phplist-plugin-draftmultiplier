<?php
if (!defined('PHPLISTINIT')) die();

// 1. Logik zum Kopieren (wenn das Formular abgeschickt wurde)
if (isset($_POST['copy_draft']) && isset($_POST['draft_id'])) {
    $original_id = intval($_POST['draft_id']);
    $count = intval($_POST['copy_count']);
    
    // Wir holen uns die Daten des Originals
    $original = Sql_Fetch_Assoc_Query(sprintf(
        'SELECT * FROM %s WHERE id = %d',
        $GLOBALS['tables']['message'], $original_id
    ));

    if ($original) {
        for ($i = 1; $i <= $count; $i++) {
            // Wir erstellen eine Kopie (ID entfernen, Betreff anpassen)
            $copy = $original;
            unset($copy['id']); 
            $copy['subject'] = $original['subject'] . " (Kopie $i)";
            $copy['entered'] = date('Y-m-d H:i:s');
            $copy['modified'] = date('Y-m-d H:i:s');
            $copy['status'] = 'draft'; // Sicherstellen, dass es ein Entwurf bleibt

            // In die Datenbank schreiben
            $cols = array_keys($copy);
            $vals = array_map(function($v) { return "'" . sql_escape($v) . "'"; }, array_values($copy));
            
            Sql_Query(sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $GLOBALS['tables']['message'], implode(',', $cols), implode(',', $vals)
            ));
        }
        echo '<div class="note">' . $count . ' Kopie(n) von "' . htmlspecialchars($original['subject']) . '" wurden erstellt.</div>';
    }
}

// 2. Anzeige der Tabelle mit Auswahlmöglichkeit
echo '<div class="container-fluid">';
echo '<div class="panel"><div class="content">';
echo '<h3>' . s('Entwurf auswählen und vervielfältigen') . '</h3>';

$req = Sql_Query(sprintf(
    'SELECT id, subject FROM %s WHERE status = "draft" ORDER BY entered DESC',
    $GLOBALS['tables']['message']
));

if (Sql_Affected_Rows($req)) {
    echo '<form method="post" action="">';
    echo '<table class="common">';
    echo '<thead><tr><th>Auswahl</th><th>ID</th><th>Betreff</th></tr></thead>';
    while ($row = Sql_Fetch_Assoc($req)) {
        echo '<tr>';
        echo '<td><input type="radio" name="draft_id" value="' . $row['id'] . '" required></td>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['subject']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';

    echo '<div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ccc;">';
    echo '<label>Anzahl der Kopien: </label>';
    echo '<input type="number" name="copy_count" value="1" min="1" max="50" style="width: 60px; margin-right: 10px;">';
    echo '<input type="submit" name="copy_draft" value="Vervielfältigen" class="btn btn-primary">';
    echo '</div>';
    echo '</form>';
} else {
    echo '<p>Keine Entwürfe gefunden.</p>';
}

echo '</div></div>';
echo '</div>';