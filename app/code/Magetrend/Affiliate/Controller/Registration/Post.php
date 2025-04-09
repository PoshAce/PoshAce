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

namespace Magetrend\Affiliate\Controller\Registration;

use Magento\Framework\Exception\LocalizedException;

class Post extends \Magento\Framework\App\Action\Action
{
    public $registrationManager;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magetrend\Affiliate\Model\RegistrationManager $registrationManager
    ) {
        $this->registrationManager = $registrationManager;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost()->toArray();

        if (empty($postData)) {
            return $this->_redirect('404');
        }

        try {
            $this->registrationManager->registerAccount($postData);
            $this->messageManager->addSuccessMessage(__('The application has been sent successful. Thank you!'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if (isset($postData['back_url'])) {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($postData['back_url']);
            return $resultRedirect;
        }

        return $this->_redirect('404');
    }
}
