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


namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic\Attribute;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\Interceptor;
use Mirasvit\Feed\Helper\Output as OutputHelper;

class Attribute extends Action
{
    protected $outputHelper;

    public function __construct(
        OutputHelper $outputHelper,
        Context      $context
    ) {
        $this->outputHelper = $outputHelper;

        parent::__construct($context);
    }

    public function execute()
    {
        $attribute = $this->getRequest()->getParam('attribute');

        $result = [
            'operators'     => $this->outputHelper->getAttributeOperators($attribute),
            'attributeType' => 'select',
            'values'        => $this->outputHelper->getAttributeValues($attribute),
        ];

        /** @var Interceptor $response */
        $response = $this->getResponse();

        return $response
            ->representJson(json_encode($result));
    }

    public function _processUrlKeys(): bool
    {
        return true;
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Mirasvit_Feed::feed_dynamic_attribute');
    }
}
