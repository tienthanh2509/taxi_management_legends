<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Thao tác với Xe
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _cars_manufacturer $_cm Lớp thao tác dữ liệu
 * @property _cars_model $_cm2 Lớp thao tác dữ liệu
 * @property _cars $_c Lớp thao tác dữ liệu
 */
class Car extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_cars_manufacturer', '_cm');
		$this->load->model('_cars_model', '_cm2');
		$this->load->model('_cars', '_c');
	}

	public function index()
	{
		$page = $this->uri->segment(3, 1);

		$this->data['car_list'] = $this->_c->get_all(30, ($page - 1) * 30);

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('car/index');
		$config['total_rows']	 = $this->_c->count_all();
		$config['per_page']		 = 30;
		$config['uri_segment']	 = 3;

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('car/welcome');
	}

	public function car_add()
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
			$status	 = $this->_c->add($data);

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

		$this->data['model_list'] = $this->_cm2->get_all();
		$this->render('car/car_add');
	}

	public function car_edit($car_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_car_lp', 'Biển số', 'required');
		$this->form_validation->set_rules('ci_form_model_id', 'Model', 'required');

		$this->data['car'] = $this->_c->get_by_id($car_id);

		if (empty($this->data['car']))
		{
			$this->data['error_message'] = 'Không tìm thấy xe nào có mã là: ' . $car_id;
		}
		else
		{
			if ($this->form_validation->run() === TRUE)
			{
				$data	 = [
					'car_lp'	 => $this->input->post('ci_form_car_lp'),
					'model_id'	 => $this->input->post('ci_form_model_id'),
				];
				$status	 = $this->_c->update($car_id, $data);

				if ($status === 0)
				{
					$this->data['error_message'] = 'Không thể cập nhật thông tin xe';
				}
				else
				{
					$this->data['message'] = 'Đã cập nhật thông tin thành công';
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}

		$this->data['model_list'] = $this->_cm2->get_all();
		$this->render('car/car_edit');
	}

	public function car_delete($car_id = '')
	{
		if (!$car_id)
		{
			show_404();
		}

		$this->data['car'] = $this->_c->get_by_id($car_id);

		if (empty($this->data['car']))
		{
			show_404();
		}

		if ($this->input->post('confirm'))
		{
			$status = $this->_c->delete($car_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa xe "' . $this->data['car']['car_lp'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa xe được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('car/car_delete');
	}

	public function model()
	{
		$this->data['model_list'] = $this->_cm2->get_all();
		$this->render('car/model');
	}

	public function model_add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_id', 'NSX', 'required');
		$this->form_validation->set_rules('ci_form_model_name', 'Tên mẫu xe', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'model_name'		 => $this->input->post('ci_form_model_name'),
				'manufacturer_id'	 => $this->input->post('ci_form_manufacturer_id'),
			];
			$status	 = $this->_cm2->add($data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm mẫu xe mới';
			}
			else
			{
				$this->data['message'] = 'Đã thêm mẫu xe mới thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['manufacturer_list'] = $this->_cm->get_all();
		$this->render('car/model_add');
	}

	public function model_edit($model_id = '')
	{
		if (!$model_id)
		{
			show_404();
		}

		$this->data['model'] = $this->_cm2->get_by_id($model_id);

		if (empty($this->data['model']))
		{
			show_404();
		}

		$this->data['manufacturer_list'] = $this->_cm->get_all();

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_id', 'Tên NSX', 'required');
		$this->form_validation->set_rules('ci_form_model_name', 'Tên mẫu xe', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'model_name'		 => $this->input->post('ci_form_model_name'),
				'manufacturer_id'	 => $this->input->post('ci_form_manufacturer_id'),
			];
			$status	 = $this->_cm2->update($model_id, $data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin mẫu xe';
			}
			else
			{
				$this->data['message'] = 'Đã cập nhật thông tin mẫu xe thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('car/model_edit');
	}

	public function model_delete($model_id = '')
	{
		if (!$model_id)
		{
			show_404();
		}

		$this->data['model'] = $this->_cm2->get_by_id($model_id);

		if (empty($this->data['model']))
		{
			show_404();
		}

		if ($this->input->post('confirm'))
		{
			$status = $this->_cm2->delete($model_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa mẫu xe "' . $this->data['model']['model_name'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa mẫu xe được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('car/model_delete');
	}

	public function manufacturer()
	{
		$this->data['manufacturer_list'] = $this->_cm->get_all();
		$this->render('car/manufacturer');
	}

	public function manufacturer_add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_name', 'Tên NSX', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'manufacturer_name' => $this->input->post('ci_form_manufacturer_name'),
			];
			$status	 = $this->_cm->add($data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm nhà sản xuất mới';
			}
			else
			{
				$this->data['message'] = 'Đã thêm nhà sản xuất mới thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('car/manufacturer_add');
	}

	public function manufacturer_edit($manufacturer_id = '')
	{
		if (!$manufacturer_id)
		{
			show_404();
		}

		$this->data['manufacturer'] = $this->_cm->get_by_id($manufacturer_id);

		if (empty($this->data['manufacturer']))
		{
			show_404();
		}

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_name', 'Tên NSX', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'manufacturer_name' => $this->input->post('ci_form_manufacturer_name'),
			];
			$status	 = $this->_cm->update($manufacturer_id, $data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin nhà sản xuất';
			}
			else
			{
				$this->data['message'] = 'Đã cập nhật thông tin nhà sản xuất thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}
		$this->render('car/manufacturer_edit');
	}

	public function manufacturer_delete($manufacturer_id = '')
	{
		if (!$manufacturer_id)
		{
			show_404();
		}

		$this->data['manufacturer'] = $this->_cm->get_by_id($manufacturer_id);

		if (empty($this->data['manufacturer']))
		{
			show_404();
		}

		if ($this->input->post('confirm'))
		{
			$status = $this->_cm->delete($manufacturer_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa nhà sản xuất "' . $this->data['manufacturer']['manufacturer_name'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa nsx xe được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('car/manufacturer_delete');
	}

}
