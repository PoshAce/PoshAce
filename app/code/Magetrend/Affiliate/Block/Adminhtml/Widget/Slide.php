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

namespace Magetrend\Affiliate\Block\Adminhtml\Widget;

use Magento\Core\Model\ObjectManager;

class Slide extends \Magento\Backend\Block\Template
{
    public $json;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $json,
        array $data = []
    ) {
        $this->json = $json;
        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        return array_merge([
            'openSelector' => '.open-modal-edit',
            'modalSelector' => '#modal-container',
            'modalContext' => '#modal-context',
            'spinnerSelector' => '*[data-role="spinner"]',
        ], $this->getData());
    }

    public function getJsonConfig()
    {
        return $this->json->jsonEncode($this->getConfig());
    }
}
