<?php

namespace iCarWPSEO\Microdatas;

class Post
{
    protected $app_name;
    protected $app_logo;
    protected $app_description;
    protected $app_url;
    protected $app_socials;

    protected $title;
    protected $body;
    protected $description;
    protected $images;
    protected $url;

    protected $date_published;
    protected $date_modified;
    protected $date_created;

    protected $tags;
    protected $keywords;

    protected $author_name;
    protected $author_url;

    protected $breadcrumbs;

    protected $posts;

    public function __construct(array $array = [], $post = [])
	{
        if ($array) {
            foreach ($array as $property => $value) {
    			if (property_exists($this, $property)) $this->$property = $value;
    		}
        }
	}

    public function __get($property)
    {
        if (property_exists($this, $property)) return $this->$property;
    }

    public function microdata_organization()
    {
        $organization = [
            "@context" => "http://schema.org",
            "@type" => "Organization",
            "name" => $this->app_name,
            "legalName" => $this->app_name,
            "url" => $this->app_url,
            "logo" => $this->microdata_image($this->app_logo)
        ];
        return array_merge($organization, $this->microdata_social_neworks());
    }

    public function microdata_image($image = [])
    {
        if (!empty($image['url']) && !empty($image['width']) && !empty($image['height'])) return [];

        return [
            "@context" => "http://schema.org",
            "@type" => "ImageObject",
            "url" => $image['url'],
            "height" => [
                "@context" => "http://schema.org",
                "@type" => "Intangible",
                "name" => $image['width']
            ],
            "width" => [
                "@context" => "http://schema.org",
                "@type" => "Intangible",
                "name" => $image['height']
            ]
        ];
    }

    public function microdata_website()
    {
        $array = [
            "@context" => "http://schema.org",
            "@type" => "WebSite",
            "url" => $this->app_url,
            "name" => $this->app_name,
            "publisher" => $this->app_name
        ];

        if ($this->app_description) $array['description'] = $this->app_description;

        return $array;
    }

    public function microdata_news_article()
    {
        $article = [
            "@context" => "http://schema.org",
            "@type" => "NewsArticle",
            "mainEntityOfPage" => $this->url,
            "headline" => $this->title,
            "alternativeHeadline" => $this->title,
            "editor" => $this->author_name,
            "genre" => $this->tags,
            "keywords" => $this->keywords,
            "publisher" => $this->microdata_organization(),
            "url" => $this->url,
            "datePublished" => $this->date_published,
            "dateCreated" => $this->date_created,
            "dateModified" => $this->date_modified,
            "description" => $this->description,
            "articleBody" => $this->body,
            "author" => [
                "@context" => "http://schema.org",
                "@type" => "Person",
                "name" => $this->author_name,
                "url" => $this->author_url
            ]
        ];

        if (is_array($this->app_logo) && !empty($this->app_logo['url'])) {
            $article['publisher']['logo'] = [
                "@type" => "ImageObject",
                "url" => $this->app_logo['url']
            ];
        }

        return array_merge($article, $this->microdata_images());
    }

    public function microdata_news_article_compact($position = null)
    {
        $title_trimmed = null;
        $title = $this->title;
        $title_length = mb_strlen($title, 'utf8');
        $title_trimmed = $title_length > 110 ? mb_substr($title, 0, -($title_length - 110), 'utf8') : $title;

        $article = [
            "@context" => "http://schema.org",
            "@type" => "NewsArticle",
            "mainEntityOfPage" => $this->url,
            "headline" => $title_trimmed,
            "alternativeHeadline" => $title_trimmed,
            "editor" => $this->author_name,
            // "genre" => $this->tags,
            // "keywords" => $this->keywords,
            "publisher" => $this->microdata_organization(),
            "url" => $this->url,
            "datePublished" => $this->date_published,
            "dateCreated" => $this->date_created,
            "dateModified" => $this->date_modified,
            "description" => $this->description,
            // "articleBody" => $this->body,
            "author" => [
                "@context" => "http://schema.org",
                "@type" => "Person",
                "name" => $this->author_name,
                "url" => $this->author_url
            ]
        ];

        if ($position) $article['position'] = $position;

        if (is_array($this->app_logo) && !empty($this->app_logo['url'])) {
            $article['publisher']['logo'] = [
                "@type" => "ImageObject",
                "url" => $this->app_logo['url']
            ];
        }

        return array_merge($article, $this->microdata_images());
    }

    public function microdata_social_neworks()
    {
        if ($this->app_socials) return ['sameAs' => $this->app_socials];
        return [];
    }

    public function microdata_images()
    {
        $images = [];
        if ($this->images) {
            foreach ($this->images as $image) {
                $images[] = [
                    "@context" => "http://schema.org",
                    "@type" => "ImageObject",
                    "url" => $image['url'],
                    "height" => [
                        "@context" => "http://schema.org",
                        "@type" => "Intangible",
                        "name" => $image['height']
                    ],
                    "width" => [
                        "@context" => "http://schema.org",
                        "@type" => "Intangible",
                        "name" => $image['width']
                    ]
                ];
            }
            if ($images) return ['image' => $images];
        }
        return $images;
    }

    public function microdata_breadcrumbs()
    {
        $breadcrumbs = [];
        if ($this->breadcrumbs && is_array($this->breadcrumbs)) {

            $breadcrumbs = [
                "@context" => "http://schema.org",
                "@type" => "BreadcrumbList"
            ];

            $i = 1;
            foreach ($this->breadcrumbs as $breadcrumb) {
                $breadcrumbs['itemListElement'][] = [
                    "@type" => "ListItem",
                    "position" => $i,
                    "item" => [
                        "@id" => $breadcrumb['url'],
                        "name" => $breadcrumb['title']
                    ]
                ];
                $i++;
            }
        }
        if ($breadcrumbs) return $breadcrumbs;
        return [];
    }

    public function microdata_item_list($items)
    {
        return [
            "@context" => "http://schema.org",
            "@type" => "ItemList",
            "url" => $this->url,
            "numberOfItems" => count($items),
            "itemListElement" => $items
        ];
    }

    public function getMicrodata()
    {
        $microdatas = [];
        $microdatas[] = array_merge(
            $this->microdata_news_article(),
            $this->microdata_social_neworks(),
            $this->microdata_images()
        );
        $microdatas[] = $this->microdata_breadcrumbs();
        $microdatas[] = $this->microdata_website();
        $microdatas[] = $this->microdata_organization();

        return $microdatas;
    }

    public function toArray()
    {
        return $this->getMicrodata();
    }

    public function toOutput()
    {
        return "<script type=\"application/ld+json\">". json_encode($this->getMicrodata()) ."</script>";
    }
}