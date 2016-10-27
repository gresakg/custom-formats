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

	protected $oembed;

	protected $url;

	protected $title;

	public function __construct() {
                //this functions remain in place for compatibility with formatted links plugin
		add_shortcode( 'see', array($this,"recommend") );
		add_shortcode( 'ggcte', array($this,"cte_button") );
		add_action( 'customize_register', array($this,'customizer') );
		//add_action( 'wp_head', array($this,'set_css'));
                add_filter('tiny_mce_before_init',array($this,'setup_styles'));
		add_filter( 'mce_buttons', array($this, 'register_tmce_buttons') );
                add_filter('admin_init', array($this,'set_editor_styles'));
	}
        
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
        
        public function set_editor_styles() {
            add_editor_style("css/editor-style.css");
        }

        public function cte_button($args,$content) {
		$data = $this->get_data($args, $content);
		return '<div class="'.$this->container_css_class.'-button" style="text-align:center;">'
		.'<a href="'.$data->url.'" class="turquoise-bg btn rounded  btn-lg" target="_blank"><b>'.$data->title.'</b></a>'
				.'</div>';
	}

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

	public function set_css() {
		echo "<style>"
				.".{$this->container_css_class}-button a {" 
				."background-color: #1f8dd6;"
				."border-radius: 0.5em;"
			    ."display: inline-block;"
			    ."line-height: 1em;"
			    ."margin: 0.5em;"
			    ."max-height: 4em;"
			    ."padding: 16px 13px 17px;"
			    ."text-align: center;"
			    ."text-decoration: none;"
			."} "
			."</style>";
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