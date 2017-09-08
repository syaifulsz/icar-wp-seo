<?php

namespace iCarWPSEO\Models;

class Admin
{
    public $plugin_slug = 'icarwpseo';
    protected $app_name;
    protected $app_logo;
    protected $language;

    // social network options
    protected $social_twitter;
    protected $social_facebook;
    protected $social_facebook_app_id;

    // default category options
    protected $seo_title;
    protected $seo_description;
    protected $seo_keywords;

    // default category options
    protected $category_itemlist_status = false;
    protected $category_seo_title;
    protected $category_seo_description;
    protected $category_seo_keywords;

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