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

namespace Mirasvit\Feed\Ui\Dynamic\Variable\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        CollectionFactory $collectionFactory,
        string            $name,
        string            $primaryFieldName,
        string            $requestFieldName,
        array             $meta = [],
        array             $data = []
    ) {
        $this->collection = $collectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        $result = [];
        foreach ($this->collection as $model) {
            $data = $model->getData();
            if (isset($data['php_code'])) {
                unset($data['php_code']);
            }

            $result[$model->getId()] = $data;
        }

        return $result;
    }
}
