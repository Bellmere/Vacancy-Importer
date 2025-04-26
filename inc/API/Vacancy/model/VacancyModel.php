<?php

namespace VacanceImporter\API\Vacancy\model;

class VacancyModel {
    private $wpdb;

    public function __construct($post_id) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->post_id = $post_id;

        // Get the terms
        $terms = wp_get_post_terms($this->post_id, 'featured');

        // Ensure terms exist and assign the name of the first term
        $this->term_name = (!empty($terms) && is_array($terms) && isset($terms[0]->name)) ? $terms[0]->name : null;
    }

    public function get_vacancies($city = '', $per_page = 10, $offset = 0) {
        $query_args = [];
        $city_filter = '';

        
    }

}
