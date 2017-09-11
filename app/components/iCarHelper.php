<?php

namespace iCarWPSEO\Components;

class iCarHelper
{

    /**
     * get_attachment_id_by_url() Get attachment ID by parsing image url
     *
     * @param  string $url
     * @return string
     */
    public static function get_attachment_id_by_url($url)
    {
        if (!$url) return null;
        global $wpdb;
        global $table_prefix;
        $parsed_url = pathinfo($url);
        if (!isset($parsed_url['basename'])) return null;
    	$results = $wpdb->get_results("SELECT * FROM {$table_prefix}posts WHERE post_type = 'attachment' AND guid LIKE '%{$parsed_url['basename']}%'", OBJECT);
        if (isset($results[0]->ID)) return $results[0]->ID;
        return null;
    }
}
