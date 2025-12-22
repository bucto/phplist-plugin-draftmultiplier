# phpList DraftMultiplier Pro

A powerful **phpList** plugin designed to rapidly duplicate a base campaign draft for multiple recipients while personalizing the subject line and footer.

## Features

* **Select Base Draft:** Choose any existing campaign from your drafts.
* **Granular Selection:** Use checkboxes to select exactly which recipients from your custom database table should receive a duplicated draft.
* **Bulk Actions:** "Select All" or "Deselect All" functionality for quick management.
* **Automatic Personalization:** * The recipient's **Name** is prepended to the subject line.
    * A unique, individual **Footer** is automatically appended to the end of the message body.
* **Non-Destructive:** Creates fresh drafts without modifying your original base campaign.

## Installation

1.  In your phpList admin area, navigate to **System** -> **Manage Plugins**.
2.  Copy and paste the following URL into the "Install a new plugin" field:
    `https://github.com/bucto/phplist-plugin-draftmultiplier/archive/refs/heads/main.zip`
3.  Click **Install**.
4.  Ensure the plugin is enabled in the list (green bulb icon).

## Prerequisites

The plugin requires a specific database table to store recipient data. Create this table (e.g., via phpMyAdmin) in your phpList database:

```sql
CREATE TABLE IF NOT EXISTS Draft_Multiplier_Data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    footer TEXT
);