<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Employees
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class Employees extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_groups', '_g');
	}

}
