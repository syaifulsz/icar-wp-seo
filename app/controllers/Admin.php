<?php

namespace iCarWPSEO\Controllers;

require __DIR__ . '/../models/Admin.php';

class Admin
{
    public $model;
    public $resources_url;
    public $wpdb;
    public $admin_page_url;

    private $plugin_slug;
    private $setting_slug = 'icar-wp-seo-settings-group';

    public function __construct()
    {
        $this->model = new \iCarWPSEO\Models\Admin();
        $this->plugin_slug = $this->model->plugin_slug;

        global $wpdb;
        $this->wpdb = $wpdb;

        add_action('admin_init', function() {
            foreach ($this->model->getVars() as $var) {
                register_setting($this->setting_slug, $var);
            }
        });

        $this->resources_url = plugins_url('icar-wp-seo/public/resources');
        $this->admin_page_url = "{$this->plugin_slug}/manage";
    }

    public function get_admin_url()
    {
        return "admin.php?page=" . urlencode($this->admin_page_url);
    }

    private function get_caches_timeout()
    {
        return $this->wpdb->get_results("SELECT * FROM wp_options WHERE option_name LIKE '%transient_timeout_icarwpseo%'");
    }

    private function get_caches()
    {
        return $this->wpdb->get_results("SELECT * FROM wp_options WHERE option_name LIKE '%transient_icarwpseo%'");
    }

    public function get_cache_keys()
    {
        return array_merge($this->get_caches(), $this->get_caches_timeout());
    }

    public function delete_all_cache()
    {
        $caches = $this->get_cache_keys();
        foreach ($caches as $cache) {
            $name = str_replace('_transient_timeout_', '', $cache->option_name);
            $name = str_replace('_transient_', '', $name);
            delete_transient($name);
        }
    }

    public function input_name($name)
    {
        return $this->model->input_name($name);
    }

    public function seo_admin()
    {
        add_action('admin_menu', function() {

            if (isset($_GET['delete_all_cache']) && $_GET['delete_all_cache'] == 'true') {
                $this->delete_all_cache();
                wp_redirect($this->get_admin_url());
                exit();
            }

            add_menu_page('SEO Admin', 'SEO', 'manage_options', $this->admin_page_url, function() {
                $this->seo_admin_html();
            });
        });
    }

    public function get_admin_options()
    {
        return $this->model->getInputs();
    }

    public function item_html($title, $input_name, $description = null)
    {
        $html  = "<tr valign=\"top\"><th scope=\"row\">{$title}</th>";
        $html .= "<td><input type=\"text\" name=\"{$this->model->input_name($input_name)}\" value=\"". $this->model->getInput($input_name) ."\" class=\"regular-text\" />";
        if ($description) {
            $html .= "<p class=\"description\">{$description}</p>";
        }
        $html .= "</td>";
        return $html;
    }

    public function item_select_html($title, $input_name, $options, $description = null, $placeholder = 'Pleas select...')
    {
        add_action('admin_footer', function() use ($input_name) {
            echo "
            <script>
            jQuery(document).ready(function($) {
                console.log('js-selectize--{$input_name}');
                $('.js-selectize--{$input_name}').selectize({
                    delimiter: ',',
                    persist: false,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                });
            });
            </script>
            ";
        });

        $values = $this->model->getInput($input_name);

        $html  = "<tr valign=\"top\"><th scope=\"row\">{$title}</th>";
        $html .= "<td style=\"padding-top: 10px;\">";

        $html .= "<select name=\"{$this->model->input_name($input_name)}\" class=\"js-selectize--{$input_name}\" style=\"width: 350px; height: 38px; visibility: hidden;\" placeholder=\"{$placeholder}\">";
        foreach ($options as $option_key => $option_value) {
            $html .= "<option value=\"{$option_key}\"  ". ($option_key == $values ? 'selected="selected"' : '') .">{$option_value}</option>";
        }
        $html .= "</select>";

        if ($description) {
            $html .= "<p class=\"description\">{$description}</p>";
        }
        $html .= "</td>";
        return $html;
    }

    public function item_tags_html($title, $input_name, $options = [], $description = null, $placeholder = 'Pleas select...')
    {
        add_action('admin_footer', function() use ($input_name) {
            echo "
            <script>
            jQuery(document).ready(function($) {
                console.log('js-selectize--{$input_name}');
                $('.js-selectize--{$input_name}').selectize({
                    delimiter: ',',
                    persist: false,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                });
            });
            </script>
            ";
        });

        $values = $this->model->getInput($input_name);

        $html  = "<tr valign=\"top\"><th scope=\"row\">{$title}</th>";
        $html .= "<td style=\"padding-top: 10px;\">";

        $html .= "<input type=\"text\" name=\"{$this->model->input_name($input_name)}\" value=\"". $this->model->getInput($input_name) ."\" class=\"regular-text  js-selectize--{$input_name}\"  style=\"width: 350px; height: 38px; visibility: hidden;\" />";

        if ($description) {
            $html .= "<p class=\"description\">{$description}</p>";
        }
        $html .= "</td>";
        return $html;
    }

    private function get_categories()
    {
        $categories = [];
        $get_categories = get_categories();
        foreach ($get_categories as $category) {
            $categories[$category->term_id] = "{$category->name} ({$category->term_id})";
        }
        return $categories;
    }

