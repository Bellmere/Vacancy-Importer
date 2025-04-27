<?php
/**
 * VacancyController
 *
 * Test Routes examples:
 * - GET all vacancies: /wp-json/digiway/v1/vacancies
 * - GET vacancies by city: /wp-json/digiway/v1/vacancies?city=Краков
 * - GET vacancies with salary filter: /wp-json/digiway/v1/vacancies?salary_min=4000
 * - GET vacancies ordered by salary ASC: /wp-json/digiway/v1/vacancies?order_by=salary&order=asc
 */
namespace VacanceImporter\API\Vacancy\controller;

use WP_REST_Request;
use WP_REST_Response;
use VacanceImporter\API\Vacancy\model\VacancyModel;

class VacancyController {
    private $model;

    public function __construct() {
        $this->model = new VacancyModel();
    }

    public static function register_routes() {
        $controller = new self();

        register_rest_route('digiway/v1', '/vacancies', [
            'methods' => 'GET',
            'callback' => [$controller, 'get_items'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('digiway/v1', '/vacancies', [
            'methods' => 'POST',
            'callback' => [$controller, 'add_item'],
            'permission_callback' => [$controller, 'permissions_check'],
        ]);

        register_rest_route('digiway/v1', '/vacancies/cities', [
            'methods' => 'GET',
            'callback' => [$controller, 'get_unique_cities'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_items(WP_REST_Request $request) {
        $city = sanitize_text_field($request->get_param('city'));
        $page = max(1, intval($request->get_param('page')));
        $per_page = intval($request->get_param('per_page')) ?: 10;
        $salary_min = $request->get_param('salary_min');
        $salary_max = $request->get_param('salary_max');
        $order_by = sanitize_text_field($request->get_param('order_by')) ?: 'id';
        $order = sanitize_text_field($request->get_param('order')) ?: 'DESC';

        $salary_min = is_numeric($salary_min) ? (int) $salary_min : null;
        $salary_max = is_numeric($salary_max) ? (int) $salary_max : null;

        $result = $this->model->get_vacancies($city, $per_page, $page, $salary_min, $salary_max, $order_by, $order);

        return new WP_REST_Response([
            'posts' => $result['posts'],
            'total' => $result['total'],
        ], 200);
    }

    public function add_item(WP_REST_Request $request) {
        $title = sanitize_text_field($request->get_param('title'));
        $description = sanitize_textarea_field($request->get_param('description'));
        $city = sanitize_text_field($request->get_param('city'));
        $salary = sanitize_text_field($request->get_param('salary'));
        $type_of_employment = sanitize_text_field($request->get_param('type_of_employment'));

        if (empty($title)) {
            return new WP_REST_Response(['message' => 'Title is required'], 400);
        }

        $post_id = $this->model->add_or_update_vacancy($title, $description, $city, $salary, $type_of_employment);

        if (is_wp_error($post_id)) {
            return new WP_REST_Response(['message' => 'Error creating/updating vacancy'], 500);
        }

        return new WP_REST_Response([
            'message' => 'Vacancy created or updated successfully',
            'id' => $post_id,
        ], 201);
    }

    public function get_unique_cities() {
        $cities = $this->model->get_unique_cities();

        if (!is_array($cities)) {
            return new WP_REST_Response([], 200);
        }

        $cities = array_filter($cities, function($city) {
            return !empty($city);
        });

        $cities = array_unique($cities);

        sort($cities);

        return new WP_REST_Response(array_values($cities), 200);
    }



    public function permissions_check(WP_REST_Request $request) {
        $valid_nonce = wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest');
        $user = wp_get_current_user();
        $is_admin = in_array('administrator', (array) $user->roles, true);

        if (!$valid_nonce) {
            return new \WP_Error('invalid_nonce', 'Invalid security token.', ['status' => 403]);
        }

        if (!$is_admin) {
            return new \WP_Error('forbidden', 'Only administrators can perform this action.', ['status' => 403]);
        }

        return true;
    }
}
