<div class="wrap">
    <h1>iCar WordPress SEO</h1>
    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php settings_fields('icar-wp-seo-settings-group'); ?>
        <?php do_settings_sections('icar-wp-seo-settings-group'); ?>

        <h2>Application related settings</h2>
        <table class="form-table">
            <?= $this->item_html('Application Name', 'app_name') ?>
            <?= $this->item_html('Language', 'language') ?>
            <?//= $this->item_html('Application Logo (Static Url)', 'app_logo_static_url', 'If you have an exact url to use for <strong>Application Logo</strong>, plugin will use this instead of the attached image.') ?>
            <?= $this->item_media_html('Application Logo', 'app_logo') ?>
        </table>

        <h2>Main SEO settings</h2>
        <table class="form-table">
            <?= $this->item_select_html('Cache Status', 'seo_cache_status', [
                0 => 'OFF',
                1 => 'ON'
            ], 'JSON-LD microdata may decrease page performance a bit. Especially with `ItemList` enabled. So we\'ll keep cache an <strong>hour</strong>') ?>
            <?= $this->item_html('Cache Duration', 'seo_cache_duration', ($this->model->getCacheDuration() ? "{$this->model->getCacheDurationText()}.<br />" : '') . 'Default cache duration is <strong>3600 seconds (1 hour)</strong>') ?>
            <tr>
                <th valign="top">Delete Cache</th>
                <td><p><a href="<?= admin_url($this->get_admin_url() . "&delete_all_cache=true") ?>" class="button button-primary">Delete All Cache</a></p></td>
            </tr>
            <?= $this->item_tags_html('Exclude Keywords', 'seo_keyword_exclude') ?>
        </table>

        <h2>Social network settings</h2>
        <table class="form-table">
            <?= $this->item_html('Facebook App ID', 'social_facebook_app_id') ?>
            <?= $this->item_html('Facebook Pages ID', 'social_facebook_pages_id') ?>
            <?= $this->item_html('Facebook', 'social_facebook', 'Provice full Facebook url, starts with https//') ?>
            <?= $this->item_html('Twitter', 'social_twitter', 'Provide Twitter username, starts with @') ?>
        </table>

        <h2>Home page settings</h2>
        <table class="form-table">
            <?= $this->item_html('Home Title', 'seo_home_title') ?>
            <?= $this->item_textarea_html('Home Description', 'seo_home_description') ?>
            <?= $this->item_select_multi_html(
                'Home Categories',
                'seo_home_categories',
                $this->get_categories(),
                '
                    Main categories that showing or featured on home page.<br />
                    Keywords is dynamically generated from latest posts that showing on these categories.
                '
            ) ?>
            <?= $this->item_select_html('Home ItemList (Microdata)', 'seo_home_itemlist_status', [
                0 => 'OFF',
                1 => 'ON'
            ], 'Enabling this may decrease home page performance a bit. But this will super benefit home page SEO') ?>
        </table>

        <h2>Category default settings</h2>
        <table class="form-table">
            <?= $this->item_textarea_html(
                'Category Description',
                'category_seo_description',
                '
                    By default, empty description will use the first two tags from latest posts on category page.<br />
                    Instead, if you fill in this description, this value be use as description if category description is empty.
                '
            ) ?>
            <?= $this->item_select_html('Category ItemList (Microdata)', 'category_itemlist_status', [
                0 => 'OFF',
                1 => 'ON'
            ], 'Enabling this may decrease category page performance a bit. But this will super benefit category page SEO') ?>
        </table>

        <?php submit_button(); ?>

    </form>
</div>