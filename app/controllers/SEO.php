<?php

namespace iCarWPSEO\Controllers;

require __DIR__ . '/../microdatas/Post.php';
require __DIR__ . '/../microdatas/Category.php';
require __DIR__ . '/../microdatas/Home.php';
require __DIR__ . '/../microdatas/Author.php';

require __DIR__ . '/../models/Post.php';
require __DIR__ . '/../models/Category.php';
require __DIR__ . '/../models/Home.php';
require __DIR__ . '/../models/Page.php';
require __DIR__ . '/../models/Author.php';

require __DIR__ . '/../models/Meta.php';
// require __DIR__ . '/../models/Admin.php';

class SEO
{
    public $admin;
    public function __construct(\iCarWPSEO\Models\Admin $admin)
    {
        $this->admin = $admin;
    }

    public function cache_key($key)
    {
        return "{$this->admin->plugin_slug}__seo_cache__{$key}";
    }

    public function seo_single($post)
    {
        $cache_key = $this->cache_key("post_{$post->ID}");

        if ($this->admin->getInput('seo_cache_status') && get_transient($cache_key)) {
            echo get_transient($cache_key);
        } else {
            $obj = new \iCarWPSEO\Models\Post([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'language' => $this->admin->getInput('language'),
                'keyword_exclude' => $this->admin->getKeywordExclude(),
                'ID' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'date_created' => $post->post_date,
                'date_published' => $post->post_date,
                'date_modified' => $post->post_modified,
                'author_ID' => $post->post_author,
                'type' => $post->post_type
            ]);

            $meta = new \iCarWPSEO\Models\Meta([
                'app_name' => $this->admin->getInput('app_name'),
                'language' => $this->admin->getInput('language'),
                'title' => $obj->seoTitle(),
                'description' => $obj->seoDescription(),
                'url' => $obj->getUrl(),
                'image' => $obj->seoMainImage(),
                'keywords' => $obj->seoKeywords(),
                'twitter_username' => $this->admin->getInput('social_twitter'),
                'fb_app_id' => $this->admin->getInput('social_facebook_app_id')
            ]);

            $microdata = new \iCarWPSEO\Microdatas\Post([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $obj->seoTitle(),
                'body' => $obj->getContent(),
                'description' => $obj->seoDescription(),
                'images' => $obj->seoImages(),
                'url' => $obj->getUrl(),

                'date_published' => $obj->getDate('published'),
                'date_modified' => $obj->getDate('modified'),
                'date_created' => $obj->getDate('created'),

                'tags' => $obj->seoKeywords(),
                'keywords' => $obj->seoKeywords(),

                'author_name' => $obj->getAuthorName(),
                'author_url' => $obj->getAuthorUrl()
            ]);

            $metas = null;
            $metas .= $meta->toOutput();
            $metas .= $microdata->toOutput();

            if ($this->admin->getInput('seo_cache_status')) set_transient($cache_key, $metas, $this->admin->getCacheDuration());

            echo $metas;
        }
    }

    public function seo_page($post)
    {
        $cache_key = $this->cache_key("page_{$post->ID}");

        if ($this->admin->getInput('seo_cache_status') && get_transient($cache_key)) {
            echo get_transient($cache_key);
        } else {
            $obj = new \iCarWPSEO\Models\Page([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'language' => $this->admin->getInput('language'),
                'keyword_exclude' => $this->admin->getKeywordExclude(),
                'ID' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'date_created' => $post->post_date,
                'date_published' => $post->post_date,
                'date_modified' => $post->post_modified,
                'author_ID' => $post->post_author,
                'type' => $post->post_type
            ]);

            $meta = new \iCarWPSEO\Models\Meta([
                'app_name' => $this->admin->getInput('app_name'),
                'language' => $this->admin->getInput('language'),
                'title' => $obj->seoTitle(),
                'description' => $obj->seoDescription(),
                'url' => $obj->getUrl(),
                'image' => $obj->seoMainImage(),
                'keywords' => $obj->seoKeywords(),
                'twitter_username' => $this->admin->getInput('social_twitter'),
                'fb_app_id' => $this->admin->getInput('social_facebook_app_id')
            ]);

            $microdata = new \iCarWPSEO\Microdatas\Post([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $obj->seoTitle(),
                'body' => $obj->getContent(),
                'description' => $obj->seoDescription(),
                'images' => $obj->seoImages(),
                'url' => $obj->getUrl(),

                'date_published' => $obj->getDate('published'),
                'date_modified' => $obj->getDate('modified'),
                'date_created' => $obj->getDate('created'),

                'tags' => $obj->seoKeywords(),
                'keywords' => $obj->seoKeywords(),

                'author_name' => $obj->getAuthorName(),
                'author_url' => $obj->getAuthorUrl()
            ]);

            $metas = null;
            $metas .= $meta->toOutput();
            $metas .= $microdata->toOutput();

            if ($this->admin->getInput('seo_cache_status')) set_transient($cache_key, $metas, $this->admin->getCacheDuration());

            echo $metas;
        }
    }

