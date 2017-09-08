<?php

namespace iCarWPSEO\Controllers;

require __DIR__ . '/../models/Admin.php';

class Admin
{
    public $model;

    private $plugin_slug = 'icarwpseo';
    private $setting_slug = 'icar-wp-seo-settings-group';

    public function __construct()
    {
        $this->model = new \iCarWPSEO\Models\Admin();

        add_action('admin_init', function() {

            foreach ($this->model->getVars() as $var) {
                register_setting($this->setting_slug, $var);
            }
        });
    }

    public function input_name($name)
    {
        return "{$this->plugin_slug}__{$name}";
    }

    public function seo_admin()
    {
        add_action('admin_menu', function() {
        	add_menu_page('SEO Admin', 'SEO', 'manage_options', __FILE__, function() {
                $this->seo_admin_html();
            });
        });
    }

    public function get_admin_options()
    {
        return $this->model->getInputs();
    }

    private function seo_admin_html() {
        wp_enqueue_media();
    	include(__DIR__ . '/../views/admin/index.php');

        add_action('admin_footer', function() {
            $id = get_option('input__app_logo');
            echo "
                <script>
                jQuery(document).ready(function($) {

                    // Uploading files
                    var file_frame;
                    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                    var set_to_post_id = {$id}; // Set this

                    jQuery('.js-media-upload__button').on('click', function(event) {
                        event.preventDefault();

                        var input_name = $(this).attr('data-name');
                        console.log('wp', input_name);

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