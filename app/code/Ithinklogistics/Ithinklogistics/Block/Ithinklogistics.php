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
 
namespace Ithinklogistics\Ithinklogistics\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;

class Ithinklogistics extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;
    protected $authContext;
    protected $assetRepository;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Http\Context $authContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        array $data = []
    ){
        $this->customerSession = $customerSession;
        $this->authContext = $authContext;
        $this->assetRepository = $assetRepository;
        parent::__construct($context, $data);
    }
}