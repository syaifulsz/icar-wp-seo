<?php

namespace iCarWPSEO\Models;

class Category extends \iCarWPSEO\Models\Post
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
        $tags = [];
        if ($this->posts) {
            foreach ($this->posts as $post) {
                $post_tags = wp_get_post_tags($post->ID);
                foreach ($post_tags as $tag_key => $tag) {
                    if ($tag_key < 2) $tags[$tag->slug] = ucwords($tag->name);
                }
            }
        }

        return $returnArray ? $tags : ($tags ? implode($tags, ', ') : null);
    }

    public function getUrl()
    {
        return get_category_link($this->ID);
    }

    public function getMainCategoryName()
    {
        return $this->title;
    }

    public function seoTitle()
    {
        return "{$this->title} - {$this->app_name}";
    }

    public function seoDescription()
    {
        if (!$this->getExcerpt() && $this->seoKeywords()) return $this->seoKeywords();
        return $this->getExcerpt() . ($this->seoKeywords() ? " - {$this->seoKeywords()}" : '');
    }
}