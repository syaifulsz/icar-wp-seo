<?php

namespace iCarWPSEO\Controllers;

require ABSPATH . 'wp-admin/includes/screen.php';

class CustomMetaboxQuickEdit
{

    /**
     * Constructor.
     */
    public function __construct() {
        $this->init();
    }

    private function allowed_screen()
    {
        // $screen = get_current_screen();
        // $is_post = @$screen->id === 'edit-post' && @$screen->post_type === 'post' ? true : false;
        // $is_page = @$screen->id === 'edit-page' && @$screen->post_type === 'page' ? true : false;

        global $pagenow;
        $is_post = @$pagenow === 'edit.php' && !@$_GET['post_type'] ? true : false;
        $is_page = @$pagenow === 'edit.php' && @$_GET['post_type'] === 'page' ? true : false;

        if (is_admin() && ($is_post || $is_page)) return true;
        return false;
    }

    /**
     * Meta box initialization.
     */
    public function init() {

        if ($this->allowed_screen()) {
            add_action('quick_edit_custom_box', [$this, 'render_view']);
            add_action('save_post',             [$this, 'save_post'], 10, 2);
            add_action('admin_footer',          [$this, 'scripts']);
        }

        add_action('wp_ajax_icarwpseo_get_post', function() {

            if (!empty($_POST['post_id'])) {
                $post = get_post($_POST['post_id']);
                echo json_encode($post);
            }

        	wp_die();
        });

        add_action('wp_ajax_icarwpseo_get_quick_edit_fields', function() {

            if (!empty($_POST['post_id'])) {
                $post = get_post($_POST['post_id']);
                $title = get_post_meta($post->ID, '_icarwpseo_seo_title', true);
                $description = get_post_meta($post->ID, '_icarwpseo_seo_description', true);
                echo json_encode([
                    'title' => $title,
                    'description' => $description,
                ]);
            }

        	wp_die();
        });

        add_action('wp_ajax_icarwpseo_quick_edit_save', function() {

            if (!empty($_POST['post_id'])) {
                $post = get_post($_POST['post_id']);
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $description = isset($_POST['description']) ? $_POST['description'] : '';

                update_post_meta( $post->ID, '_icarwpseo_seo_title', $title );
                update_post_meta( $post->ID, '_icarwpseo_seo_description', $description );

                echo json_encode([
                    'saved' => true,
                    'title' => $title,
                    'description' => $description
                ]);
            }

        	wp_die();
        });
    }

    public function html_minify($buffer) {

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }


    public function scripts()
    {
        $script = '';

        global $pagenow;
        $is_page = @$pagenow === 'edit.php' && @$_GET['post_type'] === 'page' ? true : false;

        if ($is_page) {
            $html = $this->render_view(true);
            $html = $this->html_minify($html);
            $script .= "

            jQuery(document).ready(function() {

                jQuery(document).on('click', '.editinline', function() {

                    var self = jQuery(this);
                    var parent = self.closest('.iedit');
                    var post_id = parent.attr('id').replace('post-', '');
                    var \$quick_edit_row = jQuery('#edit-'+ post_id +'.quick-edit-row');
                    var seo_row_id = 'icarwpseo_quick_edit_'+ post_id;
                    var seo_row_html = '{$html}';
                    seo_row_html = seo_row_html.replace('id=\"\"', 'id=\"'+ seo_row_id +'\"');

                    \$quick_edit_row.append(seo_row_html);
                    jQuery('#' + seo_row_id).insertAfter(\$quick_edit_row.find('.colspanchange .inline-edit-col-right'));

                    jQuery.ajax({
                        type: 'POST',
                        url: '" . admin_url('admin-ajax.php') . "',
                        data: {
                            'post_id': post_id,
                            'action': 'icarwpseo_get_quick_edit_fields'
                        },
                        success: function (data) {
                            data = JSON.parse(data);

                            var \$title = jQuery('.js-icarwpseo_seo_title');
                            var \$description = jQuery('.js-icarwpseo_seo_description');

                            \$title.val(data.title);
                            \$description.val(data.description);
                        },
                        error: function () {
                            alert('error');
                        }
                    });

                    \$quick_edit_row.find('.inline-edit-save button.save').on('click', function() {

                        var \$title = jQuery('.js-icarwpseo_seo_title');
                        var \$description = jQuery('.js-icarwpseo_seo_description');

                        jQuery.ajax({
                            type: 'POST',
                            url: '" . admin_url('admin-ajax.php') . "',
                            data: {
                                'post_id': post_id,
                                'action': 'icarwpseo_quick_edit_save',
                                'title': \$title.val(),
                                'description': \$description.val()
                            },
                            success: function (data) {
                                // data = JSON.parse(data);
                            },
                            error: function () {
                                alert('error');
                            }
                        });
                    });
                });

            });
            ";
        } else {

            $script .= "
                jQuery(document).ready(function() {

                    jQuery(document).on('click', '.editinline', function() {

                        var self = jQuery(this);
                        var parent = self.closest('.iedit');
                        var post_id = parent.attr('id').replace('post-', '');

                        jQuery.ajax({
                            type: 'POST',
                            url: '" . admin_url('admin-ajax.php') . "',
                            data: {
                                'post_id': post_id,
                                'action': 'icarwpseo_get_quick_edit_fields'
                            },
                            success: function (data) {
                                data = JSON.parse(data);

                                var \$title = jQuery('.js-icarwpseo_seo_title');
                                var \$description = jQuery('.js-icarwpseo_seo_description');

                                \$title.val(data.title);
                                \$description.val(data.description);
                            },
                            error: function () {
                                alert('error');
                            }
                        });
                    });

                });
            ";
        }

        echo "<script>{$script}</script>";
    }

    public function save_post($post_id) {

        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['icarwpseo_quick_edit_nonce'] ) ? $_POST['icarwpseo_quick_edit_nonce'] : '';
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

    public function render_view($returnHtml = false) {

        wp_nonce_field('icarwpseo_nonce_edit', 'icarwpseo_quick_edit_nonce');

        $title = null;
        $description = null;

        $html = "
        <fieldset id=\"\" class=\"wp-clearfix inline-edit-col-left\" style=\"clear: both; float: none;\">
            <legend class=\"inline-edit-legend\">iCar WP SEO</legend>
            <div class=\"inline-edit-col\">
                <label class=\"\label\">
                    <span class=\"title\">Title</span>
                    <span class=\"input-text-wrap\"><input type=\"text\" name=\"icarwpseo_seo_title\" class=\"js-icarwpseo_seo_title\" value=\"{$title}\"></span>
                </label>
                <label class=\"\label\">
                    <span class=\"title\">Description</span>
                    <span class=\"input-text-wrap\">
                        <textarea cols=\"22\" rows=\"1\" name=\"icarwpseo_seo_description\" class=\"js-icarwpseo_seo_description\">{$description}</textarea>
                    </span>
                </label>
            </div>
        </fieldset>
        ";

        if ($returnHtml) return $html;
        echo $html;
    }
}