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

namespace Magetrend\Affiliate\Helper;

class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $transportBuilder;

    public $moduleHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    public function sendEmail($configurationPath, $email, $data, $storeId = 0)
    {
        $templateId = $this->scopeConfig->getValue(
            $configurationPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $senderName = $this->moduleHelper->getSenderName($storeId);
        $senderEmail = $this->moduleHelper->getSenderEmail($storeId);
        $message = $this->transportBuilder->createMessage()
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($data)
            ->setFrom([
                'email' => $senderEmail,
                'name' => $senderName
            ])
            ->addTo($email)
            ->setReplyTo($senderEmail, $senderName);

        $copyTo = $this->scopeConfig->getValue(
            $configurationPath.'_copy_to',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!empty($copyTo)) {
            $message->addBcc($copyTo);
        }

        $transport = $message->getTransport();
        $transport->sendMessage();
    }
}
