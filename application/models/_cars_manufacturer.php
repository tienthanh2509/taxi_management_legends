<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _cars_manufacturer
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _cars_manufacturer extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all()
	{
		return $this->db->get('ci_cars_manufacturer')->result_array();
	}

	public function get_by_id($manufacturer_id)
	{
		$this->db->where('manufacturer_id', $manufacturer_id);
		$data = $this->db->get('ci_cars_manufacturer')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param)
	{
		$this->db->trans_start();
		$this->db->insert('ci_cars_manufacturer', $param);
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

	public function update($manufacturer_id, $param)
	{
		$this->db->trans_start();
		$this->db->where('manufacturer_id', $manufacturer_id);
		$this->db->update('ci_cars_manufacturer', $param);
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

	public function delete($manufacturer_id)
	{
		$this->db->trans_start();
		$this->db->where('manufacturer_id', $manufacturer_id);
		$this->db->delete('ci_cars_manufacturer');
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

}
