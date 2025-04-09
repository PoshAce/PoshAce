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
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;

class Library extends Feed
{
    protected $layout;

    public function __construct(
        FeedFactory $feedFactory,
        Registry    $registry,
        Context     $context
    ) {
        $this->layout = $context->getView()->getLayout();

        parent::__construct($feedFactory, $registry, $context);
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('pattern')) {
            $content = $this->layout->createBlock('Mirasvit\Feed\Block\Adminhtml\Feed\Library')
                ->setPattern($this->getRequest()->getParam('pattern'))
                ->setTemplate('Mirasvit_Feed::feed/library/preview.phtml')
                ->toHtml();
        } else {
            $content = $this->layout->createBlock('Mirasvit\Feed\Block\Adminhtml\Feed\Library')
                ->setTemplate('Mirasvit_Feed::feed/library.phtml')
                ->toHtml();
        }

        /** @var Interceptor $response */
        $response = $this->getResponse();

        return $response
            ->setBody($content);
    }

    public function _processUrlKeys(): bool
    {
        return true;
    }
}
