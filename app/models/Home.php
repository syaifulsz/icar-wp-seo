<?php

namespace iCarWPSEO\Models;

class Home extends \iCarWPSEO\Models\Post
{
    protected $posts;

    public function __construct(array $array = [])
	{
		foreach ($array as $property => $value) {
			if (property_exists($this, $property)) $this->$property = $value;
		}
	}

    public function seoKeywords($returnArray = false)
    {
        $items = [];
        if ($this->posts) {
            foreach ($this->posts as $post) {
                $post_tags = wp_get_post_tags($post->ID);
                $post_categories = get_the_category($post->ID);
                foreach ($post_categories as $cat_key => $cat) {
                    if ($cat_key < 2) $items[$cat->slug] = ucwords($cat->name);
                }
                foreach ($post_tags as $tag_key => $tag) {
                    if ($tag_key < 2) $items[$tag->slug] = ucwords($tag->name);
                }
            }
        }

        return $returnArray ? $items : ($items ? implode($items, ', ') : null);
    }

    public function getUrl()
    {
        return site_url();
    }

    public function getMainCategoryName()
    {
        return $this->title;
    }

    public function seoTitle()
    {
        return $this->title;
    }

    public function seoDescription()
    {
        if (!$this->getExcerpt() && $this->seoKeywords()) return $this->seoKeywords();
        return $this->getExcerpt() . ($this->seoKeywords() ? " - {$this->seoKeywords()}" : '');
    }
}