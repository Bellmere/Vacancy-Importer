<?php
/**
 * Plugin Name: Vacancy Importer
 * Description: Custom plugin to import job vacancies.
 * Version: 1.0
 * Author: Saur0n
 */

use VacanceImporter\PostTypeRegister;
use VacanceImporter\AcfFieldsRegister;
use VacanceImporter\AdminPage;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// CPT + auto create ACF
register_activation_hook(__FILE__, function() {
    PostTypeRegister::register();
    if (function_exists('acf_add_local_field_group')) {
        AcfFieldsRegister::register();
    }
});

// CPT every time
add_action('init', function() {
    PostTypeRegister::register();
});

// Check and register ACF
add_action('acf/init', function() {
    AcfFieldsRegister::register();
});

// add button to admin panel
add_action('admin_menu', function() {
    AdminPage::register();
});

//Rest api init
add_action('rest_api_init', function() {
    \VacanceImporter\API\Vacancy\controller\VacancyController::register_routes();
});


add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'vacancy-importer-frontend',
        plugin_dir_url(__FILE__) . 'view/dist/index.js',
        ['wp-element', 'wp-api-fetch'],
        filemtime(plugin_dir_path(__FILE__) . 'view/dist/index.js'),
        true
    );

    wp_localize_script('vacancy-importer-frontend', 'wpApiSettings', [
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest'),
    ]);

    wp_enqueue_style(
        'vacancy-importer-style',
        plugin_dir_url(__FILE__) . 'view/dist/style.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'view/dist/style.css')
    );
});


// short code
add_shortcode('vacancy_app', function() {
    return '<div id="vacancy-app"></div>';
});
