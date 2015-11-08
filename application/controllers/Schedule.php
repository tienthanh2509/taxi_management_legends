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

		$year	 = !empty($year) && is_numeric($year) ? $year : date('Y');
		$month	 = !empty($month) && is_numeric($month) && ($month > 0 && $month <= 12) ? $month : date('m');

		$this->data['min_date']	 = $this->model_schedule->find_min_date();
		$this->data['max_date']	 = $this->model_schedule->find_max_date();
		$this->data['max_date']	 = $this->data['max_date'] < time() ? time() : $this->data['max_date'];
		$this->data['sel_year']	 = $year;
		$this->data['sel_month'] = $month;

		$this->data['min_year']	 = date('Y', $this->data['min_date']);
		$this->data['max_year']	 = date('Y', $this->data['max_date']);

		$prefs						 = [];
		$prefs['show_other_days']	 = TRUE;
		$prefs['template']			 = [
			'table_open'			 => '<table class="table table-bordered">',
			'cal_cell_start_other'	 => '<td class="bg-warning">',
			'cal_cell_start_today'	 => '<td class="bg-primary">',
			'week_day_cell'			 => '<td class="bg-primary">{week_day}</td>',
		];

		$this->load->library('calendar', $prefs);

		$data	 = [];
		$m		 = $this->calendar->get_total_days($month, $year);
		for ($i = 1; $i < $m; $i++)
		{
			$data[$i] = $this->config->site_url('schedule/details/' . $year . '/' . $month . '/' . $i);
		}

		$this->data['calendar'] = $this->calendar->generate($year, $month, $data);

		$this->render('dashboard_schedule_index');
	}

	public function details($year = 2015, $month = 1, $day = 1)
	{
		
	}

	public function ajax_details()
	{
		
	}

}
