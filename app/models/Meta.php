<?php

namespace iCarWPSEO\Models;

class Meta
{
    protected $app_name;
    protected $language = 'en';
    protected $robots = 'index, follow';

    protected $title;
    protected $description;
    protected $url;
    protected $image;
    protected $keywords;

    protected $twitter_username;
    protected $twitter_card = 'summary_large_image';

    protected $og_type = 'article';

    public function __construct(array $array = [])
	{
		foreach ($array as $property => $value) {
			if (property_exists($this, $property)) $this->$property = trim($value);
		}
	}

    public function __get($property)
    {
        if (property_exists($this, $property)) return $this->$property;
    }

    public function toArray()
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    public function toOutput()
    {
        $metas = null;
        $seo = [];

        $seo['description'] = $this->description;
        $seo['keywords'] = $this->keywords;

        $seo['language'] = $this->language;
        $seo['robots'] = $this->robots;

        $seo['twitter:title'] = $this->title;
        $seo['twitter:description'] = $this->description;
        $seo['twitter:site'] = $this->twitter_username;
        $seo['twitter:creator'] = $this->twitter_username;
        $seo['twitter:card'] = $this->twitter_card;
        $seo['twitter:url'] = $this->url;

        $seo['og:title'] = $this->title;
        $seo['og:description'] = $this->description;
        $seo['og:site_name'] = $this->app_name;
        $seo['og:image'] = $this->image;
        $seo['og:locale'] = $this->language;
        $seo['og:url'] = $this->url;
        $seo['og:type'] = $this->og_type;

        $seo['application-name'] = $this->app_name;

        $metas .= "<title>{$this->title}</title>" . PHP_EOL;

        if ($seo) {
            foreach ($seo as $meta_name => $meta_content) {
                if ($meta_content) $metas .= "<meta name=\"{$meta_name}\" content=\"{$meta_content}\">" . PHP_EOL;
            }
        }

        if ($metas) return $metas;
    }
}