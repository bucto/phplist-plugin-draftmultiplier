# phpList DraftMultiplier

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
    `https://github.com/bucto/phplist-plugin-draftmultiplier/archive/master.zip`
3.  Click **Install**.
4.  Ensure the plugin is enabled in the list.


## How to Use

### 1. Preparing Recipient Data
Before multiplying, you need to add recipients to the plugin's database:
1. Navigate to **System** -> **Draft Multiplier: Manage Recipients**.
2. Use the form to add a **Name** (will be prepended to the subject), an **Email** (for reference), and an **Individual Footer**.
3. You can **Edit** or **Delete** existing entries in the list below the form.

### 2. Creating Personalized Copies
Once your recipients are set up:
1. Create a standard **Campaign Draft** in phpList as your template.
2. Navigate to **System** -> **Draft Multiplier: Create Copies**.
3. **Select Base Draft:** Choose your template from the dropdown menu.
4. **Select Recipients:** Use the checkboxes to pick the people you want a copy for. Use "Select All" for bulk actions.
5. **Process:** Click **Create Marked Drafts**.
6. **Result:** Check **Campaigns** -> **List of Drafts**. You will find new drafts named `[Name] - [Original Subject]` with the individual footer appended to the message.

## Menu Structure

* **Draft Multiplier: Create Copies**: The main tool for generating campaigns.
* **Draft Multiplier: Manage Recipients**: The management area for your personalization data.

## Author
* **bucto** - *Initial development*