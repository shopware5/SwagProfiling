# SwagProfiling
## Description
This free toolbar is a must-have for all developers working with Shopware. This plugin displays information in the frontend, which would otherwise have to be searched for in the backend (i.e. a chronological display or all Smarty variables or triggered events).

### Compressed view
In the toolbar view, the data is summarized and shown compressed, allowing you to see all database requests, triggered events and loaded templates in one overview.

### Detailed view
Access the detailed view by clicking on the toolbar at the bottom of the shop. Individual areas will then expand to show more detail.

#### Config
The config area of the toolbar provides in-depth insight into the loaded Shopware configuration, allowing you to monitor the configuration while the shop is live.

#### Request
The request area provides all data concerning the current request and its response.

#### Session
This area displays the data in use during the current session.

#### Template
The template area is especially interesting for developers creating adjustments in the frontend. A registered template directory is displayed, as well as all available data for these templates.

#### Events
Since Shopware extensions are mainly based on events, this area was created to display both which events are being used by the system and the data that is being modified by these events.

#### Queries
As the name suggests, the query area lists all queries that are being processed in the current database request.

#### Emails
Should an email have been sent via the “Shopware_Components_Mail” object, messages will be listed here.

#### Cache
The cache area of the toolbar shares exactly what and since when something has been in the cache.

#### Exception
Exceptions which occur will be listed here.

#### PHP
The PHP area of the toolbar displays all phpinfo() information.

#### Trace
All functions or the Shopware stack will be listed here chronologically.

#### Ajax Request
This area lists every Ajax request that has been called by the current page.

## Images
![Toolbar](https://github.com/shopwareLabs/SwagProfiling/raw/master/toolbar.png "Toolbar")
![Detailed View](https://github.com/shopwareLabs/SwagProfiling/raw/master/detailView.png "Detailed View")

## License
The MIT License (MIT). Please see [License File](https://github.com/shopwareLabs/SwagImportExport/blob/master/LICENSE "License File") for more information.