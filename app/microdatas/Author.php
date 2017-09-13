<?php

namespace iCarWPSEO\Microdatas;

class Author extends \iCarWPSEO\Microdatas\Post
{
    public $app_url;

    public function __construct(array $array = [])
	{
        if ($array) {
            foreach ($array as $property => $value) {
    			if (property_exists($this, $property)) $this->$property = $value;
    		}
        }
	}

    public function microdata_posts_as_news_articles()
    {
        $posts = [];
        if ($this->posts) {
            $posts = [
                "@context" => "http://schema.org",
                "@type" => "ItemList",
                "numberOfItems" => count($this->posts),
                "itemListElement" => $this->posts
            ];
        }
        return $posts;
    }

    public function getMicrodata()
    {
        $microdatas = [];

        $microdatas[] = $this->microdata_person($this->title, $this->url);
        $microdatas[] = $this->microdata_organization();
        $microdatas[] = $this->microdata_website();
        $microdatas[] = $this->microdata_posts_as_news_articles();

        return $microdatas;
    }
}