<?php

/**
 * Description of Schedule
 *
 * @author phamthanh
 * @property Ci_d13ht01_model_schedule $model_schedule Lớp xử lý dữ liệu
 */
class Schedule extends Admin_Controller {

	public function index($year = NULL, $month = NULL)
	{
		$this->load->model('ci_d13ht01_model_schedule', 'model_schedule');

//		echo $this->model_schedule->find_min_date() . PHP_EOL;
//		echo $this->model_schedule->find_max_date() . PHP_EOL;
//		print_r($this->model_schedule->calendar_month());
//		print_r($this->model_schedule->get_schedule_by_week('2015-04-14'));
//		print_r($this->model_schedule->get_schedule_by_day('2015-04-14'));

		$this->data['min_date']	 = $this->model_schedule->find_min_date();
		$this->data['max_date']	 = $this->model_schedule->find_max_date();
		$this->data['sel_year']	 = $year ? $year : date('Y');
		$this->data['sel_month'] = $month ? $month : date('m');

		$this->data['min_year']	 = date('Y', $this->data['min_date']);
//		$this->data['min_month'] = date('m', $this->data['min_date']);
		$this->data['max_year']	 = date('Y', $this->data['max_date']);
//		$this->data['max_month'] = date('m', $this->data['max_date']);

		$this->data['calendar'] = $this->model_schedule->calendar_month($month, $year);

		$this->render('dashboard_schedule_index');
	}

	public function details($year = 2015, $month = 1, $day = 1)
	{
		
	}

	public function ajax_details()
	{
		
	}

}
