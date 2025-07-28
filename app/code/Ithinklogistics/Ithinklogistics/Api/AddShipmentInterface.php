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

interface AddShipmentInterface {

	/**
	 * POST for AddShipment api
	 * @param string $order_id
	 * @param string $tracking_number
	 * @param string $tracking_company
	 * @param string $notify
	 * @param string $comment
	 * @param string $access_token
	 * @param string $secret_key
	 * @return mixed
	 */
	
	public function AddShipment($order_id, $tracking_number, $tracking_company, $notify, $comment, $access_token, $secret_key);
}