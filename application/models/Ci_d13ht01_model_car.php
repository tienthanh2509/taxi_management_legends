<?php

/**
 * Description of List_cars
 *
 * @author phamthanh
 */
class Ci_d13ht01_model_car extends CI_Model {

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

	function delete($cid)
	{
		$this->db->trans_start();

		$this->db->where('cid', $cid);
		$this->db->delete('ci_cars');
		$row = $this->db->affected_rows();

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		elseif ($row < 1)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

}
