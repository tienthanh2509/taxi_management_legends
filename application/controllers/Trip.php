<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Trip
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class Trip extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_cars_manufacturer', '_cm');
		$this->load->model('_cars_model', '_cm2');
		$this->load->model('_cars', '_c');
		$this->load->model('_groups', '_g');
		$this->load->model('_groups_users', '_g2');
		$this->load->model('_users', '_u');
	}

	public function index()
	{
		$this->render('trip/welcome');
	}

}
