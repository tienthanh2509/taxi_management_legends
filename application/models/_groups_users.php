<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _groups_users
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _groups_users extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('ci_groups_users')->result_array();
	}

	public function count_all()
	{
		return $this->db->count_all('ci_groups_users');
	}

	public function get_by_uid($user_id)
	{
		$this->db->where('user_id', $user_id);
		return $this->db->get('ci_groups_users')->result_array();
	}

	public function get_by_gid($groups_id)
	{
		$this->db->where('group_id', $group_id);
		return $this->db->get('ci_groups_users')->result_array();
	}

	public function get_by_gid_uid($groups_id, $user_id)
	{
		$this->db->where('group_id', $group_id);
		$this->db->where('user_id', $user_id);
		return $this->db->get('ci_groups_users')->result_array();
	}

	public function add($param)
	{
		$this->db->trans_start();
		$this->db->insert('ci_groups_users', $param);
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

	public function update($group_id, $user_id, $param)
	{
		$this->db->trans_start();
		$this->db->where('group_id', $group_id);
		$this->db->where('user_id', $user_id);
		$this->db->update('ci_groups_users', $param);
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

	public function delete($group_id, $user_id)
	{
		$this->db->trans_start();
		$this->db->where('group_id', $group_id);
		$this->db->where('user_id', $user_id);
		$this->db->delete('ci_groups_users');
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
