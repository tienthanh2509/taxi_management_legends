<?php

/**
 * Description of Car
 *
 * @author phamthanh
 * @property List_cars $list_cars Model lớp Xe
 */
class Car extends Admin_Controller {

	public function index()
	{
		$this->render('dashboard_car_index');
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
		elseif(!in_array($sort, ['cid', 'license_plate', 'name']) || !in_array($order, ['asc', 'desc']))
		{
			show_404();
		}

		$this->load->model('list_cars');
		$danh_muc_xe = $this->list_cars->car_catalog($rows, (($page - 1) * $rows), $sort, $order);
		$this->output->set_content_type('json')->append_output(json_encode($danh_muc_xe));
	}

}
