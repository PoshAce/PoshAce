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

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\Interceptor;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Exporter;

class Progress extends Feed
{
    protected $exporter;

    protected $serializer;

    public function __construct(
        Exporter    $exporter,
        FeedFactory $feedFactory,
        Registry    $registry,
        Json        $serializer,
        Context     $context
    ) {
        $this->exporter   = $exporter;
        $this->serializer = $serializer;

        parent::__construct($feedFactory, $registry, $context);
    }

    public function execute()
    {
        $feed = $this->initModel();

        $progress = $this->exporter->getHandler($feed)->toJson();

        /** @var Interceptor $response */
        $response = $this->getResponse();
        $response->representJson($this->serializer->serialize($progress));
    }
}
