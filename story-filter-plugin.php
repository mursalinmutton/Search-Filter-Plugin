<?php
/*
Plugin Name: Story Filter Plugin
Description: Adds a filter to the story post type with multiple taxonomy options.
Version: 1.0
Author: Mursalin
*/

// Enqueue CSS and JavaScript files
function sfp_enqueue_scripts() {
    wp_enqueue_style('sfp-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('sfp-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);

    wp_localize_script('sfp-script', 'sfp_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'sfp_enqueue_scripts');

// Display the filter form using a shortcode
function sfp_display_filter_form() {
    ob_start();
    ?>
    <form id="sfp-filter-form" method="GET">
        <div class="filter-group">
            <h4>Connection to CRC</h4>
            <?php
            $crc_terms = get_terms(array('taxonomy' => 'connection_to_crc', 'hide_empty' => false));
            foreach ($crc_terms as $term) {
                echo '<label><input type="checkbox" name="connection_to_crc[]" value="' . esc_attr($term->slug) . '"> ' . esc_html($term->name) . '</label>';
            }
            ?>
        </div>

        <div class="filter-group">
            <h4>Stage & Type</h4>
            <?php
            $stage_type_terms = get_terms(array('taxonomy' => 'stage_type', 'hide_empty' => false));
            foreach ($stage_type_terms as $term) {
                echo '<label><input type="checkbox" name="stage_type[]" value="' . esc_attr($term->slug) . '"> ' . esc_html($term->name) . '</label>';
            }
            ?>
        </div>

        <div class="filter-group">
            <h4>Age of Diagnosis</h4>
            <?php
            $age_terms = get_terms(array('taxonomy' => 'age_of_diagnosis', 'hide_empty' => false));
            foreach ($age_terms as $term) {
                echo '<label><input type="checkbox" name="age_of_diagnosis[]" value="' . esc_attr($term->slug) . '"> ' . esc_html($term->name) . '</label>';
            }
            ?>
        </div>

        <div class="filter-group">
            <h4>State/Province/Country</h4>
            <?php
            $location_terms = get_terms(array('taxonomy' => 'state_province_country', 'hide_empty' => false));
            foreach ($location_terms as $term) {
                echo '<label><input type="checkbox" name="state_province_country[]" value="' . esc_attr($term->slug) . '"> ' . esc_html($term->name) . '</label>';
            }
            ?>
        </div>

        <div class="filter-group">
            <h4>Ethnicity</h4>
            <?php
            $ethnicity_terms = get_terms(array('taxonomy' => 'ethnicity', 'hide_empty' => false));
            foreach ($ethnicity_terms as $term) {
                echo '<label><input type="checkbox" name="ethnicity[]" value="' . esc_attr($term->slug) . '"> ' . esc_html($term->name) . '</label>';
            }
            ?>
        </div>

        <div class="filter-group">
            <h4>Topics</h4>
            <select name="topics">
                <option value="">Select Topic</option>
                <?php
                $topics_terms = get_terms(array('taxonomy' => 'topics', 'hide_empty' => false));
                foreach ($topics_terms as $term) {
                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                }
                ?>
            </select>
        </div>

        <button type="submit">Filter</button>
    </form>
    <div id="sfp-filter-results">
        <?php
        $args = array(
            'post_type' => 'story',
            'posts_per_page' => -1, 
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                echo '<div class="story-item">';
                echo '<h3>' . get_the_title() . '</h3>';
                echo '<div>' . get_the_excerpt() . '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No stories found.</p>';
        }
        wp_reset_postdata();
        ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('story_filter', 'sfp_display_filter_form');



// Filter the story post type based on selected options
function sfp_filter_stories($query) {
    if (!is_admin() && $query->is_main_query()) {
        $tax_query = array('relation' => 'AND');

        if (!empty($_GET['connection_to_crc'])) {
            $tax_query[] = array(
                'taxonomy' => 'connection_to_crc',
                'field'    => 'slug',
                'terms'    => $_GET['connection_to_crc'],
                'operator' => 'IN',
            );
        }

        if (!empty($_GET['stage_type'])) {
            $tax_query[] = array(
                'taxonomy' => 'stage_type',
                'field'    => 'slug',
                'terms'    => $_GET['stage_type'],
                'operator' => 'IN',
            );
        }

        if (!empty($_GET['age_of_diagnosis'])) {
            $tax_query[] = array(
                'taxonomy' => 'age_of_diagnosis',
                'field'    => 'slug',
                'terms'    => $_GET['age_of_diagnosis'],
                'operator' => 'IN',
            );
        }

        if (!empty($_GET['state_province_country'])) {
            $tax_query[] = array(
                'taxonomy' => 'state_province_country',
                'field'    => 'slug',
                'terms'    => $_GET['state_province_country'],
                'operator' => 'IN',
            );
        }

        if (!empty($_GET['ethnicity'])) {
            $tax_query[] = array(
                'taxonomy' => 'ethnicity',
                'field'    => 'slug',
                'terms'    => $_GET['ethnicity'],
                'operator' => 'IN',
            );
        }

        if (!empty($_GET['topics'])) {
            $tax_query[] = array(
                'taxonomy' => 'topics',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['topics']),
            );
        }

        if (count($tax_query) > 1) {
            $query->set('tax_query', $tax_query);
        }
    }
}
add_action('pre_get_posts', 'sfp_filter_stories');



function sfp_filter_stories_ajax() {
    
    $args = array(
        'post_type' => 'story',
        'posts_per_page' => -1, 
    );

    $tax_query = array('relation' => 'AND');

    if (!empty($_GET['connection_to_crc'])) {
        $tax_query[] = array(
            'taxonomy' => 'connection_to_crc',
            'field'    => 'slug',
            'terms'    => $_GET['connection_to_crc'],
            'operator' => 'IN',
        );
    }

    if (!empty($_GET['stage_type'])) {
        $tax_query[] = array(
            'taxonomy' => 'stage_type',
            'field'    => 'slug',
            'terms'    => $_GET['stage_type'],
            'operator' => 'IN',
        );
    }

    if (!empty($_GET['age_of_diagnosis'])) {
        $tax_query[] = array(
            'taxonomy' => 'age_of_diagnosis',
            'field'    => 'slug',
            'terms'    => $_GET['age_of_diagnosis'],
            'operator' => 'IN',
        );
    }

    if (!empty($_GET['state_province_country'])) {
        $tax_query[] = array(
            'taxonomy' => 'state_province_country',
            'field'    => 'slug',
            'terms'    => $_GET['state_province_country'],
            'operator' => 'IN',
        );
    }

    if (!empty($_GET['ethnicity'])) {
        $tax_query[] = array(
            'taxonomy' => 'ethnicity',
            'field'    => 'slug',
            'terms'    => $_GET['ethnicity'],
            'operator' => 'IN',
        );
    }

    if (!empty($_GET['topics'])) {
        $tax_query[] = array(
            'taxonomy' => 'topics',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['topics']),
        );
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="story-item">';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<div>' . get_the_excerpt() . '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No stories found matching your criteria.</p>';
    }

    wp_reset_postdata();
    die(); 
}
add_action('wp_ajax_sfp_filter_stories', 'sfp_filter_stories_ajax');
add_action('wp_ajax_nopriv_sfp_filter_stories', 'sfp_filter_stories_ajax');
