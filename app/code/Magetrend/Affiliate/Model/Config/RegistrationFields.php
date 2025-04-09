<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Model\Config;

class RegistrationFields
{
    public function getData()
    {
        return[
            'firstname' => [
                'name' => 'firstname',
                'label' => 'First Name',
                'type' => 'text',
                'options' => null,
                'class' =>  '',
                'additional_data' => [ 'required' => true ]
            ],

            'lastname' => [
                'name' => 'lastname',
                'label' => 'Last Name',
                'type' => 'text',
                'options' => null,
                'class' =>  '',
                'additional_data' => [ 'required' => true ]
            ],

            'store_id' => [
                'name' => 'store_id',
                'type' => 'hidden',
                'label' => '',
                'options' => null,
                'class' =>  '',
                'additional_data' => [ 'required' => true ]
            ],

            'email' => [
                'name' => 'email',
                'label' => 'Email Address',
                'type' => 'text',
                'options' => null,
                'class' =>  'mtaf-validator-email',
                'additional_data' => [ 'required' => true ]
            ],

            'website' => [
                'name' => 'website',
                'label' => 'Website',
                'type' => 'text',
                'options' => null,
                'class' =>  '',
                'additional_data' => [ 'required' => false ]
            ],

            'comment' => [
                'name' => 'comment',
                'label' => 'Notices',
                'type' => 'textarea',
                'options' => null,
                'class' =>  '',
                'additional_data' => [ 'required' => false ]
            ]
        ];
    }
}
