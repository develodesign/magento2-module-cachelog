# Magento 2 Cache Log Module by Develodesign

This module provides a logging mechanism for cache invalidation and cleaning events in Magento 2. It helps developers and administrators track when, how, and from where the cache is being flushed, which is invaluable for debugging complex caching issues, especially those related to Varnish and third-party integrations.

## Features

- Logs cache cleaning events to a dedicated database table.
- Records details like the invalidation type (tags or type list), specific cache tags, the area of the application that triggered the cleaning (admin, frontend, cron), and a timestamp.
- Specifically logs Varnish purge actions, which can be very numerous and performance-intensive.
- Provides an admin grid to view, filter, and search cache log entries.
- Allows for mass deletion of log entries from the admin grid.
- Module and logging functionality can be enabled or disabled via system configuration.

## Installation

1.  **Require the module using Composer:**
    ```bash
    composer require develodesign//magento2-module-cachelog
    ```

2.  **Enable the module:**
    ```bash
    bin/magento module:enable Develodesign_CacheLog
    ```

3.  **Run setup scripts:**
    ```bash
    bin/magento setup:upgrade
    ```

4.  **Compile dependencies:**
    ```bash
    bin/magento setup:di:compile
    ```

5.  **Deploy static content (if in production mode):**
    ```bash
    bin/magento setup:static-content:deploy
    ```

## Configuration

1.  Navigate to **Stores > Configuration > Develodesign > Cache Log**.
2.  In the **General Settings** section, you can:
    -   **Enable Cache Logging**: Globally turn the cache logging functionality on or off.
    -   **Enable Varnish Purge Logging**: Specifically enable or disable logging for Varnish purges. It is recommended to disable this after a short debugging period, as it can generate a large number of log entries.

## Usage

To view the cache cleaning logs, navigate to **System > Tools > Clean Cache Log** in the Magento admin panel.

## License

This module is licensed under the [Open Software License (OSL 3.0)](https://opensource.org/licenses/osl-3.0.php) and the [Academic Free License (AFL 3.0)](https://opensource.org/licenses/afl-3.0.php).
