<?php

namespace iCarWPSEO\Models;

class Admin
{
    public $plugin_slug = 'icarwpseo';
    protected $app_name;
    protected $app_logo;
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

    public function getAppLogo()
    {
        $id = get_option($this->input_name('app_logo'));
        if ($id) return wp_get_attachment_url($id);
        return null;
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
            if ($key == 'app_logo') $array["{$this->input_name($key)}"] = $this->getAppLogo();
        }
        return $array;
    }

    public function input_name($name)
    {
        return "{$this->plugin_slug}__{$name}";
    }
}