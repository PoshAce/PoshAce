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

namespace Magetrend\Affiliate\Controller\Account;

use Magento\Framework\Exception\LocalizedException;

class SettingsPost extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    public $accountHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magetrend\Affiliate\Helper\Account $accountHelper
    ) {
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
        $accountData = $this->getRequest()->getParam('account', []);

        try {
            $referral = $this->accountHelper->getCurrentAccount();
            $referral->updateAccount($accountData);
            $this->messageManager->addSuccessMessage(__('Information has been saved successfully'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Ops... Something goes wrong.'));
        }

        return $this->_redirect('affiliate/account/settings');
    }
}
