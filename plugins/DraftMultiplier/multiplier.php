<?php
if (!defined('PHPLISTINIT')) die(); // Sicherheitsabfrage

echo '<div class="container-fluid">';
echo '<h1>' . s('Draft Multiplier Tool') . '</h1>';
echo '<div class="panel"><div class="content">';
echo '<h3>' . s('Verbindung steht!') . '</h3>';
echo '<p>' . s('Diese Seite wird nun aus der separaten Datei geladen.') . '</p>';

// Entwürfe abrufen
$req = Sql_Query(sprintf(
    'SELECT id, subject FROM %s WHERE status = "draft" ORDER BY entered DESC LIMIT 10',
    $GLOBALS['tables']['message']
));

if (Sql_Affected_Rows($req)) {
    echo '<table class="common">';
    echo '<thead><tr><th>ID</th><th>Betreff</th></tr></thead>';
    while ($row = Sql_Fetch_Assoc($req)) {
        echo '<tr><td>' . $row['id'] . '</td><td>' . htmlspecialchars($row['subject']) . '</td></tr>';
    }
    echo '</table>';
} else {
    echo '<p>Keine Entwürfe gefunden.</p>';
}

echo '</div></div>';
echo '</div>';