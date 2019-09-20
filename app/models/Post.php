<?php

namespace iCarWPSEO\Models;

require __DIR__ . '/../components/iCarHelper.php';

use \iCarWPSEO\Components\iCarHelper;

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

    protected $seo_title;
    protected $seo_description;

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
        return ucwords($this->author->display_name);
    }

    public function getMainCategoryName()
    {
        return $this->category_main->name;
    }

    public function seoTitle()
    {
        if ($this->seo_title) return "{$this->seo_title} - {$this->getMainCategoryName()} - {$this->app_name}";
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
            if (is_array($this->keyword_exclude) && !in_array(sanitize_title($tag), $this->keyword_exclude)) {
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

        foreach ($keywords as $word_key => $word) {
            if (is_array($this->keyword_exclude) && !in_array(sanitize_title($word_key), $this->keyword_exclude)) {
                unset($keywords[$word_key]);
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

    public function getExcerpt($content = null, $length = 150, $tail = '...')
    {
        if (!$content && $this->excerpt) return $this->excerpt;
        if (!$content && $this->content) $content = $this->content;

        $content = strip_shortcodes($content); 	      // removes shortcodes if any
        $content = strip_tags($content); 					  // removes html if any
        $content = str_replace('&nbsp;', '', $content);
        $content = $this->str_clean($content);

        $content_length = mb_strlen($content, 'utf8');

        return $content_length > $length ? trim(mb_substr($content, 0, -($content_length - $length), 'utf8')) . $tail : $content;
    }

    private function str_clean($str)
    {
        $str = trim($str);
        return preg_replace('!\s+!', ' ', $str);
    }

    private function get_term_seo_description($term, $term_type = 'category')
    {
        $term_name = ucwords($term->name);
        $options = get_option("icarwpseo__taxonomy_{$term_type}_field_{$term->term_id}");
        if (isset($options['seo_description'])) return [$term->slug => $options['seo_description']];
    }

    public function seoDescriptionTaxonomy()
    {
        $excerpt_array = [];

        if (!empty($this->category_main->term_id)) {
            if ($this->get_term_seo_description($this->category_main))
                $excerpt_array = array_merge($excerpt_array, $this->get_term_seo_description($this->category_main));
        }

        if ($this->tags) {
            foreach ($this->tags as $term_key => $term) {
                $term_description = $this->get_term_seo_description($term, 'tag');
                if ($term_description) {
                    $description = array_values($term_description);
                    $excerpt_array = array_merge($excerpt_array, $this->get_term_seo_description($term, 'tag'));
                }
            }
        }

        $excerpt_compiled = implode(' ', $excerpt_array);
        if ($excerpt_compiled) $excerpt_compiled = $this->getExcerpt($excerpt_compiled, 200);

        return $excerpt_compiled;
    }

    public function seoDescription()
    {
        $description = null;
        if ($this->seo_description) {
            $description .= "{$this->seo_description} - ";
        } else {
            if ($this->getExcerpt()) $description .= "{$this->getExcerpt()} ";
            if ($this->seoDescriptionTaxonomy()) $description .= "{$this->seoDescriptionTaxonomy()} ";
            if ($this->seoKeywords()) $description .= "{$this->seoKeywords()} - ";
        }
        if ($this->seoTitle()) $description .= "{$this->seoTitle()}";
        return $description;
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
            foreach ($medias as $url) {
                if ($url) {

                    $media_url = $url;
                    $media_width = 300;
                    $media_height = 200;

                    // @TODO Temporarily disable for now, making too much query with site with many images
                    // $media_id = iCarHelper::get_attachment_id_by_url($url);
                    $media_id = false;

                    if ($media_id) {
                        $media = wp_get_attachment_metadata($media_id);
                        $media_url = wp_get_attachment_url($media_id);
                        if (!empty($media['width']) && !empty($media['height'])) {
                            $media_width = $media['width'];
                            $media_width = $media['width'];
                        }
                    }

                    $images[] = [
                        'url' => $media_url,
                        'width' => $media_width,
                        'height' => $media_height
                    ];
                }
            }
        }

        if (!$images && $this->getAppLogo(true)) {

            $parse = $this->getAppLogo(true);
            if (isset($parse['width']) && isset($parse['height'])) {
                $images[] = [
                    'url' => $parse['url'],
                    'width' => $parse['width'],
                    'height' => $parse['height']
                ];
            }
        }

        return $images;
    }

    public function getAppLogo($array = true)
    {
        if (is_array($this->app_logo) && !empty($this->app_logo['url'])) {
            if ($array) return $this->app_logo;
            return $this->app_logo['url'];
        }
        return null;
    }

    public function seoMainImage($returnArray = false)
    {
        $mainImageID = get_post_thumbnail_id($this->ID);
        $mainImageUrl = null;
        $attachment = [];

        // fallback for `thesis_post_image` the `thesis` WordPress theme
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