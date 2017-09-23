<?php

namespace iCarWPSEO\Models;

class Page extends \iCarWPSEO\Models\Post
{
    public function __construct(array $array = [])
	{
		foreach ($array as $property => $value) {
			if (property_exists($this, $property)) $this->$property = $value;
		}
	}

    public function getMainCategoryName()
    {
        return null;
    }

    public function getAuthorName()
    {
        return null;
    }

    public function seoTitle()
    {
        if ($this->seo_title) return "{$this->seo_title} - {$this->app_name}";
        return "{$this->title} - {$this->app_name}";
    }

    public function seoDescription()
    {
        $description = null;
        if ($this->seo_description) {
            $description .= "{$this->seo_description} - ";
        } else if ($this->getExcerpt()) {
            $description .= "{$this->getExcerpt()} ";
        } else {
            $description .= get_bloginfo('description') . ' ';
        }
        if ($this->seoTitle()) $description .= "{$this->seoTitle()}";
        return $description;
    }
}