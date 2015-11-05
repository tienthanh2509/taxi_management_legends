<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Phạm Tiến Thành
 * https://github.com/tienthanh2509
 */

/**
 * Twig php template engine for CodeIgniter
 *
 * @author Pham Tien Thanh
 * @copyright (c) 2015, Pham Thanh
 * @license    MIT License
 */
class Twig {

	// Cấu hình Twig
	private $config;
	// Twig Object
	private $twig;
	private $loader;
	// Các hàm hỗ trợ của CodeIgniter
	private $functions_assistant = [
		'base_url', 'site_url', 'current_url', 'uri_string'
	];
	// Các hàm không cần html escape
	private $functions_safe		 = [
		'form_open', 'form_open_multipart',
		'form_hidden', 'form_input', 'form_password', 'form_upload',
		'form_textarea', 'form_multiselect', 'form_dropdown', 'form_checkbox',
		'form_radio', 'form_submit', 'form_reset', 'form_button',
		'form_label', 'form_prep',
		'form_fieldset', 'form_fieldset_close',
		'form_close', 'form_error',
		'set_value', 'set_select', 'set_checkbox', 'set_radio',
		'lang', 'sprintf'
	];

	public function __construct($config = [])
	{
		$this->config	 = [
			'paths'	 => VIEWPATH,
			'debug'	 => (ENVIRONMENT === 'development' ? FALSE : TRUE),
			'cache'	 => (ENVIRONMENT === 'development' ? FALSE : (APPPATH . 'cache')),
		];
		$this->config	 = array_merge($this->config, $config);

		$CI = & get_instance();
		$CI->load->helper(['url', 'form', 'language']);

		Twig_Autoloader::register();
		$this->init();
	}

	protected function init()
	{
		$this->loader	 = new Twig_Loader_Filesystem($this->config['paths']);
		$this->twig		 = new Twig_Environment($this->loader, $this->config);

		$this->addCIFunctions();
	}

	public function reset()
	{
		$this->loader	 = NULL;
		$this->twig		 = NULL;

		$this->init();
	}

	/**
	 * Renders Twig Template and Set Output
	 * 
	 * @param string $view  template filename without `.twig`
	 * @param array $params
	 */
	public function display($view, $params = [])
	{
		$CI = & get_instance();
		$CI->output->append_output($this->render($view, $params));
	}

	/**
	 * Renders Twig Template and Returns as String
	 * 
	 * @param string $view  template filename without `.html`
	 * @param array $params
	 * @return string
	 */
	public function render($view, $params = [])
	{
		$this->reset();
		$view = $view . '.html';
		return $this->twig->render($view, $params);
	}

	private function addCIFunctions()
	{
		// as is functions
		foreach ($this->functions_assistant as $function)
		{
			if (function_exists($function))
			{
				$this->twig->addFunction(new \Twig_SimpleFunction($function, $function));
			}
		}
		// safe functions
		foreach ($this->functions_safe as $function)
		{
			if (function_exists($function))
			{
				$this->twig->addFunction(new \Twig_SimpleFunction($function, $function, ['is_safe' => ['html']]));
			}
		}
	}

}
