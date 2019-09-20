<?php

namespace iCarWPSEO\Controllers;

class CustomMetabox
{

    /**
     * Constructor.
     */
    public function __construct() {

        if (is_admin()) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
    }

    /**
     * Meta box initialization.
     */
    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
    }

    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
            'icarwpseo-seo-metabox',
            __('SEO'),
            array ($this, 'render_metabox'),
            ['post', 'page'],
            'advanced',
            'default'
        );
    }

    /**
     * Renders the meta box.
     */
    public function render_metabox($post) {

        // Add nonce for security and authentication.
        wp_nonce_field('icarwpseo_nonce_edit', 'icarwpseo_metabox_editor_nonce');

        $value = get_post_meta($post->ID, '_icarwpseo_seo_title', true);
        echo '<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="icarwpseo_seo_title">Title</label></p>';
        echo '<input type="text" id="icarwpseo_seo_title" name="icarwpseo_seo_title" class="js-icarwpseo_seo_title" value="'.esc_attr($value).'" style="width: 100%;" />';
        echo '<p>SEO title need to be around 50-60 characters. <span class="js-icarwpseo_seo_title--count"></span></p>';

        $value = get_post_meta($post->ID, '_icarwpseo_seo_description', true);
        echo '<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="icarwpseo_seo_description">Description</label></p>';
        echo '<textarea id="icarwpseo_seo_description" name="icarwpseo_seo_description" class="js-icarwpseo_seo_description" style="width: 100%;" />'.esc_attr($value).'</textarea>';
        echo '<p>SEO title need to be around 160 characters. <span class="js-icarwpseo_seo_description--count"></span></p>';

        add_action('admin_footer', function() {
            echo "
            <script>
            jQuery(document).ready(function($) {

                var \$title = jQuery('.js-icarwpseo_seo_title');
                var \$description = jQuery('.js-icarwpseo_seo_description');

                var \$titleCount = jQuery('.js-icarwpseo_seo_title--count');
                var \$descriptionCount = jQuery('.js-icarwpseo_seo_description--count');

                \$title.on('keyup', function() {
                    var \$this = jQuery(this);
                    var value = \$this.val();

                    if (!value) {
                        \$titleCount.html('');
                    } else {
                        \$titleCount.html('You\'ve inserterd <strong>' + value.length + '</strong> characters.');
                    }
                });

                \$description.on('keyup', function() {
                    var \$this = jQuery(this);
                    var value = \$this.val();

                    if (!value) {
                        \$descriptionCount.html('');
                    } else {
                        \$descriptionCount.html('You\'ve inserterd <strong>' + value.length + '</strong> characters.');
                    }
                });

            });
            </script>";
        });
    }

    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {

        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['icarwpseo_metabox_editor_nonce'] ) ? $_POST['icarwpseo_metabox_editor_nonce'] : '';
        $nonce_action = 'icarwpseo_nonce_edit';

        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }

        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Sanitize the user input.
        $title = sanitize_text_field($_POST['icarwpseo_seo_title']);
        $description = sanitize_text_field($_POST['icarwpseo_seo_description']);

        // Update the meta field.
        update_post_meta( $post_id, '_icarwpseo_seo_title', $title );
        update_post_meta( $post_id, '_icarwpseo_seo_description', $description );
    }
}