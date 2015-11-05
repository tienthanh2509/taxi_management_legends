<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stat
 *
 * @author phamt
 */
class Stat extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->driver('session');

		if (!$this->session->get_userdata('ci_user'))
		{
			exit;
		}
	}

	public function ajax_month()
	{
		$this->load->database();

		$month_data = [];
		for ($j = 2014; $j <= 2015; $j++)
		{
			for ($i = 1; $i <= 12; $i++)
			{
				$timestamp_begin = strtotime($j . '-' . $i);
				$timestamp_end = strtotime('+1 month -1 second', $timestamp_begin);
				//echo $timestamp_end . ' - ' . date('Y-m-d', $timestamp_end) . PHP_EOL;

				$this->db->select_sum('total_price', 'total_price');
				$this->db->where('checkout_datetime BETWEEN ' . $timestamp_begin . ' AND ' . $timestamp_end);
				$query = $this->db->get('ci_order');

				foreach ($query->result_array() as $row)
				{
					$month_data[$i][$j] = $row['total_price'];
				}
			}
		}

		$this->output->set_content_type('json')->append_output(json_encode($month_data))->cache(500);
	}

}
