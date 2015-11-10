<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of User
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _groups $_g Lớp thao tác dữ liệu
 * @property _groups_users $_g2 Lớp thao tác dữ liệu
 * @property _users $_u Lớp thao tác dữ liệu
 */
class User extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_groups', '_g');
		$this->load->model('_groups_users', '_g2');
		$this->load->model('_users', '_u');
	}

	public function group()
	{
		$page = $this->uri->segment(3, 1);

		$this->data['group_list'] = $this->_g->get_all(30, ($page - 1) * 30);

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('group/index');
		$config['total_rows']	 = $this->_g->count_all();
		$config['per_page']		 = 30;
		$config['uri_segment']	 = 3;

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('user/group');
	}

	public function group_add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_group_name', 'Tên nhóm', 'required|is_unique[ci_groups.group_name]');
		$this->form_validation->set_rules('ci_form_group_description', 'Ghi chú', '');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'group_name'		 => $this->input->post('ci_form_group_name'),
				'group_description'	 => $this->input->post('ci_form_group_description'),
			];
			$status	 = $this->_g->add($data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm nhóm mới';
			}
			else
			{
				$this->data['message'] = 'Đã thêm nhóm mới thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('user/group_add');
	}

	public function group_edit($group_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_group_name', 'Tên nhóm', 'required');
		$this->form_validation->set_rules('ci_form_group_description', 'Ghi chú', '');

		$this->data['group'] = $this->_g->get_by_id($group_id);

		if (empty($this->data['group']))
		{
			$this->data['error_message'] = 'Không tìm thấy nhóm nào có mã là: ' . $group_id;
		}
		else
		{
			if ($this->form_validation->run() === TRUE)
			{
				$data	 = [
					'group_name'		 => $this->input->post('ci_form_group_name'),
					'group_description'	 => $this->input->post('ci_form_group_description'),
				];
				$status	 = $this->_g->update($group_id, $data);

				if ($status === 0)
				{
					$this->data['error_message'] = 'Không thể cập nhật thông tin nhóm';
				}
				else
				{
					$this->data['message'] = 'Đã cập nhật thông tin nhóm thành công';
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}

		$this->render('user/group_edit');
	}

	public function group_delete($group_id = '')
	{
		if (!$group_id)
		{
			show_404();
		}

		$this->data['group'] = $this->_g->get_by_id($group_id);

		if (empty($this->data['group']))
		{
			show_404();
		}

		if ($this->input->post('confirm'))
		{
			$status = $this->_g->delete($group_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa nhóm "' . $this->data['group']['group_name'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa nhóm được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('user/group_delete');
	}

	function index()
	{
		$page = $this->uri->segment(3, 1);

		$this->data['user_list'] = $this->_u->get_all(30, ($page - 1) * 30);

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('user/index');
		$config['total_rows']	 = $this->_u->count_all();
		$config['per_page']		 = 30;
		$config['uri_segment']	 = 3;

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('user/welcome');
	}

	public function user_add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_user_name', 'Tên tài khoản', 'required|min_length[6]|max_length[32]|is_unique[ci_users.user_name]');
		$this->form_validation->set_rules('ci_form_user_password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('ci_form_user_password_confirm', 'Nhập lại MK', 'required|matches[ci_form_user_password]');
		$this->form_validation->set_rules('ci_form_user_email', 'Email', 'is_unique[ci_users.user_email]');
		$this->form_validation->set_rules('ci_form_user_phone', 'Điện thoại', 'numeric');
		$this->form_validation->set_rules('ci_form_user_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_user_ln', 'Họ & tên đệm', '');
		$this->form_validation->set_rules('ci_form_user_fn', 'Tên', 'required');
		$this->form_validation->set_rules('ci_form_group[]', 'Nhóm', 'required');
		$this->form_validation->set_rules('ci_form_user_bd', 'Ngày sinh', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');

		if ($this->form_validation->run() === TRUE)
		{
			$user_data	 = [
				'user_name'		 => $this->input->post('ci_form_user_name'),
				'user_password'	 => password_hash($this->input->post('ci_form_user_password'), PASSWORD_DEFAULT),
				'user_email'	 => $this->input->post('ci_form_user_email'),
				'user_phone'	 => $this->input->post('ci_form_user_phone'),
				'user_gender'	 => $this->input->post('ci_form_user_gender'),
				'user_ln'		 => $this->input->post('ci_form_user_ln'),
				'user_fn'		 => $this->input->post('ci_form_user_fn'),
				'user_bd'		 => $this->input->post('ci_form_user_bd'),
			];
			$status		 = $this->_u->add($user_data, $this->input->post('ci_form_group'));

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm nhân viên mới thành công';
			}
			elseif ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm nhân viên mới';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không rõ';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['group_list'] = $this->_g->get_all();
		$this->render('user/user_add');
	}

	public function user_edit($user_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

//		$this->form_validation->set_rules('ci_form_user_name', 'Tên tài khoản', 'required|min_length[6]|max_length[32]|is_unique[ci_users.user_name]');
		$this->form_validation->set_rules('ci_form_user_password', 'Mật khẩu', 'min_length[4]|max_length[32]');
//		$this->form_validation->set_rules('ci_form_user_password_confirm', 'Nhập lại MK', 'matches[ci_form_user_password]');
//		$this->form_validation->set_rules('ci_form_user_email', 'Email', 'is_unique[ci_users.user_email]');
		$this->form_validation->set_rules('ci_form_user_phone', 'Điện thoại', 'numeric');
		$this->form_validation->set_rules('ci_form_user_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_user_ln', 'Họ & tên đệm', '');
		$this->form_validation->set_rules('ci_form_user_fn', 'Tên', 'required');
		$this->form_validation->set_rules('ci_form_group[]', 'Nhóm', 'required');
		$this->form_validation->set_rules('ci_form_user_bd', 'Ngày sinh', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');

		$this->data['user'] = $this->_u->get_by_id($user_id);

		if (!$this->data['user'])
			show_404();

		if ($this->form_validation->run() === TRUE)
		{
			$user_data = [
				'user_name'		 => $this->input->post('ci_form_user_name'),
				'user_email'	 => $this->input->post('ci_form_user_email'),
				'user_phone'	 => $this->input->post('ci_form_user_phone'),
				'user_gender'	 => $this->input->post('ci_form_user_gender'),
				'user_ln'		 => $this->input->post('ci_form_user_ln'),
				'user_fn'		 => $this->input->post('ci_form_user_fn'),
				'user_bd'		 => $this->input->post('ci_form_user_bd'),
			];
			if ($this->input->post('ci_form_user_password'))
			{
				$user_data['user_password'] = password_hash($this->input->post('ci_form_user_password'), PASSWORD_DEFAULT);
			}
			$status = $this->_u->update($user_id, $user_data, $this->input->post('ci_form_group'));

			if ($status === 1)
			{
				$this->data['message'] = 'Đã cập nhật thông tin nhân viên thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin nhân viên';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['group_list'] = $this->_g->get_all();

		if (empty($this->input->post('ci_form_group')))
		{
			$ci_form_user_groups				 = $this->_g2->get_by_uid($user_id);
			$this->data['ci_form_user_groups']	 = [];
			foreach ($this->data['group_list'] as $key => $group1)
			{
				$this->data['ci_form_user_groups'][$key] = $group1;

				foreach ($ci_form_user_groups as $group2)
					if ($group1['group_id'] == $group2['group_id'])
					{
						$this->data['ci_form_user_groups'][$key]['grant'] = TRUE;
					}
			}
		}
		else
		{
			$this->data['ci_form_user_groups'] = [];
			foreach ($this->data['group_list'] as $key => $group1)
			{
				$this->data['ci_form_user_groups'][$key]			 = $group1;
				$this->data['ci_form_user_groups'][$key]['grant']	 = in_array($group1['group_id'], $this->input->post('ci_form_group'));
			}
		}

		$this->render('user/user_edit');
	}

	public function user_delete($user_id = '')
	{
		$this->data['user'] = $this->_u->get_by_id($user_id);
		if (empty($this->data['user']))
		{
			$this->data['error_message'] = 'Không tìm thấy nhân viên nào có mã là: ' . $user_id;
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_u->delete($user_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa tài khoản thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa tài khoản được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}
		else
		{
			$this->data['group_list'] = $this->_g->get_all();

			if (empty($this->input->post('ci_form_group')))
			{
				$ci_form_user_groups				 = $this->_g2->get_by_uid($user_id);
				$this->data['ci_form_user_groups']	 = [];
				foreach ($this->data['group_list'] as $key => $group1)
				{
					$this->data['ci_form_user_groups'][$key] = $group1;

					foreach ($ci_form_user_groups as $group2)
						if ($group1['group_id'] == $group2['group_id'])
						{
							$this->data['ci_form_user_groups'][$key]['grant'] = TRUE;
						}
				}
			}
			else
			{
				$this->data['ci_form_user_groups'] = [];
				foreach ($this->data['group_list'] as $key => $group1)
				{
					$this->data['ci_form_user_groups'][$key]			 = $group1;
					$this->data['ci_form_user_groups'][$key]['grant']	 = in_array($group1['group_id'], $this->input->post('ci_form_group'));
				}
			}
		}

		$this->render('user/user_delete');
	}

	public function login()
	{
		$login_attempts				 = (int) $this->session->userdata('login.attempts') ? $this->session->userdata('login.attempts') : 0;
		$login_attempts_timestamp	 = (int) $this->session->userdata('login.attempts.countdown') ? $this->session->userdata('login.attempts.countdown') : 0;
		$login_attempts_countdown	 = (30 * 60) - (time() - $login_attempts_timestamp);

		if ($this->session->userdata('user_name'))
		{
			$response = [
				'status'	 => 1,
				'message'	 => 'Đăng nhập thành công!'
			];
		}
		elseif ($login_attempts > 3 && $login_attempts_countdown > 0)
		{
			$response = [
				'status'	 => -1,
				'message'	 => 'Bạn đã đăng nhập sai quá nhiều lần!',
				'timeout'	 => $login_attempts_countdown
			];
		}
		else
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<span>', '</span>');
			$this->form_validation->set_rules('username', 'Tên tài khoản', 'required|min_length[6]|max_length[32]');
			$this->form_validation->set_rules('password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');

			if ($this->form_validation->run() === TRUE)
			{
				$username	 = $this->input->post('username');
				$password	 = $this->input->post('password');
				$status		 = $this->_u->login($username, $password);

				if ($status === 0)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Sai tài khoản hoặc mật khẩu?!'
					];
					$this->session->set_userdata('login.attempts', $login_attempts + 1);
					if ($login_attempts > 3)
					{
						$this->session->set_userdata('login.attempts.countdown', time());
					}
				}
				elseif (!empty($status['user_id']) && !empty($status['user_name']))
				{
					$this->session->set_userdata('user_id', $status['user_id']);
					$this->session->set_userdata('user_name', $status['user_name']);

					$response = [
						'status'	 => 1,
						'message'	 => 'Đăng nhập thành công!'
					];
				}
				elseif ($status === -1)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Tài khoản không đủ quyền hạn để truy cập!'
					];
				}
				else
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Đã xảy ra lỗi!'
					];
				}
			}
			else
			{
				$response = [
					'status'	 => FALSE,
					'message'	 => validation_errors(),
				];
			}
		}
		$this->output->set_content_type('json');
		$this->output->append_output(json_encode($response));
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->output->set_header('Location: ' . $this->config->site_url());
	}

	public function change_password()
	{
		$this->render('user/change_password');
	}

	public function ajax_change_password()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span>');

		$this->form_validation->set_rules('password_old', 'Mật khẩu cũ', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password_new', 'Mật khẩu mới', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password_confirm', 'Nhập lại mật khẩu mới', 'required|min_length[4]|max_length[32]|matches[password_new]');

		if ($this->form_validation->run() === TRUE)
		{
			$username		 = $this->session->userdata('user_name');
			$password_old	 = $this->input->post('password_old');
			$password_new	 = $this->input->post('password_new');
			$status			 = $this->_u->change_password($username, $password_old, $password_new);
			if ($status === 0)
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Tài khoản không tồn tại!'
				];
			}
			elseif ($status === 1)
			{
				$response = [
					'status'	 => 1,
					'message'	 => 'Đổi mật khẩu thành công!'
				];
			}
			elseif ($status === -1)
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Dường như mật khẩu cũ giống y chang mật khẩu mới!'
				];
			}
			elseif ($status === -2)
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Mật khẩu cũ không đúng!'
				];
			}
			else
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Đã xảy ra lỗi!'
				];
			}
		}
		else
		{
			$response = [
				'status'	 => 0,
				'message'	 => validation_errors(),
			];
		}

		$this->output->set_content_type('json');
		$this->output->append_output(json_encode($response));
	}

}
