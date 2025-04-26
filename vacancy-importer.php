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

// CPT и ACF при активации
register_activation_hook(__FILE__, function() {
    PostTypeRegister::register();
    if (function_exists('acf_add_local_field_group')) {
        AcfFieldsRegister::register();
    }
});

// CPT каждый раз при init
add_action('init', function() {
    PostTypeRegister::register();
});

// Проверка и регистрация ACF полей
add_action('acf/init', function() {
    AcfFieldsRegister::register();
});

// Добавление страницы в админку
add_action('admin_menu', function() {
    AdminPage::register();
});