    public function item_select_multi_html($title, $input_name, $options, $description = null, $placeholder = 'Please select...')
    {
        add_action('admin_footer', function() use ($input_name) {
            echo "
            <script>
            jQuery(document).ready(function($) {
                console.log('js-selectize--{$input_name}');
                $('.js-selectize--{$input_name}').selectize({
                    delimiter: ',',
                    persist: false,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                });
            });
            </script>
            ";
        });

        $values = $this->model->getInput($input_name);

        $html  = "<tr valign=\"top\"><th scope=\"row\">{$title}</th>";
        $html .= "<td style=\"padding-top: 10px;\">";

        $html .= "<select name=\"{$this->model->input_name($input_name)}[]\" multiple class=\"js-selectize--{$input_name}\" style=\"width: 350px; height: 38px; visibility: hidden;\" placeholder=\"{$placeholder}\">";
        foreach ($options as $option_key => $option_value) {
            $html .= "<option value=\"{$option_key}\"  ". (is_array($values) && in_array($option_key, $values) ? 'selected="selected"' : '') .">{$option_value}</option>";
        }
        $html .= "</select>";

        if ($description) {
            $html .= "<p class=\"description\">{$description}</p>";
        }
        $html .= "</td>";
        return $html;
    }

    public function item_textarea_html($title, $input_name, $description = null)
    {
        $html  = "<tr valign=\"top\"><th scope=\"row\">{$title}</th>";
        $html .= "<td><textarea name=\"{$this->model->input_name($input_name)}\" class=\"regular-text\" rows=\"4\" cols=\"50\">". $this->model->getInput($input_name) ."</textarea>";
        if ($description) {
            $html .= "<p class=\"description\">{$description}</p>";
        }
        $html .= "</td>";
        return $html;
    }

    public function item_media_html($title, $input_name, $description = null)
    {
        $id = $this->model->getInput($input_name);
        return "<tr valign=\"top\">
            <th scope=\"row\">Application Logo</th>
            <td>
                <div class=\"media-upload  media-upload--{$input_name}\">
                    <img src=\"". ($id ? wp_get_attachment_url($id) : '') ."\" style=\"max-height: 100px; ". ($id ? '' : 'display: none;') ."\" class=\"media-upload__preview  media-upload__preview--{$input_name}  js-media-upload__preview--{$input_name}\">
                    <input type=\"hidden\" name=\"{$this->model->input_name($input_name)}\" class=\"media-upload__input  media-upload__input--{$input_name}  js-media-upload__input--{$input_name}\" value=\"{$id}\">
                    <button type=\"button\" name=\"button\"  data-name=\"{$input_name}\"  class=\"button  media-upload__button  js-media-upload__button  media-upload__button--{$input_name}  js-media-upload__button--{$input_name}\">". ($id ? 'Upload or Reselect Image' : 'Upload or Select Image') ."</button>
                    ". ($description ? "<p class=\"description\"><small>Minimum image size is <strong>476px</strong>.</small></p>" : '') ."
                </div>
            </td>
        </tr>";
    }

    private function wp_enqueue_selectizejs()
    {
        $v = '0.12.4';

        // selectize dependencies
        wp_register_script('sifter-js', "{$this->resources_url}/sifter/sifter.min.js", [], $v);
        wp_register_script('microplugin-js', "{$this->resources_url}/microplugin/src/microplugin.js", [], $v);

        wp_register_script('selectize-js', "{$this->resources_url}/selectize/dist/js/selectize.min.js", [], $v);
        wp_register_style('selectize-css', "{$this->resources_url}/selectize/dist/css/selectize.css", [], $v);

        wp_enqueue_script('sifter-js');
        wp_enqueue_script('microplugin-js');

        wp_enqueue_script('selectize-js');
        wp_enqueue_style('selectize-css');
    }

    private function seo_admin_html() {

        $this->wp_enqueue_selectizejs();
        wp_enqueue_media();

    	include(__DIR__ . '/../views/admin/index.php');

        // add_action('media-upload-js', function() {
        add_action('admin_footer', function() {
            $id = $this->model->getInput('app_logo');
            echo "
                <script>
                jQuery(document).ready(function($) {

                    console.log('media-upload-js');

                    // Uploading files
                    var file_frame;
                    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                    var set_to_post_id = '{$id}'; // Set this

                    jQuery('.js-media-upload__button').on('click', function(event) {
                        event.preventDefault();
                        var input_name = $(this).attr('data-name');

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            // Set the post ID to what we want
                            file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                            // Open frame
                            file_frame.open();
                            return;
                        } else {
                            // Set the wp.media post id so the uploader grabs the ID we want when initialised
                            wp.media.model.settings.post.id = set_to_post_id;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.file_frame = wp.media({
                            title: 'Select a image to upload',
                            button: {
                                text: 'Use this image',
                            },
                            multiple: false	// Set to true to allow multiple files to be selected
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                            // We set multiple to false so only get one image from the uploader
                            attachment = file_frame.state().get('selection').first().toJSON();

                            // Do something with attachment.id and/or attachment.url here
                            $('.js-media-upload__preview--' + input_name).attr('src', attachment.url).show();
                            $('.js-media-upload__input--' + input_name).val(attachment.id);

                            // Restore the main post ID
                            wp.media.model.settings.post.id = wp_media_post_id;
                        });

                        // Finally, open the modal
                        file_frame.open();
                    });

                    // Restore the main ID when the add media button is pressed
                    jQuery('a.add_media').on( 'click', function() {
                        wp.media.model.settings.post.id = wp_media_post_id;
                    });
                });
                </script>
            ";
        });
    }
}