    public function seo_posts_itemlist($posts) {
        $microdata_posts = [];
        $post_key = 1;
        foreach ($posts as $post) {
             $obj = new \iCarWPSEO\Models\Post([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'language' => $this->admin->getInput('language'),
                'ID' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'date_created' => $post->post_date,
                'date_published' => $post->post_date,
                'date_modified' => $post->post_modified,
                'author_ID' => $post->post_author,
                'type' => $post->post_type
            ]);

            $microdata = new \iCarWPSEO\Microdatas\Post([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $obj->seoTitle(),
                'body' => $obj->getContent(),
                'description' => $obj->seoDescription(),
                'images' => $obj->seoImages(),
                'url' => $obj->getUrl(),

                'date_published' => $obj->getDate('published'),
                'date_modified' => $obj->getDate('modified'),
                'date_created' => $obj->getDate('created'),

                'tags' => $obj->seoKeywords(),
                'keywords' => $obj->seoKeywords(),

                'author_name' => $obj->getAuthorName(),
                'author_url' => $obj->getAuthorUrl()
            ]);

            $microdata_posts[] = $microdata->microdata_news_article_compact($post_key);
            $post_key++;
        }

        return $microdata_posts;
    }

    public function seo_category($category, $posts)
    {
        $cache_key = $this->cache_key("category_{$category->cat_ID}");

        if ($this->admin->getInput('seo_cache_status') && get_transient($cache_key)) {
            echo get_transient($cache_key);
        } else {
            $obj = new \iCarWPSEO\Models\Category([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'language' => $this->admin->getInput('language'),
                'ID' => $category->cat_ID,
                'title' => $category->name,
                'content' => $category->description,
                'excerpt' => $this->admin->getInput('category_seo_description'),
                'type' => $category->taxonomy,
                'posts' => $posts
            ]);

            $meta = new \iCarWPSEO\Models\Meta([
                'app_name' => $this->admin->getInput('app_name'),
                'language' => $this->admin->getInput('language'),
                'title' => $obj->seoTitle(),
                'description' => $obj->seoDescription(),
                'url' => $obj->getUrl(),
                'image' => $obj->seoMainImage(),
                'keywords' => $obj->seoKeywords(),
                'twitter_username' => $this->admin->getInput('social_twitter'),
                'twitter_card' => 'summary',
                'og_type' => 'website',
                'fb_app_id' => $this->admin->getInput('social_facebook_app_id')
            ]);

            $microdata = new \iCarWPSEO\Microdatas\Category([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $obj->seoTitle(),
                'body' => $obj->getContent(),
                'description' => $obj->seoDescription(),
                'images' => $obj->seoImages(),
                'url' => $obj->getUrl(),

                'tags' => $obj->seoKeywords(),
                'keywords' => $obj->seoKeywords(),

                'posts' => $this->admin->getInput('category_itemlist_status') ? $this->seo_posts_itemlist($posts) : []
            ]);

            $metas = null;
            $metas .= $meta->toOutput();
            $metas .= $microdata->toOutput();

            if ($this->admin->getInput('seo_cache_status')) set_transient($cache_key, $metas, $this->admin->getCacheDuration());

            echo $metas;
        }
    }

