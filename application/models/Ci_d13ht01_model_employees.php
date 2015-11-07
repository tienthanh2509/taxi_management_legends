<?php

/**
 * Description of Ci_d13ht01_model_employees
 *
 * @author phamthanh
 * @property Ci_d13ht01_model_auth $auth Auth
 */
class Ci_d13ht01_model_employees extends CI_Model {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	public function add($user_data, $group_list)
	{
		if (empty($user_data) || empty($group_list))
		{
			return 0;
		}

		$this->load->model('ci_d13ht01_model_auth', 'auth');

		$this->db->trans_start();

		$this->db->insert('ci_users', $user_data);
		$new_user = $this->auth->get_user_by_username($user_data['username']);

		$grant = [];

		foreach ($group_list as $group)
		{
			$grant[] = [
				'gid'	 => $group,
				'uid'	 => $new_user['uid']
			];
		}

		$this->db->insert_batch('ci_groups_users', $grant);

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

	public function update($uid, $user_data, $new_group)
	{
		if (empty($user_data) || empty($new_group))
		{
			return 0;
		}

		$this->load->model('ci_d13ht01_model_auth', 'auth');

		$this->db->trans_start();

		$this->db->where('uid', $uid);
		$this->db->update('ci_users', $user_data);
		$new_user = $this->auth->get_user_by_username($user_data['username']);

		$grant		 = [];
		$revoke		 = [];
		$group_list	 = [];
		$old_group	 = $this->auth->get_role_by_uid($uid);

		foreach ($this->get_all_group() as $value)
		{
			$group_list[] = $value['gid'];
		}

		foreach ($group_list as $g)
		{
			if (in_array($g, $old_group) && !in_array($g, $new_group))
			{
				$revoke[] = [
					'gid'	 => $g,
					'uid'	 => $uid
				];
			}

			if (!in_array($g, $old_group) && in_array($g, $new_group))
			{
				$grant[] = [
					'gid'	 => $g,
					'uid'	 => $uid
				];
			}
		}

		if (!empty($grant))
		{
			$this->db->insert_batch('ci_groups_users', $grant);
		}

		if (!empty($revoke))
		{
			foreach ($revoke as $value)
			{
				$this->db->delete('ci_groups_users', $value);
			}
		}

//		print_r($old_group);
//		print_r($new_group);
//		print_r($group_list);
//		print_r($grant);
//		print_r($revoke);

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

	public function ajax_data_grid($limit = 10, $offset = 0, $sort = 'ci_users.uid', $order = 'asc', $filter_rules = [])
	{
		return [
			'total'	 => $this->__ajax_data_grid('count', $limit, $offset, $sort, $order, $filter_rules),
			'rows'	 => $this->__ajax_data_grid('get', $limit, $offset, $sort, $order, $filter_rules)
		];
	}

	private function __ajax_data_grid($type = 'get', $limit = 10, $offset = 0, $sort = 'ci_users.uid', $order = 'asc', $filter_rules = [])
	{
		// Kiểm tra dữ liệu đầu vào
		$sort			 = ($sort == 'uid' ? 'ci_users.uid' : $sort);
		$sort			 = ($sort == 'role' ? 'ci_groups.description' : $sort);
		$filter_rules	 = is_array($filter_rules) ? $filter_rules : [];

		if ($type == 'get')
		{
			$col = [
				'ci_users.uid',
				'ci_users.username',
				'ci_users.email',
				'ci_users.hire_date',
				'ci_users.first_name',
				'ci_users.last_name',
				'ci_users.phone',
				'ci_users.birthday',
				'ci_users.hire_date',
				'ci_users.gender',
				'ci_users.active',
				'ci_groups.description AS role'
			];
			$this->db->select($col);
		}
		else
		{
			$this->db->select('COUNT(ci_users.uid) AS total');
		}

		$this->db->join('ci_groups_users', 'ci_groups_users.uid = ci_users.uid');
		$this->db->join('ci_groups', 'ci_groups_users.gid = ci_groups.gid');

		foreach ($filter_rules as $rule)
		{
			// Kiểm tra tên trường dữ liệu
			$rule['field'] = $rule['field'] === 'uid' ? 'ci_users.uid' : $rule['field'];

			if (in_array($rule['field'], ['birthday', 'hire_date']))
			{
				$timestamp = strtotime($rule['value']);

				if (!$timestamp)
				{
					continue;
				}

				if ($rule['op'] === 'less')
				{
					$this->db->where($rule['field'] . ' < ', $timestamp);
				}
				elseif ($rule['op'] === 'greater')
				{
					$this->db->where($rule['field'] . ' > ', $timestamp);
				}
				elseif ($rule['op'] === 'equal')
				{
					$this->db->where($rule['field'], $timestamp);
				}
				elseif ($rule['op'] === 'notequal')
				{
					$this->db->where($rule['field'] . ' <> ', $timestamp);
				}
			}
			elseif ($rule['op'] === 'equal')
			{
				$rule['field'] = $rule['field'] === 'role' ? 'ci_groups_users.gid' : $rule['field'];

				$this->db->where($rule['field'], $rule['value']);
			}
			else
			{
				$this->db->like($rule['field'], $rule['value']);
			}
		}

		$this->db->order_by($sort, $order);

		if ($type == 'get')
		{
			$this->db->limit($limit, $offset);
		}

		$query_data = $this->db->get('ci_users');

		if ($type == 'get')
		{
			return $query_data->result_array();
		}
		else
		{
			foreach ($query_data->result_array() as $row)
			{
				return $row['total'];
			}
		}
	}

	public function get_all_group()
	{
		$query_data = $this->db->get('ci_groups');

		return $query_data->result_array();
	}

}
