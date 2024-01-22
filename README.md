# ListBoss UI for SiteBoss®

This pckage adds a page to the SiteBoss CMS to show the results of mailings.

## Installation

### 1. Add the package

Run composer:

```bash
composer require notfoundnl/siteboss-listboss
```
No artisan commands or migrations are required.

### 2. Update your .env file

Add these values to your `.env` file adding your own API key. These values may already be present.

```env
LISTBOSS_ENDPOINT=https://listboss.nl/v2/
LISTBOSS_API_KEY=
```

### 3. Add the page to the CMS

To add the menu item add something like this, make sure to change the rights to the required rights.


```json
[
    {
        "icon": "list",
        "title": "Jobs",
        "path": "/table/jobs/",
        "rights": "admin"
    }
]
```