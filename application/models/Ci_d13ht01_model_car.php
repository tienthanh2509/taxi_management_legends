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

	public function add($license_plate, $mid)
	{
		if (empty($license_plate) || empty($mid))
		{
			return -1;
		}

		$this->db->trans_start();

		$this->db->insert('ci_cars', ['license_plate' => $license_plate, 'mid' => $mid]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	public function update($cid, $license_plate, $model)
	{
		if (empty($cid) || empty($license_plate) || empty($model))
		{
			return -1;
		}

		$this->db->trans_start();

		$data = [
			'mid'			 => $model,
			'license_plate'	 => $license_plate,
		];
		$this->db->where('cid', $cid);
		$this->db->update('ci_cars', $data);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	function get_car_by_cid($cid)
	{
		return $this->__get_car_by_xxx('cid', $cid);
	}

	private function __get_car_by_xxx($xxx, $yyy)
	{
		$this->db->where($xxx, $yyy);

		$query = $this->db->get('ci_cars');

		foreach ($query->result_array() as $row)
		{
			return $row;
		}

		return NULL;
	}

	public function get_manufacturer_by_cid($cid)
	{
		$this->db->select('ci_cars.cid, ci_cars.license_plate'
			. ', ci_cars_model.mid as model_id'
			. ', ci_cars_model.name as model_name'
			. ', ci_cars_manufacturer.mid as manufacturer_id'
			. ', ci_cars_manufacturer.name as manufacturer_name');
		$this->db->where('cid', $cid);
		$this->db->join('ci_cars_model', 'ci_cars.mid = ci_cars_model.mid');
		$this->db->join('ci_cars_manufacturer', 'ci_cars_manufacturer.mid = ci_cars_model.manufacturer');
		$this->db->limit(1);

		$query = $this->db->get('ci_cars');

		foreach ($query->result_array() as $row)
		{
			return $row;
		}

		return NULL;
	}

	public function get_model_of_manufacturer($mid)
	{
		if (!$mid)
		{
			return [];
		}

		$this->db->where('manufacturer', $mid);
		$query_data = $this->db->get('ci_cars_model');

		return $query_data->result_array();
	}

	public function get_all_model()
	{
		$query_data = $this->db->get('ci_cars_model');

		return $query_data->result_array();
	}

	public function get_all_manufacturer()
	{
		$query_data = $this->db->get('ci_cars_manufacturer');

		return $query_data->result_array();
	}

	public function car_catalog($limit = 10, $offset = 0, $sort = 'cid', $order = 'asc')
	{
		// Tìm tổng số xe
		$this->db->select('COUNT(ci_cars.cid) AS total');
		$this->db->join('ci_cars_model', 'ci_cars.mid = ci_cars_model.mid');
		$query_count = $this->db->get('ci_cars');

		$this->db->select('ci_cars.cid, ci_cars.license_plate, ci_cars_model.name as model, ci_cars_manufacturer.name as manufacturer');
		$this->db->join('ci_cars_model', 'ci_cars.mid = ci_cars_model.mid');
		$this->db->join('ci_cars_manufacturer', 'ci_cars_model.manufacturer = ci_cars_manufacturer.mid');
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
