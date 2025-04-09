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

namespace Mirasvit\Feed\Ui\Feed\Form\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\Feed;

class Info extends Template
{
    protected $_nameInLayout = "Mirasvit_Feed::html_info";

    protected $registry;

    public function __construct(
        Registry $registry,
        Context  $context
    ) {
        $this->registry = $registry;

        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->setTemplate('Mirasvit_Feed::feed/edit/tab/general/info.phtml');
    }

    public function getFeed(): Feed
    {
        return $this->registry->registry('current_model');
    }
}
