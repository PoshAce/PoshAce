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

namespace Magetrend\Affiliate\Controller\Withdrawal;

use Magento\Framework\Exception\LocalizedException;

class Post extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    public $withdrawal;

    public $accountHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magetrend\Affiliate\Model\Withdrawal $withdrawal,
        \Magetrend\Affiliate\Helper\Account $accountHelper
    ) {
        $this->withdrawal = $withdrawal;
        $this->resultPageFactory = $resultPageFactory;
        $this->accountHelper = $accountHelper;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $amount = $this->getRequest()->getParam('amount', 0);
        if ($amount > 0) {
            try {
                $referral = $this->accountHelper->getCurrentAccount();
                $this->withdrawal->validateRequest($amount, $referral->getId());
                $this->withdrawal->initRequest($amount, $referral->getId());
                $this->messageManager->addSuccessMessage(__('Thank you! We have got your withdrawal request!'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Ops... Something goes wrong.'));
            }
        }

        return $this->_redirect('affiliate/withdrawal/index');
    }
}
