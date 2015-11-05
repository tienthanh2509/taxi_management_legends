<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Employees
 *
 * @author phamthanh
 * @property Ci_d13ht01_model_employees $_employees Model nhân viên
 */
class Employees extends Admin_Controller {

	public function index()
	{
		$this->render('dashboard_employees_index');
	}
	
	public function add()
	{
		$this->render('dashboard_employees_add');
	}
	
	////////////////////////////////////////////////////////////////////////////

	public function ajax_list_employees()
	{
		$page			 = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows			 = $this->input->post('rows') ? $this->input->post('rows') : 15;
		$sort			 = $this->input->post('sort') ? $this->input->post('sort') : 'uid';
		$order			 = $this->input->post('order') ? $this->input->post('order') : 'asc';
		$filter_rules	 = $this->input->post('filterRules') ? $this->input->post('filterRules') : '';

		$dt_field = [
			'uid',
			'username',
			'email',
			'hire_date',
			'first_name',
			'last_name',
			'phone',
			'birthday',
			'hire_date',
			'gender',
			'active',
			'role'
		];

		if (!is_numeric($page) || !is_numeric($rows) || $page < 1 || $rows < 1 || $rows > 50)
		{
			show_404();
		}
		elseif (!in_array($sort, $dt_field) || !in_array($order, ['asc', 'desc']))
		{
			show_404();
		}

		$filter_rules = json_decode($filter_rules, 1);

		$this->load->model('ci_d13ht01_model_employees', '_employees');
		$danh_sach_nhan_vien = $this->_employees->ajax_data_grid($rows, (($page - 1) * $rows), $sort, $order, $filter_rules);
		$this->output->set_content_type('json')->append_output(json_encode($danh_sach_nhan_vien));
	}

	public function ajax_delete_employees()
	{
		$this->load->model('ci_d13ht01_model_auth', 'auth');

		$uid = $this->input->post('uid');

		if (!$uid)
		{
			$output = [
				'status'	 => -1,
				'message'	 => 'Dữ liệu đầu vào không hợp lệ!'
			];
		}
		else
		{
			$status = $this->auth->delete_user($uid);
			if ($status == 1)
			{
				$output = [
					'status'	 => $status,
					'message'	 => 'Đã xóa tài khoản thành công!'
				];
			}
			elseif ($status == 0)
			{
				$output = [
					'status'	 => $status,
					'message'	 => 'Không thể xóa tài khoản được yêu cầu!'
				];
			}
			else
			{
				$output = [
					'status'	 => $status,
					'message'	 => 'Lỗi không xác định, mã lỗi ' . $status
				];
			}
		}

		$this->output->set_content_type('json')->set_output(json_encode($output));
	}

	public function ajax_list_group()
	{
		//$this->output->cache(60);
		$this->load->model('ci_d13ht01_model_employees', '_employees');
		$this->output->set_content_type('json')->set_output(json_encode($this->_employees->get_all_group()));
	}
}
