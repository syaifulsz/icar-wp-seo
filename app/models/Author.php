<?php

namespace iCarWPSEO\Models;

class Author extends \iCarWPSEO\Models\Post
{
    protected $posts;

    public function __construct(array $array = [])
	{
		foreach ($array as $property => $value) {
			if (property_exists($this, $property)) $this->$property = $value;
		}

        if ($this->author_ID) {
            $this->author = get_userdata($this->author_ID);
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
        return get_author_posts_url($this->author_ID);
    }

    public function seoTitle()
    {
        return "{$this->getAuthorName()} - {$this->title} - {$this->app_name}";
    }

    public function seoDescription()
    {
        if (!$this->getExcerpt() && $this->seoKeywords()) return $this->seoKeywords();
        return $this->getExcerpt() . ($this->seoKeywords() ? " - {$this->seoKeywords()}" : '');
    }
}