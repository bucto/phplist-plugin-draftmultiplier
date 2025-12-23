<?php
if (!defined('PHPLISTINIT')) die();

$msg = '';
$edit_entry = null;

// --- LOGIK: EINTRAG LÖSCHEN ---
if (isset($_GET['delete'])) {
    Sql_Query("DELETE FROM Draft_Multiplier_Data WHERE id = " . intval($_GET['delete']));
    $msg = '<div class="note">Entry deleted.</div>';
}

// --- LOGIK: EINTRAG LADEN FÜR EDIT ---
if (isset($_GET['edit'])) {
    $edit_entry = Sql_Fetch_Assoc_Query("SELECT * FROM Draft_Multiplier_Data WHERE id = " . intval($_GET['edit']));
}

// --- LOGIK: SPEICHERN (NEU ODER UPDATE) ---
if (isset($_POST['save_entry'])) {
    $name = sql_escape($_POST['name']);
    $email = sql_escape($_POST['email']);
    $footer = sql_escape($_POST['footer']);
    $id = intval($_POST['entry_id']);

    if ($id > 0) {
        // UPDATE
        Sql_Query(sprintf(
            "UPDATE Draft_Multiplier_Data SET name = '%s', email = '%s', footer = '%s' WHERE id = %d",
            $name, $email, $footer, $id
        ));
        $msg = '<div class="note">Entry updated successfully.</div>';
    } else {
        // INSERT
        Sql_Query(sprintf(
            "INSERT INTO Draft_Multiplier_Data (name, email, footer) VALUES ('%s', '%s', '%s')",
            $name, $email, $footer
        ));
        $msg = '<div class="note">New entry added successfully.</div>';
    }
    $edit_entry = null; // Formular nach Speichern leeren
}

echo '<div class="container-fluid"><h1>Manage Recipient Data</h1>';
echo $msg;

// --- FORMULAR: HINZUFÜGEN / EDITIEREN ---
$form_title = $edit_entry ? 'Edit Recipient' : 'Add New Recipient';
$btn_label = $edit_entry ? 'Update Entry' : 'Add to List';
$val_name = $edit_entry ? htmlspecialchars($edit_entry['name']) : '';
$val_email = $edit_entry ? htmlspecialchars($edit_entry['email']) : '';
$val_footer = $edit_entry ? htmlspecialchars($edit_entry['footer']) : '';
$val_id = $edit_entry ? intval($edit_entry['id']) : 0;

echo '<div class="panel"><div class="content"><h3>' . $form_title . '</h3>';
echo '<form method="post" action="./?page=manage&pi=DraftMultiplier">
    <input type="hidden" name="entry_id" value="' . $val_id . '">
    <input type="text" name="name" value="' . $val_name . '" placeholder="Name (for Subject)" required style="width:200px;"> 
    <input type="email" name="email" value="' . $val_email . '" placeholder="Email" style="width:200px;"> <br><br>
    <textarea name="footer" placeholder="Individual Footer Text" style="width:100%; height:80px;">' . $val_footer . '</textarea><br>
    <input type="submit" name="save_entry" value="' . $btn_label . '" class="btn btn-primary"> ';
    if ($edit_entry) echo '<a href="./?page=manage&pi=DraftMultiplier" class="btn">Cancel</a>';
echo '</form></div></div>';

// --- LISTE: ANZEIGEN ---
echo '<div class="panel"><div class="content"><h3>Existing Entries</h3>';
$res = Sql_Query("SELECT * FROM Draft_Multiplier_Data ORDER BY name ASC");
echo '<table class="common">
    <thead><tr><th>Name</th><th>Email</th><th>Footer Preview</th><th>Actions</th></tr></thead>';
while ($row = Sql_Fetch_Assoc($res)) {
    echo "<tr>
        <td>" . htmlspecialchars($row['name']) . "</td>
        <td>" . htmlspecialchars($row['email']) . "</td>
        <td><small>" . nl2br(htmlspecialchars(substr($row['footer'], 0, 100))) . (strlen($row['footer']) > 100 ? '...' : '') . "</small></td>
        <td>
            <a href='./?page=manage&pi=DraftMultiplier&edit=" . $row['id'] . "' class='button'>Edit</a> 
            <a href='./?page=manage&pi=DraftMultiplier&delete=" . $row['id'] . "' class='button' onclick='return confirm(\"Are you sure?\")'>Delete</a>
        </td>
    </tr>";
}
echo '</table></div></div></div>';

echo '<hr><div style="text-align: center; color: #666; font-size: 0.9em; padding: 20px;">';
echo 'Plugin developed by <strong>Thomas Bücken</strong> | ';
echo '<a href="https://github.com/bucto/phplist-plugin-draftmultiplier" target="_blank" style="text-decoration: none; color: #007bff;">GitHub Project Page</a>';
echo '</div>';