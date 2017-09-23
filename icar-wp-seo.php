<?php

/**
 * @link              https://www.icarasia.com
 * @since             0.1.0
 * @package           iCar_WP_SEO
 *
 * @wordpress-plugin
 * Plugin Name:       iCar WordPress SEO
 * Plugin URI:        https://www.icarasia.com/
 * Description:       SEO plugin for iCar Asia WordPress sites
 * Version:           0.1.0
 * Author:            Syaiful Shah Zinan <syaiful.shah@icarasia.com>
 * Author URI:        https://www.icarasia.com/
 * Text Domain:       icar-wp-seo
 */

(defined('WPINC') && defined('ABSPATH')) or die();

// register classes
require __DIR__ . '/app/controllers/SEO.php';
require __DIR__ . '/app/controllers/Admin.php';
require __DIR__ . '/app/controllers/Taxonomy.php';
require __DIR__ . '/app/controllers/Tag.php';
require __DIR__ . '/app/controllers/CustomMetabox.php';
require __DIR__ . '/app/controllers/CustomMetaboxQuickEdit.php';

$admin = new iCarWPSEO\Controllers\Admin();
$admin->run();

$taxonomy_category_admin = new iCarWPSEO\Controllers\Taxonomy();
$taxonomy_category_admin->run();

$taxonomy_tag_admin = new iCarWPSEO\Controllers\Tag();
$taxonomy_tag_admin->run();

$seo = new iCarWPSEO\Controllers\SEO(new iCarWPSEO\Models\Admin());
$seo->run();

new iCarWPSEO\Controllers\CustomMetabox();
new iCarWPSEO\Controllers\CustomMetaboxQuickEdit();