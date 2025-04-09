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

namespace  Magetrend\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Program extends Action
{
    public $programManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magetrend\Affiliate\Model\ProgramManager $programManager
    ) {
        $this->programManager = $programManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
