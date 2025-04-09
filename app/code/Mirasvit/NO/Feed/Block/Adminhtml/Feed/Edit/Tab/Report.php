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

namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Api\Data\ValidationInterface;
use Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\Report\Grid;
use Mirasvit\Feed\Repository\ValidationRepository;
use Zend_Db_Expr;

class Report extends Container
{
    protected $_template = 'Mirasvit_Feed::feed/edit/tab/report.phtml';

    protected $registry;

    private   $validationRepository;

    public function __construct(
        ValidationRepository $validationRepository,
        Registry             $registry,
        Context              $context,
                             $data = []
    ) {
        $this->validationRepository = $validationRepository;
        $this->registry             = $registry;

        $this->_headerText = __('Feed Generation Report');

        parent::__construct($context, $data);
    }

    public function getErrors(): array
    {
        $result     = [];
        $collection = $this->validationRepository->getCollection();
        $collection->addFieldToFilter(ValidationInterface::FEED_ID, $this->registry->registry('current_model')->getId());

        $collection->getSelect()
            ->columns([
                'count' => new Zend_Db_Expr('count(' . ValidationInterface::ID . ')'),
                ValidationInterface::ATTRIBUTE,
                ValidationInterface::VALIDATOR,
            ])
            ->group(ValidationInterface::ATTRIBUTE);

        foreach ($collection as $error) {
            /** @var mixed $error */
            $validator = $this->validationRepository->getValidatorByCode($error->getValidator());
            $result[]  = [
                'count'     => $error['count'],
                'attribute' => $error[ValidationInterface::ATTRIBUTE],
                'hint'      => $validator->getHint($error[ValidationInterface::ATTRIBUTE]),
                'message'   => $this->validationRepository
                    ->getValidatorByCode($error[ValidationInterface::VALIDATOR])
                    ->getMessage(),
            ];
        }

        return $result;
    }

    public function getGridHtml(): string
    {
        return $this->getLayout()
            ->createBlock(Grid::class)
            ->toHtml();
    }
}
