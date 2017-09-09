<?php

namespace iCarWPSEO\Models;

class Post
{
    protected $ID;
    protected $author_ID;
    protected $content;
    protected $excerpt;
    protected $title;
    protected $date_modified;
    protected $date_created;
    protected $date_published;
    protected $type;
    protected $tags;
    protected $categories;
    protected $category_main;
    protected $author;
    protected $keyword_exclude;

    protected $app_name;
    protected $app_logo;
    protected $language;

    public function __construct(array $array = [])
	{
		foreach ($array as $property => $value) {
			if (property_exists($this, $property)) $this->$property = $value;
		}

        if ($this->author_ID) {
            $this->author = get_userdata($this->author_ID);
        }

        if ($this->ID) {
            $this->categories = get_the_category($this->ID);
            if (isset($this->categories[0]->name)) $this->category_main = $this->categories[0];

            $this->tags = get_the_tags($this->ID);
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
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    public function getDate($type = 'published') {
        $type = "date_{$type}";
        return $this->{$type};
    }

    public function tagsText() {
        $tags = [];
        if ($this->tags) {
            foreach ($this->tags as $tag) {
                $tags[$tag->slug] = ucwords($tag->name);
            }
        }

        return implode(', ', $tags);
    }

    public function getAuthorName()
    {
        return $this->author->display_name;
    }

    public function getMainCategoryName()
    {
        return $this->category_main->name;
    }

    public function seoTitle()
    {
        return "{$this->title} - {$this->getMainCategoryName()} - {$this->app_name}";
    }

    public function getUrl()
    {
        return get_permalink($this->ID);
    }

    public function getAuthorUrl()
    {
        return get_author_posts_url($this->author_ID);
    }

    public function seoKeywordsTitle($returnArray = false)
    {
        $keywords = [];
        $title = $this->title;
        $tags = explode(' ', $title);
        foreach ($tags as $tag) {
            if (is_array($this->keyword_exclude) && !in_array($tag, $this->keyword_exclude)) {
                $keywords[sanitize_title($tag)] = ucfirst($tag);
            }
        }
        if ($returnArray) return $keywords;
        if ($keywords) return implode(',', $keywords);
        return null;
    }

    public function seoKeywords($returnArray = false)
    {
        $keywords = [];

        if ($this->tags) {
            foreach ($this->tags as $tag) {
                $keywords[$tag->slug] = ucwords($tag->name);
            }
        }

        if ($this->categories) {
            foreach ($this->categories as $category) {
                $keywords[$category->slug] = ucwords($category->name);
            }
        }

        $keywords = array_merge($keywords, $this->seoKeywordsTitle(true));
        $keywords[sanitize_title($this->app_name)] = $this->app_name;

        return $returnArray ? $keywords : ($keywords ? implode($keywords, ', ') : null);
    }

    public function getContent()
    {
        return apply_filters('the_content', $this->content);
    }

    public function getExcerpt($length = 150)
    {
        if ($this->excerpt) return $this->excerpt;

        $content = strip_shortcodes($this->content); 	      // removes shortcodes if any
        $content = strip_tags($content); 					  // removes html if any
        $content = str_replace('&nbsp;', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);

        $content_length = mb_strlen($content, 'utf8');

        return $content_length > $length ? mb_substr($content, 0, -($content_length - $length), 'utf8') : $content;
    }

    public function seoDescription()
    {
        return "{$this->getExcerpt()} - {$this->seoKeywords()} - {$this->seoTitle()}";
    }

    public function seoImages()
    {
        $content = apply_filters('the_content', $this->content);
        $medias = [];
        $images = [];

        $doc = new \DOMDocument;
        if ($content && $doc->loadHTML($content)) {
            $imgNodes = $doc->getElementsByTagName('img');
            if ($imgNodes) {
                foreach ($imgNodes as $imgNode) {
                    $slug = sanitize_title($imgNode->getAttribute('src'));
                    $medias[$slug] = $imgNode->getAttribute('src');
                }
            }
        }

        if ($medias) {
            foreach ($medias as $media) {
                if ($media) {
                    $parse = @getimagesize($media);
                    if (isset($parse[0]) && isset($parse[1])) {
                        $images[] = [
                            'url' => $media,
                            'width' => $parse[0],
                            'height' => $parse[1]
                        ];
                    }
                }
            }
        }

        if (!$images && $this->getAppLogo()) {
            $parse = @getimagesize($this->getAppLogo());
            if (isset($parse[0]) && isset($parse[1])) {
                $images[] = [
                    'url' => $this->getAppLogo(),
                    'width' => $parse[0],
                    'height' => $parse[1]
                ];
            }
        }

        return $images;
    }

    public function getAppLogo()
    {
        if (is_numeric($this->app_logo)) return wp_get_attachment_url($this->app_logo);
        return $this->app_logo;
    }

    public function seoMainImage($returnArray = false)
    {
        $mainImageID = get_post_thumbnail_id($this->ID);
        $mainImageUrl = null;
        $attachment = [];
        if (empty($mainImageID)) {
            $thesisPostImage = get_post_meta($this->ID, 'thesis_post_image', true);
            if (!empty($thesisPostImage)) $mainImageID = get_attachment_id_by_url($thesisPostImage);
        }
        if ($mainImageID) $attachment = wp_get_attachment_image_src($mainImageID, 'full');
        if (isset($attachment[0])) $mainImageUrl = $attachment[0];

        if (empty($mainImageUrl)) {
            $attachment = $this->seoImages();
            if (isset($attachment[0]['url'])) {
                $mainImageUrl = $attachment[0]['url'];
            } else {
                $mainImageUrl = $this->getAppLogo();
            }
        }

        if ($attachment && $returnArray) return ['ID' => $mainImageID, 'attachment' => $attachment];
        return $mainImageUrl;
    }
}