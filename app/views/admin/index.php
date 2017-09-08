<?php

function item_html($title, $input_name, $description = null)
{
    $html  = "<tr valign=\"top\"><th scope=\"row\">{$title}</th>";
    $html .= "<td><input type=\"text\" name=\"{$input_name}\" value=\"". get_option($input_name) ."\" class=\"regular-text\" />";
    if ($description) {
        $html .= "<p class=\"description\">{$description}</p>";
    }
    $html .= "</td>";
    return $html;
}

function item_media_html($title, $input_name, $description = null)
{
    $id = get_option($input_name);
    return "<tr valign=\"top\">
        <th scope=\"row\">Application Logo</th>
        <td>
            <div class=\"media-upload  media-upload--{$input_name}\">
                <img src=\"". ($id ? wp_get_attachment_url($id) : '') ."\" style=\"max-height: 100px; ". ($id ? '' : 'display: none;') ."\" class=\"media-upload__preview  media-upload__preview--{$input_name}  js-media-upload__preview--{$input_name}\">
                <input type=\"hidden\" name=\"{$input_name}\" class=\"media-upload__input  media-upload__input--{$input_name}  js-media-upload__input--{$input_name}\" value=\"{$id}\">
                <button type=\"button\" name=\"button\"  data-name=\"{$input_name}\"  class=\"button  media-upload__button  js-media-upload__button  media-upload__button--{$input_name}  js-media-upload__button--{$input_name}\">". ($id ? 'Upload or Reselect Image' : 'Upload or Select Image') ."</button>
                ". ($description ? "<p class=\"description\"><small>Minimum image size is <strong>476px</strong>.</small></p>" : '') ."
            </div>
        </td>
    </tr>";
}

?>

<div class="wrap">

    <h1>iCar WordPress SEO</h1>
    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php settings_fields('icar-wp-seo-settings-group'); ?>
        <?php do_settings_sections('icar-wp-seo-settings-group'); ?>

        <h2>Application related settings</h2>
        <table class="form-table">
            <?= item_html('Application Name', $this->input_name('app_name')) ?>
            <?= item_html('Language', $this->input_name('language')) ?>
            <?= item_media_html('Application Logo', $this->input_name('app_logo')) ?>
        </table>

        <h2>Social network settings</h2>
        <table class="form-table">
            <?= item_html('Facebook App ID', $this->input_name('social_facebook_app_id')) ?>
            <?= item_html('Facebook', $this->input_name('social_facebook'), 'Provice full Facebook url, starts with https//') ?>
            <?= item_html('Twitter', $this->input_name('social_twitter'), 'Provide Twitter username, starts with @') ?>
        </table>

        <?php submit_button(); ?>
    </form>
</div>