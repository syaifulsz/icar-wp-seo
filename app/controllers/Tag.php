<?php

namespace iCarWPSEO\Controllers;

class Tag extends \iCarWPSEO\Controllers\Taxonomy
{
    public $taxonomy = "tag";

    public function taxonomy_custom_fields_html($term) {
        $this->add_field_textarea($term, 'seo_description', 'SEO Description for Tag', 'A short/brief explaination about this taxonomy. This value is to be use to dynamically generating description for post that is being attached with this taxonomy.');
    }

    public function run()
    {
        add_action("edited_term", function($id) {
            $this->taxonomy_custom_fields_register($id);
        }, 10, 2);
        add_action("created_term", function($id) {
            $this->taxonomy_custom_fields_register($id);
        }, 10, 2);
        add_action("edit_{$this->taxonomy}_form_fields", function($term) {
            $this->taxonomy_custom_fields_html($term);
        });
        add_action("add_{$this->taxonomy}_form_fields", function($term) {
            $this->taxonomy_custom_fields_html($term);
        });
    }
}