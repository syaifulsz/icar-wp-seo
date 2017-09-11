<?php

namespace iCarWPSEO\Controllers;

class Taxonomy
{
    public $field_slug;
    public $taxonomy = "category";

    public function __construct()
    {
        $this->field_slug = "icarwpseo__taxonomy_{$this->taxonomy}_field";
    }

    private function _add_field_logics($term, $input_name)
    {
        $output = [
            'unique_name' => $input_name,
            'id' => null,
            'key' => null,
            'value' => null,
            'type' => 'add'
        ];
        $id = $term;
        if (!empty($term->term_id)) $id = $term->term_id;
        $output['key'] = "{$this->field_slug}[{$input_name}]";
        if (is_numeric($id)) {
            $options = get_option("{$this->field_slug}_{$id}");
            if (!empty($options[$input_name])) $output['value'] = $options[$input_name];
            $output['type'] = 'edit';
            $output['id'] = $id;
        }

        return $output;
    }

    public function add_field_textarea($term, $input_name, $input_title, $helper = null)
    {
        $logics = $this->_add_field_logics($term, $input_name);
        if ($logics['type'] == 'edit') {
            echo "<tr class=\"form-field form-required term-{$input_name}-wrap\">";
            echo "<th scope=\"row\"><label for=\"term-{$input_name}\">{$input_title}</label></th>";
            echo '<td>';
            echo "<textarea id=\"term-{$input_name}\" name=\"{$logics['key']}\" rows=\"5\" cols=\"50\" class=\"large-text\">{$logics['value']}</textarea>";
            if ($helper) echo "<p class=\"description\">{$helper}</p>";
            echo '</td>';
            echo '</tr>';
        } else {
            echo "<div class=\"form-field term-{$input_name}-wrap\">";
            echo "<label for=\"term-{$input_name}\">{$input_title}</label>";
            echo "<textarea name=\"{$logics['key']}\" id=\"term-{$input_name}\" rows=\"5\" cols=\"40\">{$logics['value']}</textarea>";
            if ($helper) echo "<p>{$helper}</p>";
            echo '</div>';
        }
    }

    public function add_field_text($term, $input_name, $input_title, $helper = null)
    {
        $logics = $this->_add_field_logics($term, $input_name);
        if ($logics['type'] == 'edit') {
            echo "<tr class=\"form-field form-required term-{$input_name}-wrap\">";
            echo "<th scope=\"row\"><label for=\"term-{$input_name}\">{$input_title}</label></th>";
            echo '<td>';
            echo "<input type=\"text\" id=\"term-{$input_name}\" name=\"{$logics['key']}\" size=\"40\" value=\"{$logics['value']}\" />";
            if ($helper) echo "<p class=\"description\">{$helper}</p>";
            echo '</td>';
            echo '</tr>';
        } else {
            echo "<div class=\"form-field term-{$input_name}-wrap\">";
            echo "<label for=\"term-{$input_name}\">{$input_title}</label>";
            echo "<input type=\"text\" id=\"term-{$input_name}\" name=\"{$logics['key']}\" size=\"40\" value=\"{$logics['value']}\" />";
            if ($helper) echo "<p>{$helper}</p>";
            echo '</div>';
        }
    }

    public function taxonomy_custom_fields_html($term) {
        $this->add_field_textarea($term, 'seo_description', 'SEO Description', 'A short/brief explaination about this taxonomy. This value is to be use to dynamically generating description for post that is being attached with this taxonomy.');
    }

    public function taxonomy_custom_fields_register($id) {
        if (!is_numeric($id) && isset($id->term_id)) $id = $id->term_id;
        $field_name = "{$this->field_slug}_{$id}";
        if (!empty($_POST[$this->field_slug]) && is_array($_POST[$this->field_slug])) {
            $values = $_POST[$this->field_slug];
            $current_values = get_option($field_name);
            if ($current_values) $values = array_merge($current_values, $values);
            update_option($field_name, $values);
        }
    }

    public function run()
    {
        add_action("edited_{$this->taxonomy}", function($id) {
            $this->taxonomy_custom_fields_register($id);
        }, 10, 2);
        add_action("created_{$this->taxonomy}", function($id) {
            $this->taxonomy_custom_fields_register($id);
        }, 10, 2);
        add_action("{$this->taxonomy}_edit_form_fields", function($term) {
            $this->taxonomy_custom_fields_html($term);
        });
        add_action("{$this->taxonomy}_add_form_fields", function($term) {
            $this->taxonomy_custom_fields_html($term);
        });
    }
}