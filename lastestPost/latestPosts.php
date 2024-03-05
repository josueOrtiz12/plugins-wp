<?php

/**
 * Plugin Name: Latest Posts from Category 
 * Plugin URI: 
 * Description: Tool to show the latest posts from Category 
 * Version: 1.0.5
 * Author: Josue Ortiz
 * Author URI: 
 * License: 
 */
require_once(ABSPATH . 'wp-includes/widgets.php');


class lastPostPluginWidget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'last-post-plugin-widget',
            'Latest Posts from Category',
            array('description' => 'Tool to show the latest posts.')
        );
    }


    public function form($instance)
    {

        $defaults = array(
            'title' => 'Recent post',
            'selected_category' => 'All Categories',
            'posts_per_page' => $instance['posts_per_page'],
        );

        $instance = wp_parse_args($instance, $defaults);
        $title = $instance['title'];
        $categoryActive = $instance['selected_category'];

        if (isset($categoryActive) && !empty($categoryActive)) {
            $categoryName = get_cat_name($categoryActive);
        }
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>">Número de entradas a mostrar:</label>
            <input type="number" class="widefat" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($instance['posts_per_page']); ?>">
        </p>

        <?php
        $categories = get_categories();
        // Add category selection dropdown
        ?>
        <label for="<?php echo $this->get_field_id('posts_per_page'); ?>">Filter by Category:</label>
<?php
        echo '<p>';
        echo '<select name="' . $this->get_field_name('selected_category') . '" id="' . $this->get_field_id('selected_category') . '">';
        echo '<option value="">' . $categoryName . '</option>';
        echo '<option value="0">All categories</option>';


        foreach ($categories as $category) {
            $selected = $category->term_id === $instance['selected_category'] ? ' selected="selected"' : '';
            echo '<option value="' . $category->term_id . '"' . $selected . '>' . $category->name . '</option>';
        }

        echo '</select>';
        echo '</p>';
    }
    public function widget($args, $instance)
    {
        // var_dump($args);
        echo $args['before_widget'] . $args['before_title'] . $instance['title'] . $args['after_title'];

        $default_args = array(
            'post_type' => 'post',
            'posts_per_page' => $instance['posts_per_page'],
        );

        if (!empty($instance['selected_category']) && $instance['selected_category'] != '0') {
            $default_args['cat'] = $instance['selected_category'];
        }

        $args = wp_parse_args($default_args);


        $the_query = new WP_Query($args);
        // var_dump($the_query);
        // The Loop
        if ($the_query->have_posts()) {
            echo '<ul>';
            while ($the_query->have_posts()) {
                $the_query->the_post();
                echo '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No entries found.</p>';
        }

        echo $args['after_widget'];
    }
}

function register_last_post_plugin_widget()
{
    register_widget('LastPostPluginWidget');
}

add_action('widgets_init', 'register_last_post_plugin_widget');
