<?php

/**
 * Ithinklogistics
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ithinklogistics
 * @package     Ithinklogistics_Ithinklogistics
 * @copyright   Copyright (c) Ithinklogistics (https://www.ithinklogistics.com/)
 */ 

namespace Ithinklogistics\Ithinklogistics\Api;

interface OrderListInterface {

	/**
	 * POST for OrderList api
	 * @param string $status
	 * @param string $from_date
	 * @param string $page
	 * @param string $access_token
	 * @param string $secret_key
	 * @return mixed
	 */
	
	public function OrderList($status, $from_date, $page, $access_token, $secret_key);
}