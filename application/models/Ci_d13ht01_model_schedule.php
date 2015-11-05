<?php

/**
 * Lớp xử lý dữ liệu cho chức năng lập lịch
 *
 * @author phamthanh
 */
class Ci_d13ht01_model_schedule extends CI_Model {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	/**
	 * Hàm tìm ngày đã lập lịch nhỏ nhất trên hệ thống
	 * 
	 * @return int Thời gian dưới dạng unix timestamp
	 */
	public function find_min_date()
	{
		$this->db->select_min('date');
		$query = $this->db->get('ci_schedule');

		foreach ($query->result_array() as $row)
		{
			return $row['date'];
		}

		return 0;
	}

	/**
	 * Hàm tìm ngày đã lập lịch lớn nhất trên hệ thống
	 * 
	 * @return int Thời gian dưới dạng unix timestamp
	 */
	public function find_max_date()
	{
		$this->db->select_max('date');
		$query = $this->db->get('ci_schedule');

		foreach ($query->result_array() as $row)
		{
			return $row['date'];
		}

		return 0;
	}

	/**
	 * 
	 * 
	 * @param mixed $month
	 * @param mixed $year
	 * @return array
	 */
	public function calendar_month($month = NULL, $year = NULL)
	{
		$month		 = $month ? $month : date('m');
		$year		 = $year ? $year : date('Y');
		$calendar	 = [];

		/* days and weeks vars now ... */
		$timestamp			 = mktime(0, 0, 0, $month, 1, $year);
		$timestamp_week		 = date('w', $timestamp); // Thứ của ngày
		$days_in_month		 = date('t', $timestamp); // Số ngày trong tháng
		$weeks_in_year		 = date('W', $timestamp); // Tuần trong năm
		$days_in_this_week	 = 0; // Số tuần
		$day_counter		 = 0; // Bộ đếm ngày
		$day_in_week		 = 0; // Bộ đếm số ngày trong tuần

		/**/
		// Tìm vị trí trước vị trí ngày đầu tiên trong tháng
		$calendar[$days_in_this_week]['week'] = $weeks_in_year;
		for ($i = 0; $i < $timestamp_week; $i++)
		{
			$day_in_week++;
			$calendar[$days_in_this_week]['rows'][] = 'x';
		}

		// Thêm các ngày tiếp theo vào bộ lịch
		for ($i = $day_counter; $i < $days_in_month; $i++)
		{
			$day_in_week++;
			$calendar[$days_in_this_week]['rows'][] = ++$day_counter;

			if ($day_in_week > 6)
			{
				$calendar[$days_in_this_week]['week']	 = $weeks_in_year++;
				$day_in_week							 = 0;
				$days_in_this_week++;
			}
		}

		// Phủ kín các ô còn trống
		for ($i = $day_in_week; $i < 7; $i++)
		{
			$calendar[$days_in_this_week]['week']	 = $weeks_in_year;
			$calendar[$days_in_this_week]['rows'][]	 = 'x';
		}

		return $calendar;
	}

	/**
	 * Tìm lịch phân công trong khoảng thời gian xác định
	 * 
	 * @param int $timestamp_begin
	 * @param int $timestamp_end
	 * @param int $page
	 * @param int $rows
	 * @return mixed
	 */
	private function __schedule_finder($timestamp_begin = 0, $timestamp_end = 0, $page = 1, $rows = 15)
	{
		// Kiểm tra khoảng thời gian đưa vào
		if (!$timestamp_begin || !$timestamp_end || $timestamp_begin < 0 || $timestamp_end < 0)
		{
			return NULL;
		}

		$response = [];

		$this->db->select('COUNT(ci_schedule.sid) AS total');
		$this->db->join('ci_cars', 'ci_schedule.cid = ci_cars.cid');
		$this->db->join('ci_users', 'ci_schedule.uid = ci_users.uid');
		$this->db->where('ci_schedule.date BETWEEN ' . $timestamp_begin . ' AND ' . $timestamp_end);
		$response['total'] = $this->db->get('ci_schedule')->first_row()->total;

		$this->db->select('ci_schedule.sid,
  ci_cars.license_plate,
  ci_users.uid,
  ci_users.first_name,
  ci_users.last_name,
  ci_schedule.date,
  ci_schedule.shift');
		$this->db->join('ci_cars', 'ci_schedule.cid = ci_cars.cid');
		$this->db->join('ci_users', 'ci_schedule.uid = ci_users.uid');
		$this->db->where('ci_schedule.date BETWEEN ' . $timestamp_begin . ' AND ' . $timestamp_end);
		$this->db->limit($rows, (($page - 1) * $rows));
		$response['rows'] = $this->db->get('ci_schedule')->result_array();

		return $response;
	}

	/**
	 * 
	 * @param string $day Y-m-d
	 * @param int $page
	 * @param int $rows
	 * @return mixed
	 */
	public function get_schedule_by_day($day = '', $page = 1, $rows = 15)
	{
		$timestamp = strtotime($day);

		if (!$timestamp || $timestamp < 0)
		{
			return NULL;
		}

		$timestamp_begin = $timestamp;
		$timestamp_end	 = strtotime('+1 day -1 second', $timestamp_begin);

		return $this->__schedule_finder($timestamp_begin, $timestamp_end, $page, $rows);
	}

	/**
	 * 
	 * @param string $day Y-m-d
	 * @param int $page
	 * @param int $rows
	 * @return mixed
	 */
	public function get_schedule_by_week($day = '', $page = 1, $rows = 15)
	{
		$timestamp = strtotime($day);

		if (!$timestamp || $timestamp < 0)
		{
			return NULL;
		}

		$timestamp_begin = $timestamp;
		$timestamp_end	 = strtotime('+1 week -1 second', $timestamp_begin);

		return $this->__schedule_finder($timestamp_begin, $timestamp_end, $page, $rows);
	}
}
