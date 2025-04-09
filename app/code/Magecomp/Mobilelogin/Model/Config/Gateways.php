<?php
namespace Magecomp\Mobilelogin\Model\Config;

use Magento\Framework\App\ObjectManager;
use Magecomp\Mobilelogin\Helper\Apicall;

class Gateways extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{
    protected $apihelper;

    public function __construct(Apicall $apihelper, array $data = [])
    {
        $this->apihelper = $apihelper;
        parent::__construct($data);
    }

    public function toOptionArray()
    {
        $options = [['value'=>'', 'label'=>'Select SMS Gateway']];
        foreach ($this->apihelper->getSmsgatewaylist() as $key => $smsgateway) {
            $Smsgatewaymodel = ObjectManager::getInstance()->create($smsgateway);
            $options[] = ['value' => $key,'label' => $Smsgatewaymodel->getTitle()];
        }

        return $options;
    }
}
