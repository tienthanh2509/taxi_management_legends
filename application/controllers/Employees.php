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
 * @property Ci_d13ht01_model_auth $auth Model auth
 */
class Employees extends Admin_Controller {

	public function index()
	{
		$this->render('dashboard_employees_index');
	}

	public function add()
	{
		$this->load->model('ci_d13ht01_model_employees', '_employees');

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_username', 'Tên tài khoản', 'required|min_length[6]|max_length[32]|is_unique[ci_users.username]');
		$this->form_validation->set_rules('ci_form_password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('ci_form_password_confirm', 'Nhập lại MK', 'required|matches[ci_form_password]');
		$this->form_validation->set_rules('ci_form_email', 'Email', 'is_unique[ci_users.email]');
		$this->form_validation->set_rules('ci_form_phonenumber', 'Điện thoại', 'numeric');
		$this->form_validation->set_rules('ci_form_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_lastname', 'Họ & tên đệm', '');
		$this->form_validation->set_rules('ci_form_firstname', 'Tên', 'required');
		$this->form_validation->set_rules('ci_form_group[]', 'Nhóm', 'required');
		$this->form_validation->set_rules('ci_form_birthday', 'Ngày sinh', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');

		if ($this->form_validation->run() === TRUE)
		{
			$user_data = [
				'username'	 => $this->input->post('ci_form_username'),
				'password'	 => password_hash($this->input->post('ci_form_password'), PASSWORD_DEFAULT),
				'email'		 => $this->input->post('ci_form_email'),
				'phone'		 => $this->input->post('ci_form_phonenumber'),
				'gender'	 => $this->input->post('ci_form_gender'),
				'last_name'	 => $this->input->post('ci_form_lastname'),
				'first_name' => $this->input->post('ci_form_firstname'),
				'birthday'	 => strtotime($this->input->post('ci_form_birthday')),
			];

			$status = $this->_employees->add($user_data, $this->input->post('ci_form_group'));

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm nhân viên mới';
			}
			else
			{
				$this->data['message'] = 'Đã thêm nhân viên mới thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['ci_form']				 = [];
		$this->data['ci_form']['group_list'] = $this->_employees->get_all_group();

		$this->render('dashboard_employees_add');
	}

	public function view($user_id = '')
	{
		
	}

	public function edit($user_id = '')
	{
		$this->load->model('ci_d13ht01_model_auth', 'auth');
		$this->load->model('ci_d13ht01_model_employees', '_employees');

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		if (!$user_id)
		{
			$this->form_validation->set_rules('ci_form_userid', 'Mã NV', 'required|numeric');
			if ($this->form_validation->run() === TRUE)
			{
				$user = $this->auth->get_user_by_uid($this->input->post('ci_form_userid'));

				if (empty($user))
				{
					$this->data['error_message'] = 'Không tìm thấy nhân viên nào có mã là: ' . $this->input->post('ci_form_userid');
				}
				else
				{
					$this->output->set_header('Location: ' . $this->config->site_url('employees/edit/' . $this->input->post('ci_form_userid')));
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}
		else
		{
//			$this->form_validation->set_rules('ci_form_username', 'Tên tài khoản', 'required|min_length[6]|max_length[32]]');
//			$this->form_validation->set_rules('ci_form_password', 'Mật khẩu', 'min_length[4]|max_length[32]');
//			$this->form_validation->set_rules('ci_form_password_confirm', 'Nhập lại MK', 'matches[ci_form_password]');
//			$this->form_validation->set_rules('ci_form_email', 'Email', 'is_unique[ci_users.email]');
			$this->form_validation->set_rules('ci_form_phonenumber', 'Điện thoại', 'numeric');
			$this->form_validation->set_rules('ci_form_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
			$this->form_validation->set_rules('ci_form_lastname', 'Họ & tên đệm', '');
			$this->form_validation->set_rules('ci_form_firstname', 'Tên', 'required');
			$this->form_validation->set_rules('ci_form_group[]', 'Nhóm', 'required');
			$this->form_validation->set_rules('ci_form_birthday', 'Ngày sinh', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');

			$this->data['ci_form']['group_list'] = $this->_employees->get_all_group();
			$this->data['ci_form_user']			 = $this->auth->get_user_by_uid($user_id);

			if ($this->form_validation->run() === TRUE)
			{
				$user_data = [
					'username'	 => $this->input->post('ci_form_username'),
					'email'		 => $this->input->post('ci_form_email'),
					'phone'		 => $this->input->post('ci_form_phonenumber'),
					'gender'	 => $this->input->post('ci_form_gender'),
					'last_name'	 => $this->input->post('ci_form_lastname'),
					'first_name' => $this->input->post('ci_form_firstname'),
					'birthday'	 => strtotime($this->input->post('ci_form_birthday')),
				];

				if($this->input->post('ci_form_password')) {
					$user_data['password'] = password_hash($this->input->post('ci_form_password'), PASSWORD_DEFAULT);
				}

				$status = $this->_employees->update($user_id, $user_data, $this->input->post('ci_form_group'));

				if ($status === 0)
				{
					$this->data['error_message'] = 'Không thể cập nhật thông tin nhân viên';
				}
				else
				{
					$this->data['message'] = 'Đã cập nhật thông tin nhân viên thành công';
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}

			$ci_form_user_groups = !empty($this->input->post('ci_form_group')) ? $this->input->post('ci_form_group') : $this->auth->get_role_by_uid($user_id);

			$this->data['ci_form_user_groups'] = [];

			foreach ($this->data['ci_form']['group_list'] as $key => $group1)
			{
				$this->data['ci_form_user_groups'][$key]			 = $group1;
				$this->data['ci_form_user_groups'][$key]['grant']	 = in_array($group1['gid'], $ci_form_user_groups);
			}
		}

		$this->data['user_id'] = $user_id;

		$this->render('dashboard_employees_edit');
		$this->output->enable_profiler();
	}

	public function delete($user_id = '')
	{
		$this->load->model('ci_d13ht01_model_auth', 'auth');
		$this->load->model('ci_d13ht01_model_employees', '_employees');

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		if (!$user_id)
		{
			$this->form_validation->set_rules('ci_form_userid', 'Mã NV', 'required|numeric');
			if ($this->form_validation->run() === TRUE)
			{
				$user = $this->auth->get_user_by_uid($this->input->post('ci_form_userid'));

				if (empty($user))
				{
					$this->data['error_message'] = 'Không tìm thấy nhân viên nào có mã là: ' . $this->input->post('ci_form_userid');
				}
				else
				{
					$this->output->set_header('Location: ' . $this->config->site_url('employees/edit/' . $this->input->post('ci_form_userid')));
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}

		$this->data['user_id'] = $user_id;

		$this->render('dashboard_employees_delete');
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
