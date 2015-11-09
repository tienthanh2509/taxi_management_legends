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
 */
class User extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_groups', '_g');
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

}
