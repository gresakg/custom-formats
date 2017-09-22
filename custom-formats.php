<?php

/*
Plugin Name: Custom Formats
Plugin URI: http://gresak.net
Description: Include special format dropdowns
Author: Gregor Grešak
Version: 1.0
Author URI: http://gresak.net
*/

new GG_Custom_Formats();

class GG_Custom_Formats {

	protected $recommendation_string = "See also";

	protected $container_css_class = "formated-links";

	protected $url;

	protected $title;

	public function __construct() {
        //this functions remain in place for compatibility with formatted links plugin
		add_shortcode( 'see', array($this,"recommend") );
		add_shortcode( 'ggcte', array($this,"cte_button") );
		
		add_action( 'customize_register', array($this,'customizer') );
		add_action( 'wp_head', array($this,'set_css'));
		add_action( 'admin_head', array($this,'set_css'));
        add_filter('tiny_mce_before_init',array($this,'setup_styles'));
		add_filter( 'mce_buttons', array($this, 'register_tmce_buttons') );
        add_filter('admin_init', array($this,'set_editor_styles'));
        //add_action( 'init',array($this,'register_string'));
	}

	public function register_string() {
		if(function_exists('pll_register_string')) {
        	pll_register_string('recommendation_string',"See also","custom-formats");
        }
	}
    
    /**
     * Setup styles for tinymce
     * @param  [type] $ed [description]
     * @return [type]     [description]
     */
    public function setup_styles($ed) {
        $formats = array(  
		// Each array child is a format with it's own settings
		array(  
			'title' => 'CTA',  
			'block' => 'div',  
			'classes' => 'formated-links-button cta-button',
			'wrapper' => true,
			
		),
                array(
                    'title' => 'Priporoči',
                    'block' => 'p',
                    'classes' => 'formatted-links see'
                ),
                array(
                    'title' => 'Okvir',
                    'block' => 'div',
                    'classes' => 'custom-frame'
                ),
                array (
                    'title' => 'Drobno',
                    'inline' => 'small',
                )
                
	);
            $ed['style_formats'] = json_encode($formats);
            return $ed;
        }
    
    /**
     * Deprecated
     */
    public function set_editor_styles() {
        add_editor_style("css/editor-style.css");
    }

    /**
     * Shortcode callback function for shortcode [ggcte]
     * Deprecated, remains for compatibility
     * @param  [type] $args    [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function cte_button($args,$content) {
		$data = $this->get_data($args, $content);
		return '<div class="'.$this->container_css_class.'-button" style="text-align:center;">'
		.'<a href="'.$data->url.'" class="turquoise-bg btn rounded  btn-lg" target="_blank"><b>'.$data->title.'</b></a>'
				.'</div>';
	}

	/**
	 * Shortcode callback for shortcode [see]
	 * Deprecated, remains for compatibility
	 * @param  array $args    shortcode args
	 * @param  string $content shortcode content
	 * @return string          html for shortcode
	 */
	public function recommend($args,$content="") {
		$data = $this->get_data($args, $content);
		return '<div class="'.$this->container_css_class.'"><b>'
				.get_theme_mod("recommendation_string",$this->recommendation_string)
				.':</b> <a href="'.$data->url.'">'.$data->title.'</a>'
				.'</div>';

	}

	public function register_tmce_buttons($buttons) {
            array_push( $buttons, 'styleselect' );
            return $buttons;
	}

	/**
	 * Register customization for the recommendation "See also" string
	 * @param  [type] $customize [description]
	 * @return [type]            [description]
	 */
	public function customizer($customize) {
		$customize->add_setting('recommendation_string',array("default"=>"See also"));
		$customize->add_section('inline_recommendations', array(
			"title" => "Inline Recommendations",
			"priority" => 100
			));
		$customize->add_control(
			new WP_Customize_Control(
				$customize,
				'recommendation_string',
				array(
					'label' => 'Recommendation string',
					'section' => 'inline_recommendations',
					'settings' => 'recommendation_string'
					)
				)
			);
	}

	/**
	 * Setup css for recommendation (See also)
	 */
	public function set_css() {
		echo "\n".'<style type="text/css">.formatted-links.see::before { content: "'.get_theme_mod("recommendation_string",$this->recommendation_string).': " !important;font-weight: bold;color: black;}</style>'."\n";
	}

	protected function get_data($args,$content="") {
		$data = new stdClass();
		if(isset($args['url'])) {
			$data->url = $args['url'];
		} else {
			$data->url = $content;
		}
		if(empty($content)) {
			$content = $data->url;
		}
		$data->title = $content;
		
		return $data;
	}

	protected function get_html($data) {
		return '<div class="'.$this->container_css_class.'"><b>'
				.get_theme_mod("recommendation_string",$this->recommendation_string)
				.':</b> <a href="'.$data->url.'">'.$data->title.'</a>'
				.'</div>';
	}

}