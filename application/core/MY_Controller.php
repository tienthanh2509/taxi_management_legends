<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * core/MY_Controller.php
 *
 * Default application controller
 *
 */
class CI_D13HT01 extends CI_Controller {

	public $twig; // Twig instance
	protected $data; // parameters for view components

	/**
	 * Constructor.
	 * Establish view parameters & set a couple up
	 */

	function __construct()
	{
		parent::__construct();

		$this->data = [];

		//
		$this->add_param();

		// Khởi động thư viện Twig
		$this->load->library('Twig');
		$this->twig = new Twig();

		$this->add_security_header();
	}

	protected function add_param()
	{
		$this->data['head'] = [
			'title'	 => 'D13HT01 - Hệ Thống Thông Tin',
			'meta'	 => [
				'description'	 => 'Website lớp D13HT01 ngành hệ thống thông tin.',
				'keywords'		 => 'd13ht01, httt, tdmu, cntt',
				'author'		 => 'Tiến Thành',
				'developer'		 => 'Phạm Tiến Thành',
				'robots'		 => 'all',
			]
		];

		$this->data['error_message'] = '';
		$this->data['CI'] = &get_instance();
	}

	/**
	 * Thêm một số HTTP Header để tăng cường bảo mật
	 */
	protected function add_security_header()
	{
		// Prevent some security threats, per Kevin
		// Turn on IE8-IE9 XSS prevention tools
		$this->output->set_header('X-XSS-Protection: 1; mode=block');
		// Don't allow any pages to be framed - Defends against CSRF
		$this->output->set_header('X-Frame-Options: DENY');
		// prevent mime based attacks
		$this->output->set_header('X-Content-Type-Options: nosniff');
	}

	/**
	 * Xử lý giao diện
	 * 
	 * @param string $t Tên file template
	 */
	function render($t)
	{
		$this->output->append_output($this->twig->render($t, $this->data));
	}

}

/**
 * @property Ion_auth $ion_auth
 * @property Ion_auth_model $ion_auth_model
 */
class Admin_Controller extends CI_D13HT01 {

	function __construct()
	{
		parent::__construct();

		$this->load->driver('session');

		if (!$this->session->userdata('ci_user'))
		{
			$this->render('login');
			exit($this->output->get_output());
		}
	}

}

class Public_Controller extends CI_D13HT01 {

	function __construct()
	{
		parent::__construct();
	}

}
