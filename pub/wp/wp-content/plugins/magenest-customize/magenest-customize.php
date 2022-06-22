<?php

/**
 * Plugin Name: Magenest Customize
 * Description: Magenest Customize FE & BE
 * Version: 1.0.1
 * Text Domain: mgn-cus
 *
 * Copyright: (c) 2021. (info@magenest.com)
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

register_activation_hook(__FILE__, 'mgn_post_visible');

function mgn_post_visible()
{
    global $wpdb;

    $postTable = $wpdb->prefix . 'posts';

    $posts = $wpdb->get_row("SELECT * FROM {$postTable}");
    if (!$posts->visible_slider) {
        $wpdb->query("ALTER TABLE {$postTable} ADD visible_slider INT DEFAULT 0");
    }

    if (!$posts->visible_most_viewed) {
        $wpdb->query("ALTER TABLE {$postTable} ADD visible_most_viewed INT DEFAULT 0");
    }
}

class Magenest_Customize
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'post_metakey_metabox'));
        add_action('save_post', array($this, 'save_post_visible'));
    }

    /**
     * Add field to input meta description & meta keyword
     */
    function post_metakey_metabox()
    {
        add_meta_box('post-visible', 'Post Visible', array($this, 'post_visible'), 'post');
    }

    /**
     * Render keywords field on Backend
     */
    public function post_visible()
    {
        global $post;
        ?>
        <div>
            <input type="checkbox" id="visible_slider" name="visible_slider" value="1"
                <?php if (1 == $post->visible_slider) echo 'checked="checked"'; ?>
            >
            <label for="visible_slider">Visible In Slider</label>
        </div>
        <div>
            <input type="checkbox" id="visible_most_viewed" name="visible_most_viewed" value="1"
                <?php if (1 == $post->visible_most_viewed) echo 'checked="checked"'; ?>
            >
            <label for="visible_most_viewed">Visible In Slider Most Viewed</label>
        </div>
        <?php
    }

    public function save_post_visible()
    {
        global $wpdb;
        global $post;
        $postTable = $wpdb->prefix . 'posts';
        $sliderValue = $_POST["visible_slider"] ?? 0;
        $mostViewedValue = $_POST["visible_most_viewed"] ?? 0;

        $wpdb->query("UPDATE {$postTable} SET visible_slider = {$sliderValue}, visible_most_viewed = {$mostViewedValue} WHERE ID = {$post->ID};");
    }

}

new Magenest_Customize();
