<?php

/**
 * Description of Welcome
 *
 * @author phamt
 */
class Welcome extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->driver('session');
	}

	public function index()
	{
		if (!$this->session->userdata('ci_user'))
		{
			$this->render('login');
		}
		else
		{
			$this->render('dashboard');
		}
	}

}
