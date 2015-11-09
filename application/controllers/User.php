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

		$this->form_validation->set_rules('ci_form_car_lp', 'Biển số', 'required|is_unique[ci_cars.car_lp]');
		$this->form_validation->set_rules('ci_form_model_id', 'Model', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'car_lp'	 => $this->input->post('ci_form_car_lp'),
				'model_id'	 => $this->input->post('ci_form_model_id'),
			];
			$status	 = $this->_g->add($data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm xe mới';
			}
			else
			{
				$this->data['message'] = 'Đã thêm xe mới thành công';
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
		
	}

	public function group_delete($group_id = '')
	{
		
	}

}
