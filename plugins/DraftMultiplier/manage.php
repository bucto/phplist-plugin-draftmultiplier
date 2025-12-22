<?php
if (!defined('PHPLISTINIT')) die();

// --- LOGIK: EINTRAG LÖSCHEN ---
if (isset($_GET['delete'])) {
    Sql_Query("DELETE FROM Draft_Multiplier_Data WHERE id = " . intval($_GET['delete']));
    echo '<div class="note">Entry deleted.</div>';
}

// --- LOGIK: EINTRAG HINZUFÜGEN ---
if (isset($_POST['add_entry'])) {
    Sql_Query(sprintf(
        "INSERT INTO Draft_Multiplier_Data (name, email, footer) VALUES ('%s', '%s', '%s')",
        sql_escape($_POST['name']), sql_escape($_POST['email']), sql_escape($_POST['footer'])
    ));
    echo '<div class="note">Entry added successfully.</div>';
}

echo '<div class="container-fluid"><h1>Manage Recipient Data</h1>';

// Formular zum Hinzufügen
echo '<div class="panel"><div class="content"><h3>Add New Recipient</h3>';
echo '<form method="post">
    <input type="text" name="name" placeholder="Name (for Subject)" required style="width:200px;"> 
    <input type="email" name="email" placeholder="Email" style="width:200px;"> <br><br>
    <textarea name="footer" placeholder="Individual Footer Text" style="width:100%; height:80px;"></textarea><br>
    <input type="submit" name="add_entry" value="Add to List" class="btn btn-primary">
</form></div></div>';

// Liste der vorhandenen Einträge
echo '<div class="panel"><div class="content"><h3>Existing Entries</h3>';
$res = Sql_Query("SELECT * FROM Draft_Multiplier_Data ORDER BY id DESC");
echo '<table class="common">
    <thead><tr><th>Name</th><th>Email</th><th>Footer</th><th>Action</th></tr></thead>';
while ($row = Sql_Fetch_Assoc($res)) {
    echo "<tr>
        <td>" . htmlspecialchars($row['name']) . "</td>
        <td>" . htmlspecialchars($row['email']) . "</td>
        <td><small>" . nl2br(htmlspecialchars($row['footer'])) . "</small></td>
        <td><a href='./?page=manage&pi=DraftMultiplier&delete=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
    </tr>";
}
echo '</table></div></div></div>';