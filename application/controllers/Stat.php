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
 * @property _groups $_g Lớp thao tác dữ liệu
 * @property _groups_users $_g2 Lớp thao tác dữ liệu
 * @property _users $_u Lớp thao tác dữ liệu
 */
class Stat extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_groups', '_g');
		$this->load->model('_groups_users', '_g2');
		$this->load->model('_users', '_u');
	}

}
