<?php

namespace iCarWPSEO\Models;

class Admin
{
    public $plugin_slug = 'icarwpseo';
    protected $app_name;
    protected $app_logo;
    protected $app_logo_static_url;

    // Image size should use 1200 x 1200 (or larger) square image
    // Facebook recommends 1200 x 630 pixels for the og:image dimensions (info),
    // which is an approximate aspect ratio of 1.91:1
    protected $app_logo_width = 1200;
    protected $app_logo_height = 630;

    protected $language;

    protected $seo_keyword_exclude;

    protected $seo_cache_status = false;
    protected $seo_cache_duration = 3600;

    // social network options
    protected $social_twitter;
    protected $social_facebook;
    protected $social_facebook_app_id;
    protected $social_facebook_pages_id;

    // default category options
    protected $seo_home_itemlist_status = false;
    protected $seo_home_title;
    protected $seo_home_description;
    protected $seo_home_keywords;
    protected $seo_home_categories;

    // default category options
    protected $category_itemlist_status = false;
    protected $category_seo_description;

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

    public function getTaxonomyInput($id, $key, $taxonomy = 'category')
    {
        $options = get_option("icarwpseo__taxonomy_{$taxonomy}_field_{$id}");
        if (!empty($options[$key])) return $options[$key];
        return null;
    }

    public function getCacheDurationText()
    {
        $duration = $this->getInput('seo_cache_duration');
        $init = $duration;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        return "You have set cache duration to<strong>" . ($hours ? " {$hours} hour" . ($hours > 1 ? 's' : '') : '') . ($minutes ? ($hours ? ',' : '') . " {$minutes} minute" . ($minutes > 1 ? 's' : '') : '') . '</strong>';
    }

    public function getCacheDuration()
    {
        if (!$this->getInput('seo_cache_duration')) return $this->seo_cache_duration;
        return $this->getInput('seo_cache_duration');
    }

    public function getAppLogo($array = false)
    {
        $image_width = get_option($this->input_name('app_logo_width')) ?: $this->app_logo_width;
        $image_height = get_option($this->input_name('app_logo_height')) ?: $this->app_logo_height;

        $url = get_option($this->input_name('app_logo_static_url'));
        if ($url) {
            if ($array) return ['url' => $url, 'width' => $image_width, 'height' => $image_height];
            return $url;
        }

        $id = get_option($this->input_name('app_logo'));
        if ($id) {

            $url = wp_get_attachment_url($id);
            $image_meta = wp_get_attachment_metadata($id);
            if ($image_meta) {
                $image_width = $image_meta['width'];
                $image_height = $image_meta['height'];
            }

            if ($array) return ['id' => $id, 'url' => $url, 'width' => $image_width, 'height' => $image_height];
            return $url;
        }

        return false;
    }

    public function getAppUrl()
    {
        return site_url();
    }

    public function getKeywordExclude()
    {
        $tags = [];
        if (empty($this->getInput('seo_keyword_exclude'))) return $tags;
        $excludes = $this->getInput('seo_keyword_exclude');
        $excludes = explode(',', $excludes);
        foreach ($excludes as $tag) {
            $tags[sanitize_title($tag)] = sanitize_title($tag);
        }
        return $tags;
    }

    public function getVars()
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($key != 'plugin_slug') $array[] = $this->input_name($key);
        }
        return $array;
    }

    public function getInput($input)
    {
        return get_option($this->input_name($input));
    }

    public function getTwitterUrl()
    {
        $social_twitter = strtolower($this->getInput('social_twitter'));
        $social_twitter = str_replace('@', '', $social_twitter);
        return "https://twitter.com/{$social_twitter}";
    }

    public function getInputs()
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($key != 'plugin_slug') $array["{$this->input_name($key)}"] = get_option($this->input_name($key));
            if ($key == 'app_logo') $array["{$this->input_name($key)}"] = $this->getAppLogo(true);
        }
        return $array;
    }

    public function input_name($name)
    {
        return "{$this->plugin_slug}__{$name}";
    }
}