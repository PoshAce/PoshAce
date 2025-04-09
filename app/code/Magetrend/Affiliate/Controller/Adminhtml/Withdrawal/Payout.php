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

namespace Magetrend\Affiliate\Controller\Adminhtml\Withdrawal;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Payout extends \Magetrend\Affiliate\Controller\Adminhtml\Withdrawal
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');
            $amount = $this->getRequest()->getParam('amount_paid');
            $comment = $this->getRequest()->getParam('comment');
            try {
                $this->withdrawalManager->payout($id, $amount, $comment);
                $this->messageManager->addSuccess(__('The payout has been processed successful'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
