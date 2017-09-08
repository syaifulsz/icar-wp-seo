<?php

namespace iCarWPSEO\Controllers;

require __DIR__ . '/../microdatas/Post.php';
require __DIR__ . '/../microdatas/Category.php';

require __DIR__ . '/../models/Post.php';
require __DIR__ . '/../models/Category.php';

require __DIR__ . '/../models/Meta.php';
// require __DIR__ . '/../models/Admin.php';

class SEO
{
    public $admin;
    public function __construct(\iCarWPSEO\Models\Admin $admin)
    {
        $this->admin = $admin;
    }

    public function seo_single($post)
    {
        $m_post = new \iCarWPSEO\Models\Post([
            'app_name' => $this->admin->getInput('app_name'),
            'app_logo' => $this->admin->getInput('app_logo'),
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

        $m_meta = new \iCarWPSEO\Models\Meta([
            'app_name' => $this->admin->getInput('app_name'),
            'language' => $this->admin->getInput('language'),
            'title' => $m_post->seoTitle(),
            'description' => $m_post->seoDescription(),
            'url' => $m_post->getUrl(),
            'image' => $m_post->seoMainImage(),
            'keywords' => $m_post->seoKeywords(),
            'twitter_username' => $this->admin->getInput('social_twitter')
        ]);

        $m_postmicrodata = new \iCarWPSEO\Microdatas\Post([
            'app_name' => $this->admin->getInput('app_name'),
            'app_logo' => $this->admin->getAppLogo(),
            'app_url' => $this->admin->getAppUrl(),
            'app_socials' => [
                $this->admin->getTwitterUrl(),
                $this->admin->getInput('social_facebook')
            ],

            'title' => $m_post->seoTitle(),
            'body' => $m_post->getContent(),
            'description' => $m_post->seoDescription(),
            'images' => $m_post->seoImages(),
            'url' => $m_post->getUrl(),

            'date_published' => $m_post->getDate('published'),
            'date_modified' => $m_post->getDate('modified'),
            'date_created' => $m_post->getDate('created'),

            'tags' => $m_post->seoKeywords(),
            'keywords' => $m_post->seoKeywords(),

            'author_name' => $m_post->getAuthorName(),
            'author_url' => $m_post->getAuthorUrl()
        ]);

        $metas = null;
        $metas .= $m_meta->toOutput();
        $metas .= $m_postmicrodata->toOutput();
        echo $metas;
    }

    public function seo_category_itemlist($posts) {
        $microdata_posts = [];
        $post_key = 1;
        foreach ($posts as $post) {
             $m_post = new \iCarWPSEO\Models\Post([
                'app_name' => $this->admin->getInput('app_name'),
                'app_logo' => $this->admin->getInput('app_logo'),
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
                'app_logo' => $this->admin->getAppLogo(),
                'app_url' => $this->admin->getAppUrl(),
                'app_socials' => [
                    $this->admin->getTwitterUrl(),
                    $this->admin->getInput('social_facebook')
                ],

                'title' => $m_post->seoTitle(),
                'body' => $m_post->getContent(),
                'description' => $m_post->seoDescription(),
                'images' => $m_post->seoImages(),
                'url' => $m_post->getUrl(),

                'date_published' => $m_post->getDate('published'),
                'date_modified' => $m_post->getDate('modified'),
                'date_created' => $m_post->getDate('created'),

                'tags' => $m_post->seoKeywords(),
                'keywords' => $m_post->seoKeywords(),

                'author_name' => $m_post->getAuthorName(),
                'author_url' => $m_post->getAuthorUrl()
            ]);

            $microdata_posts[] = $microdata->microdata_news_article_compact($post_key);
            $post_key++;
        }

        return $microdata_posts;
    }

    public function seo_category($category, $posts)
    {
        $m_category = new \iCarWPSEO\Models\Category([
            'app_name' => $this->admin->getInput('app_name'),
            'app_logo' => $this->admin->getInput('app_logo'),
            'language' => $this->admin->getInput('language'),
            'ID' => $category->cat_ID,
            'title' => $category->name,
            'content' => $category->description,
            'type' => $category->taxonomy
        ]);

        $m_meta = new \iCarWPSEO\Models\Meta([
            'app_name' => $this->admin->getInput('app_name'),
            'language' => $this->admin->getInput('language'),
            'title' => $m_category->seoTitle(),
            'description' => $m_category->seoDescription(),
            'url' => $m_category->getUrl(),
            'image' => $m_category->seoMainImage(),
            'keywords' => $m_category->seoKeywords(),
            'twitter_username' => $this->admin->getInput('social_twitter')
        ]);

        $m_postmicrodata = new \iCarWPSEO\Microdatas\Category([
            'app_name' => $this->admin->getInput('app_name'),
            'app_logo' => $this->admin->getAppLogo(),
            'app_url' => $this->admin->getAppUrl(),
            'app_socials' => [
                $this->admin->getTwitterUrl(),
                $this->admin->getInput('social_facebook')
            ],

            'title' => $m_category->seoTitle(),
            'body' => $m_category->getContent(),
            'description' => $m_category->seoDescription(),
            'images' => $m_category->seoImages(),
            'url' => $m_category->getUrl(),

            'tags' => $m_category->seoKeywords(),
            'keywords' => $m_category->seoKeywords(),

            'posts' => $this->admin->getInput('category_itemlist_status') ? $this->seo_category_itemlist($posts) : []
        ]);

        $metas = null;
        $metas .= $m_meta->toOutput();
        $metas .= $m_postmicrodata->toOutput();
        echo $metas;
    }

    public function run()
    {
        add_filter('wp_head', function() {
            if (is_single()) {

                global $post;
                $this->seo_single($post);

            } else if (get_query_var('cat')) {

                global $posts;
                $category = get_category(get_query_var('cat'));
                $this->seo_category($category, $posts);

            }
		});
    }
}