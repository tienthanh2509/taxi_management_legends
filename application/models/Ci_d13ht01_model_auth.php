<?php

/**
 * Description of Ci_d13ht01_model_auth
 *
 * @author phamt
 */
class Ci_d13ht01_model_auth extends CI_Model {

	public $user;

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	function get_user_by_email($email)
	{
		return $this->__get_user_by_xxx('email', $email);
	}

	function get_user_by_uid($uid)
	{
		return $this->__get_user_by_xxx('uid', $uid);
	}

	function get_user_by_username($username)
	{
		return $this->__get_user_by_xxx('username', $username);
	}

	private function __get_user_by_xxx($xxx, $yyy)
	{
		$this->db->where($xxx, $yyy);

		$query = $this->db->get('ci_users');

		foreach ($query->result_array() as $row)
		{
			return $row;
		}

		return NULL;
	}

	function get_role_by_uid($uid)
	{
		$this->db->select('gid');
		$this->db->where('uid', $uid);

		$query = $this->db->get('ci_groups_users');

		$groups = [];
		foreach ($query->result_array() as $row)
		{
			$groups[] = $row['gid'];
		}

		return $groups;
	}

	function is_admin($userid)
	{
		$this->db->where('gid', 1);
		$this->db->where('uid', $userid);

		$query = $this->db->get('ci_groups_users');

		if ($query->num_rows() < 1)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function login($username, $password)
	{
		$user = $this->auth->get_user_by_email($username);

		if (empty($user) || !password_verify($password, $user['password']))
		{
			return 0;
		}
		else
		{
			$this->user = $user;

			return $this->is_admin($user['uid']) ? 1 : -1;
		}
	}

	function delete_user($uid)
	{
		$this->db->where('uid', $uid);
		$this->db->delete('ci_users');
		$row = $this->db->affected_rows();

		if ($row < 1)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	function change_password($username, $password_old, $password_new)
	{
		$user = $this->get_user_by_username($username);

		if (!$user)
		{
			return 0;
		}
		elseif ($password_old == $password_new)
		{
			return -1;
		}
		elseif (!password_verify($password_old, $user['password']))
		{
			return -2;
		}
		else
		{
			$data = [
				'password' => password_hash($password_new, PASSWORD_DEFAULT)
			];
			$this->db->update('ci_users', $data);

			$row = $this->db->affected_rows();

			if ($row < 1)
			{
				return 2;
			}
			else
			{
				return 1;
			}
		}
	}

}
