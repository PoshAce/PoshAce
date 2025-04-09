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

namespace Magetrend\Affiliate\Controller\Adminhtml\FormBuilder;

class Index extends \Magetrend\Affiliate\Controller\Adminhtml\FormBuilder
{
    /**
     * Newsletter subscribers page
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Magetrend_Affiliate::formbuilder');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Forms'));

        $this->_addBreadcrumb(__('FormBuilder'), __('FormBuilder'));
        $this->_addBreadcrumb(__('Forms'), __('Forms'));

        $this->_view->renderLayout();
    }
}
