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

namespace Mirasvit\Feed\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Exporter;
use Mirasvit\Feed\Model\Feed;

abstract class Export extends Action
{
    protected $feedFactory;

    protected $exporter;

    protected $serializer;

    public function __construct(
        FeedFactory $feedFactory,
        Exporter    $exporter,
        Json        $serializer,
        Context     $context
    ) {
        $this->feedFactory = $feedFactory;
        $this->exporter    = $exporter;
        $this->serializer  = $serializer;

        parent::__construct($context);
    }


    protected function getFeed(): Feed
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $feed = $this->feedFactory->create()->load($id);

            return $feed;
        }

        return false;
    }
}