    public function seo_home()
    {
        $cache_key = $this->cache_key('home');

        if ($this->admin->getInput('seo_cache_status') && get_transient($cache_key)) {
            echo get_transient($cache_key);
        } else {
            $settings = [
                'posts_per_page' => 10
            ];

            $categories = $this->admin->getInput('seo_home_categories');
            if ($categories && is_array($categories)) $settings['cat'] = implode(',', $categories);

            $query = new \WP_Query($settings);

            $obj = new \iCarWPSEO\Models\Home([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'language' => $this->admin->getInput('language'),
                'title' => $this->admin->getInput('seo_home_title'),
                'excerpt' => $this->admin->getInput('seo_home_description'),
                'posts' => $query->posts
            ]);

            $meta = new \iCarWPSEO\Models\Meta([
                'app_name' => $this->admin->getInput('app_name'),
                'language' => $this->admin->getInput('language'),
                'title' => $obj->seoTitle(),
                'description' => $obj->seoDescription(),
                'url' => $obj->getUrl(),
                'image' => $this->admin->getAppLogo(),
                'keywords' => $obj->seoKeywords(),
                'twitter_username' => $this->admin->getInput('social_twitter'),
                'twitter_card' => 'summary',
                'og_type' => 'website',
                'fb_app_id' => $this->admin->getInput('social_facebook_app_id')
            ]);

            $microdata = new \iCarWPSEO\Microdatas\Home([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $obj->seoTitle(),
                'body' => $obj->getContent(),
                'description' => $obj->seoDescription(),
                'images' => $obj->seoImages(),
                'url' => $obj->getUrl(),

                'tags' => $obj->seoKeywords(),
                'keywords' => $obj->seoKeywords(),

                'posts' => $this->admin->getInput('seo_home_itemlist_status') ? $this->seo_posts_itemlist($obj->posts) : []
            ]);

            $metas = null;
            $metas .= $meta->toOutput();
            $metas .= $microdata->toOutput();

            if ($this->admin->getInput('seo_cache_status')) set_transient($cache_key, $metas, $this->admin->getCacheDuration());

            echo $metas;
        }
    }

    public function seo_author($author)
    {
        $cache_key = $this->cache_key('author');

        if ($this->admin->getInput('seo_cache_status') && get_transient($cache_key)) {
            echo get_transient($cache_key);
        } else {
            $settings = [
                'author' => $author,
                'posts_per_page' => 10
            ];

            $query = new \WP_Query($settings);

            $obj = new \iCarWPSEO\Models\Author([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'language' => $this->admin->getInput('language'),
                'title' => $this->admin->getInput('seo_home_title'),
                'excerpt' => $this->admin->getInput('seo_home_description'),
                'author_ID' => $author,
                'posts' => $query->posts
            ]);

            $meta = new \iCarWPSEO\Models\Meta([
                'app_name' => $this->admin->getInput('app_name'),
                'language' => $this->admin->getInput('language'),
                'title' => $obj->seoTitle(),
                'description' => $obj->seoDescription(),
                'url' => $obj->getUrl(),
                'image' => $this->admin->getAppLogo(),
                'keywords' => $obj->seoKeywords(),
                'twitter_username' => $this->admin->getInput('social_twitter'),
                'twitter_card' => 'summary',
                'og_type' => 'website',
                'fb_app_id' => $this->admin->getInput('social_facebook_app_id')
            ]);

            $microdata = new \iCarWPSEO\Microdatas\Author([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getAppLogo(true),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $obj->getAuthorName(),
                'body' => $obj->getContent(),
                'description' => $obj->seoDescription(),
                'images' => $obj->seoImages(),
                'url' => $obj->getUrl(),

                'tags' => $obj->seoKeywords(),
                'keywords' => $obj->seoKeywords(),

                'posts' => $this->admin->getInput('seo_home_itemlist_status') ? $this->seo_posts_itemlist($obj->posts) : []
            ]);

            $metas = null;
            $metas .= $meta->toOutput();
            $metas .= $microdata->toOutput();

            if ($this->admin->getInput('seo_cache_status')) set_transient($cache_key, $metas, $this->admin->getCacheDuration());

            echo $metas;
        }
    }

    public function run()
    {
        add_filter('wp_head', function() {

            if (is_front_page() || is_home()) {

                $this->seo_home();

            } else if (is_page()) {

                global $post;
                $this->seo_page($post);

            } else if (is_single()) {

                global $post;
                $this->seo_single($post);

            } else if (get_query_var('cat')) {

                global $posts;
                $category = get_category(get_query_var('cat'));
                $this->seo_category($category, $posts);

            } else if (is_author()) {

                global $author;
                global $posts;
                $this->seo_author($author, $posts);

            }
		});
    }
}