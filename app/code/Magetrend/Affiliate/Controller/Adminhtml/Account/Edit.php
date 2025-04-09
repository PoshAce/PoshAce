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

namespace Magetrend\Affiliate\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;

class Edit extends \Magetrend\Affiliate\Controller\Adminhtml\Account
{
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magetrend_Affiliate::account');
        return $resultPage;
    }

    /**
     * Edit Blog Post
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magetrend\Affiliate\Model\Account');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This object no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->coreRegistry->register('current_model', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Affiliate Account') : __('New Affiliate Account'),
            $id ? __('Edit Affiliate Account') : __('New Affiliate Account')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Affiliate Account'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Affiliate Account') : __('New Affiliate Account')
        );

        return $resultPage;
    }
}
