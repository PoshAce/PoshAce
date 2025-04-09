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

namespace Magetrend\Affiliate\Controller\Referral;

use Magetrend\Affiliate\Helper\Data as Helper;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    public $resultJsonFactory;

    public $moduleHelper;

    public $responseFactory;

    public $cookieManager;

    public $cookieMetadataFactory;

    public $recordManager;

    public $remoteAddress;

    public $storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magetrend\Affiliate\Model\RecordManager $recordManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->moduleHelper = $moduleHelper;
        $this->responseFactory = $responseFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->recordManager = $recordManager;
        $this->remoteAddress = $remoteAddress;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->moduleHelper->isActive()) {
            return $this->returnResponse(false);
        }

        $url = $this->getRequest()->getParam('url');
        if (strpos($url, '?'.Helper::REFERRAL_PARAM_KEY.'=') === false
            && strpos($url, '&'.Helper::REFERRAL_PARAM_KEY.'=') === false) {
            return $this->returnResponse(false);
        }

        $tmpUrl = explode('?'.Helper::REFERRAL_PARAM_KEY.'=', str_replace('&', '?', $url));
        if (count($tmpUrl) != 2) {
            return $this->returnResponse(false);
        }

        $referralCode = explode('?', end($tmpUrl));
        $referralCode = $referralCode[0];

        $url = rtrim(str_replace([
            Helper::REFERRAL_PARAM_KEY.'='.$referralCode.'&',
            Helper::REFERRAL_PARAM_KEY.'='.$referralCode,
        ], '', $url), '?');

        $isSaved = $this->recordManager->recordClick([
            'referral_code' => $referralCode,
            'time' => time(),
            'ip' => $this->remoteAddress->getRemoteAddress(),
            'url' => $url,
            'store_id' => $this->storeManager->getStore()->getId()
        ]);

        if ($isSaved) {
            $this->rememberUser($referralCode);
        }

        return $this->returnResponse(true);
    }

    private function rememberUser($referralCode)
    {
        $cookieName = \Magetrend\Affiliate\Helper\Data::REFERRAL_CODE_COOKIE_NAME;
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setHttpOnly(false)
            ->setDuration(60 * 60 * 24 * 365)
            ->setPath('/');
        $this->cookieManager->setPublicCookie($cookieName, $referralCode, $cookieMetadata);
    }

    private function returnResponse($success = true)
    {
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => $success]);
    }
}
