<?php

/**
 * Description of List_cars
 *
 * @author phamthanh
 */
class List_cars extends CI_Model {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	public function car_catalog($limit = 10, $offset = 0, $sort = 'cid', $order = 'asc')
	{
		// Tìm tổng số xe
		$this->db->select('COUNT(ci_cars.cid) AS total');
		$this->db->join('ci_cars_manufacturer', 'ci_cars.mid = ci_cars_manufacturer.m_id');
		$query_count = $this->db->get('ci_cars');

		$this->db->select('ci_cars.cid, ci_cars.license_plate, ci_cars_manufacturer.name');
		$this->db->join('ci_cars_manufacturer', 'ci_cars.mid = ci_cars_manufacturer.m_id');
		$this->db->limit($limit, $offset);
		$this->db->order_by($sort, $order);
		$query_data = $this->db->get('ci_cars');

		return ['total' => $query_count->first_row()->total, 'rows' => $query_data->result_array()];
	}

}
