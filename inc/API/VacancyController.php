<?php

namespace VacanceImporter\API;

use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

class VacancyController {
//    =====     Public GET      ======
    public static function register_routes() {
        register_rest_route('digiway/v1', '/vacancies', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_items'],
            'permission_callback' => '__return_true',
        ]);
//      =======     POST Protect    ========
        register_rest_route('digiway/v1', '/vacancies', [
            'methods' => 'POST',
            'callback' => [self::class, 'add_item'],
            'permission_callback' => [self::class, 'permissions_check'],
        ]);
    }

    /**
     * GET /vacancies
     * receive vacancies
     */
    public static function get_items(WP_REST_Request $request) {
        $city = sanitize_text_field($request->get_param('city'));
        $page = max(1, intval($request->get_param('page')));
        $per_page = intval($request->get_param('per_page')) ?: 10;
        $offset = ($page - 1) * $per_page;

        $args = [
            'post_type' => 'vacancy',
            'posts_per_page' => $per_page,
            'paged' => $page,
        ];

        if (!empty($city)) {
            $args['meta_query'] = [
                [
                    'key' => 'city',
                    'value' => $city,
                    'compare' => 'LIKE',
                ],
            ];
        }

        $query = new WP_Query($args);

        $vacancies = [];

        foreach ($query->posts as $post) {
            $vacancies[] = [
                'id' => $post->ID,
                'title' => get_the_title($post),
                'content' => apply_filters('the_content', $post->post_content),
                'city' => get_field('city', $post->ID),
                'salary' => get_field('salary', $post->ID),
                'type_of_employment' => get_field('type_of_employment', $post->ID),
            ];
        }

        return new WP_REST_Response([
            'vacancies' => $vacancies,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $page,
        ], 200);
    }

    /**
     * POST /vacancies
     * Добавить новую вакансию
     */
    public static function add_item(WP_REST_Request $request) {
        $title = sanitize_text_field($request->get_param('title'));
        $content = sanitize_textarea_field($request->get_param('description'));
        $city = sanitize_text_field($request->get_param('city'));
        $salary = sanitize_text_field($request->get_param('salary'));
        $type_of_employment = sanitize_text_field($request->get_param('type_of_employment'));

        if (empty($title)) {
            return new WP_REST_Response(['message' => 'Title is required'], 400);
        }

        $post_id = wp_insert_post([
            'post_type' => 'vacancy',
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return new WP_REST_Response(['message' => 'Error creating vacancy'], 500);
        }

        // Сохраняем ACF поля
        update_field('city', $city, $post_id);
        update_field('salary', $salary, $post_id);
        update_field('type_of_employment', $type_of_employment, $post_id);

        return new WP_REST_Response([
            'message' => 'Vacancy created successfully',
            'id' => $post_id,
        ], 201);
    }

    /**
     * Permission callback для POST запросов
     */
    public static function permissions_check($request) {
        return wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest') && current_user_can('edit_posts');
    }
}
