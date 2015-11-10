<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Stat
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _cars_manufacturer $_cm Lớp thao tác dữ liệu
 * @property _cars_model $_cm2 Lớp thao tác dữ liệu
 * @property _cars $_c Lớp thao tác dữ liệu
 * @property _groups $_g Lớp thao tác dữ liệu
 * @property _groups_users $_g2 Lớp thao tác dữ liệu
 * @property _users $_u Lớp thao tác dữ liệu
 */
class Stat extends Admin_Controller {

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
		$this->data['count_car']			 = $this->_c->count_all();
		$this->data['count_user']			 = $this->_u->count_all();
		$this->data['count_group']			 = $this->_g->count_all();
		$this->data['count_model']			 = $this->_cm2->count_all();
		$this->data['count_manufacturer']	 = $this->_cm->count_all();

		$this->render('stat/welcome');
	}

}
