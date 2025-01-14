# Story Filter Plugin

## Description
The **Story Filter Plugin** is a custom WordPress plugin that allows users to filter and display stories dynamically. It utilizes AJAX for seamless data fetching and provides a user-friendly interface for filtering content.

## Features
- Dynamic filtering using AJAX.
- Easy integration with WordPress themes.
- Customizable filtering options via the plugin settings.
- Lightweight and efficient.

## Installation
1. Download the plugin files or clone the repository:
   ```bash
   git clone https://github.com/mursalinmutton/Search-Filter-Plugin.git
   ```
2. Upload the plugin folder to your WordPress installation under `/wp-content/plugins/`.
3. Activate the plugin through the WordPress admin dashboard in the **Plugins** section.

## Usage
1. Add the shortcode `[sfp_filter]` to any page or post where you want to display the filter form and results.
2. Customize the filtering options by editing the plugin's PHP file if needed.

### Filter Form Example
The plugin provides a form for filtering, which includes fields for user input. Example:
```html
<form id="sfp-filter-form">
    <!-- Add your filter fields here -->
</form>
<div id="sfp-filter-results"></div>
```

## Scripts and AJAX
The plugin uses JavaScript (`script.js`) to handle the filtering process. Key features:
- Prevents default form submission.
- Sends a serialized form data request to the server.
- Updates the results dynamically without reloading the page.

### AJAX Endpoint
The plugin processes AJAX requests via the WordPress `admin-ajax.php` API. Ensure the following action is defined:
```php
add_action('wp_ajax_sfp_filter_stories', 'sfp_filter_stories');
add_action('wp_ajax_nopriv_sfp_filter_stories', 'sfp_filter_stories');
```

## Requirements
- WordPress 5.0 or higher.
- PHP 7.0 or higher.
- jQuery (included with WordPress).

## Development Notes
### Key Files:
- **`story-filter-plugin.php`**: Main plugin file, handles initialization and server-side logic.
- **`script.js`**: Manages the AJAX filtering process on the client side.

