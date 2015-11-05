<?php

/**
 * Description of User
 *
 * @author phamthanh
 * @property Ci_d13ht01_model_auth $auth
 */
class User extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');
	}

	public function login()
	{
		if ($this->session->userdata('ci_user'))
		{
			$response = [
				'status'	 => 1,
				'message'	 => 'Đăng nhập thành công!'
			];
		}
		else
		{
			$this->load->library('form_validation');

			$this->form_validation->set_error_delimiters('<span>', '</span>');
			$this->form_validation->set_rules('username', 'Tên tài khoản', 'required|min_length[6]|max_length[32]');
			$this->form_validation->set_rules('password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');
			//$this->form_validation->set_rules('ck_remember_me', 'Ghi nhớ', 'integer');

			if ($this->form_validation->run() === TRUE)
			{
				$this->load->model('ci_d13ht01_model_auth', 'auth');

				$username	 = $this->input->post('username');
				$password	 = $this->input->post('password');

				$status = $this->auth->login($username, $password);

				if ($status === 0)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Sai tài khoản hoặc mật khẩu?!'
					];
				}
				elseif ($status === 1)
				{
					$this->session->set_userdata('ci_user', $this->auth->user['username']);

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
		$this->load->model('ci_d13ht01_model_auth', 'auth');

		if (!$this->session->userdata('ci_user'))
		{
			$this->output->set_header('Location: ' . $this->config->site_url());
			return;
		}

		//$this->data['user'] = $this->auth->get_user_by_username($this->session->userdata('ci_user'));
		$this->render('user_change_password');
	}

	public function ajax_change_password()
	{
		if (!$this->session->userdata('ci_user'))
		{
			$response = [
				'status'	 => 0,
				'message'	 => 'Bạn chưa đăng nhập!'
			];
		}
		else
		{
			$this->load->library('form_validation');

			$this->form_validation->set_error_delimiters('<span>', '</span>');
			$this->form_validation->set_rules('password_old', 'Mật khẩu cũ', 'required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('password_new', 'Mật khẩu mới', 'required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('password_confirm', 'Nhập lại mật khẩu mới', 'required|min_length[4]|max_length[32]|matches[password_new]');

			if ($this->form_validation->run() === TRUE)
			{
				$this->load->model('ci_d13ht01_model_auth', 'auth');

				$username		 = $this->session->userdata('ci_user');
				$password_old	 = $this->input->post('password_old');
				$password_new	 = $this->input->post('password_new');

				$status = $this->auth->change_password($username, $password_old, $password_new);

				if ($status === 0)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Tài khoản không tồn tại!'
					];
				}
				elseif ($status === 1)
				{
					$this->session->set_userdata('ci_user', $this->auth->user['username']);

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
		}

		$this->output->set_content_type('json');
		$this->output->append_output(json_encode($response));
	}

}
