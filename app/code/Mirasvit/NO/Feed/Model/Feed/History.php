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

namespace Mirasvit\Feed\Model\Feed;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\Feed;

/**
 * @method int getFeedId()
 * @method $this setFeedId($id)
 * @method string getTitle()
 * @method $this setTitle($title)
 * @method string getMessage()
 * @method $this setMessage($message)
 * @method string getType()
 * @method $this setType($type)
 */
class History extends AbstractModel
{

    protected $historyFactory;

    public function __construct(
        HistoryFactory                   $historyFactory,
        Context $context,
        Registry      $registry
    ) {
        $this->historyFactory = $historyFactory;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\ResourceModel\Feed\History');
    }

    public function add(Feed $feed, string $title, string $message): self
    {
        /** @var History $history */
        $history = $this->historyFactory->create();
        $history->setFeedId($feed->getId())
            ->setTitle($title)
            ->setMessage($message)
            ->setType(php_sapi_name() == 'cli' ? 'CLI Mode' : 'Manual')
            ->save();

        return $this;
    }
}
