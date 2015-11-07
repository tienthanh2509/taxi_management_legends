<?php

/**
 * Description of Car
 *
 * @author phamthanh
 * @property Ci_d13ht01_model_car $_car Model lớp Xe
 */
class Car extends Admin_Controller {

	public function index()
	{
		$this->render('dashboard_car_index');
	}

	public function ajax_model_list()
	{
		$manufacturer = $this->input->post('manufacturer');

		if (!$manufacturer)
		{
			show_404();
		}

		$this->load->model('Ci_d13ht01_model_car', '_car');

		$manufacturer_list = $this->_car->get_model_of_manufacturer($manufacturer);
		$this->output->set_content_type('json');
		$this->output->set_output(json_encode([
			'total'	 => count($manufacturer_list),
			'rows'	 => $manufacturer_list
		]));
	}

	public function ajax_car_catalog()
	{
		$page	 = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows	 = $this->input->post('rows') ? $this->input->post('rows') : 15;
		$sort	 = $this->input->post('sort') ? $this->input->post('sort') : 'cid';
		$order	 = $this->input->post('order') ? $this->input->post('order') : 'asc';

		if (!is_numeric($page) || !is_numeric($rows) || $page < 1 || $rows < 1 || $rows > 50)
		{
			show_404();
		}
		elseif (!in_array($sort, ['cid', 'license_plate', 'name']) || !in_array($order, ['asc', 'desc']))
		{
			show_404();
		}

		$this->load->model('Ci_d13ht01_model_car', '_car');
		$danh_muc_xe = $this->_car->car_catalog($rows, (($page - 1) * $rows), $sort, $order);
		$this->output->set_content_type('json')->append_output(json_encode($danh_muc_xe));
	}

	public function add()
	{
		$this->load->model('Ci_d13ht01_model_car', '_car');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_license_plate', 'Biển số', 'required|is_unique[ci_cars.license_plate]');
		//$this->form_validation->set_rules('ci_form_manufacturer', 'Nhà sản xuất', 'required');
		$this->form_validation->set_rules('ci_form_model', 'Model', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$status = $this->_car->add($this->input->post('ci_form_license_plate'), $this->input->post('ci_form_model'));

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

		$this->data['ci_form']['manufacturer_list'] = $this->_car->get_all_manufacturer();
		$this->render('dashboard_car_add');
	}

	public function edit($cid = '')
	{
		
	}

	public function delete($cid = '')
	{
		$this->load->model('Ci_d13ht01_model_car', '_car');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		if (!$cid)
		{
			$this->form_validation->set_rules('ci_form_cid', 'Mã NV', 'required|numeric');
			if ($this->form_validation->run() === TRUE)
			{
				$this->data['car'] = $this->_car->get_car_by_cid($this->input->post('ci_form_cid'));

				if (empty($this->data['car']))
				{
					$this->data['error_message'] = 'Không tìm thấy xe nào có mã là: ' . $this->input->post('ci_form_cid');
				}
				else
				{
					$this->output->set_header('Location: ' . $this->config->site_url('car/delete/' . $this->input->post('ci_form_cid')));
					return;
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}
		else
		{
			$this->data['car'] = $this->_car->get_car_by_cid($cid);

			if (empty($this->data['car']))
			{
				$this->data['error_message'] = 'Không tìm thấy xe nào có mã là: ' . $cid;
			}
			elseif ($this->input->post('confirm'))
			{
				$status = $this->_car->delete($cid);
				if ($status == 1)
				{
					$this->data['message'] = 'Đã xóa xe thành công!';
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
		}

		$this->data['cid'] = $cid;

		$this->render('dashboard_car_delete');
	}

	public function ajax_delete_car()
	{
		$this->load->model('Ci_d13ht01_model_car', '_car');

		$cid = $this->input->post('cid');

		if (!$cid)
		{
			$output = [
				'status'	 => -1,
				'message'	 => 'Dữ liệu đầu vào không hợp lệ!'
			];
		}
		else
		{
			$status = $this->_car->delete($cid);
			if ($status == 1)
			{
				$output = [
					'status'	 => $status,
					'message'	 => 'Đã xóa xe thành công!'
				];
			}
			elseif ($status == 0)
			{
				$output = [
					'status'	 => $status,
					'message'	 => 'Không thể xóa xe được yêu cầu!'
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

}
