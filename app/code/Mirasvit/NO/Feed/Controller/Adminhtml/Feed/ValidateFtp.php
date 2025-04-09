<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.4.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\Interceptor;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Deliverer;

class ValidateFtp extends Feed
{
    protected $deliverer;

    protected $serializer;

    public function __construct(
        Deliverer   $deliverer,
        FeedFactory $feedFactory,
        Registry    $registry,
        Json        $serializer,
        Context     $context
    ) {
        $this->deliverer  = $deliverer;
        $this->serializer = $serializer;

        parent::__construct($feedFactory, $registry, $context);
    }

    public function execute()
    {
        $feed   = $this->initModel();
        $params = $this->getRequest()->getParam('ftp');

        if (is_array($params)) {
            $feed->addData($params);
        }

        try {
            $this->deliverer->validate($feed);
            $message = __('A connection was successfully established with "%1"', $feed->getFtpHost());
            $status  = 'success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $status  = 'error';
        }

        /** @var Interceptor $response */
        $response = $this->getResponse();
        $response->representJson($this->serializer->serialize([
            'status'  => $status,
            'message' => $message,
        ]));
    }
}
