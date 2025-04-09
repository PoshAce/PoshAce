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

namespace Mirasvit\Feed\Helper;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Feed\Repository\ValidationRepository;
use Mirasvit\Feed\Export\Filter\Pool as FilterPool;
use Mirasvit\Feed\Export\Resolver\Pool as ResolverPool;
use Mirasvit\Feed\Model\Feed;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class Output extends AbstractHelper
{

    protected $filterPool;

    protected $resolverPool;

    protected $objectManager;

    protected $operatorInputByType = [
            'string'      => ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}'],
            'numeric'     => ['==', '!=', '>=', '>', '<=', '<'],
            'date'        => ['==', '>=', '<='],
            'select'      => ['==', '!='],
            'boolean'     => ['==', '!='],
            'multiselect' => ['{}', '!{}', '()', '!()'],
            'grid'        => ['()', '!()'],
        ];

    protected $operatorOptions = [
            '=='  => 'is',
            '!='  => 'is not',
            '>='  => 'equals or greater than',
            '<='  => 'equals or less than',
            '>'   => 'greater than',
            '<'   => 'less than',
            '{}'  => 'contains',
            '!{}' => 'does not contain',
            '()'  => 'is one of',
            '!()' => 'is not one of',
        ];

    private   $validationRepository;

    protected $context;

    public function __construct(
        ValidationRepository   $validationRepository,
        FilterPool             $filterPool,
        ResolverPool           $resolverPool,
        ObjectManagerInterface $objectManager,
        Context                $context
    ) {
        $this->validationRepository = $validationRepository;
        $this->filterPool           = $filterPool;
        $this->resolverPool         = $resolverPool;
        $this->objectManager        = $objectManager;
        $this->context              = $context;
        parent::__construct($context);
    }

    public function getAttributeOptions(string $excludeCode = null): array
    {
        $options = [];

        foreach ($this->resolverPool->getResolvers() as $resolver) {
            $attributes = $resolver->getAttributes();

            asort($attributes);

            foreach ($attributes as $code => $label) {
                if (
                    isset($excludeCode)
                    && strpos($code, 'dynamic') !== false
                    && $excludeCode === explode(':', $code)[1]
                ) {
                    continue;
                }

                $group                      = $this->getAttributeGroup($code);
                $options[$group]['label']   = $group;
                $options[$group]['value'][] = ['value' => $code, 'label' => $label];
            }
        }

        usort($options, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return array_values($options);
    }

    public function getAttributesCount(array $attributes): int
    {
        $result = 0;

        foreach ($attributes as $attributeGroup) {
            if (!empty($attributeGroup['value'])) {
                $result += count($attributeGroup['value']);
            }
        }

        return $result;
    }

    public function getPatternTypeOptions(): array
    {
        return [
            [
                'label' => 'Pattern',
                'value' => 'pattern',
            ],
            [
                'label' => 'Attribute',
                'value' => '',
            ],
            [
                'label' => 'Parent Product',
                'value' => 'parent',
            ],
            [
                'label' => 'Only Parent Product',
                'value' => 'only_parent',
            ],
            [
                'label' => 'Grouped Product',
                'value' => 'grouped',
            ],
        ];
    }

    public function getFilterOptions(): array
    {
        return $this->filterPool->getFilters();
    }

    public function getValidatorOptions(): array
    {
        $options = [];
        foreach ($this->validationRepository->getValidators() as $validator) {
            $options[] = [
                'label' => $validator->getName(),
                'value' => $validator->getCode(),
            ];
        }

        return $options;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAttributeGroup(string $code): string
    {
        $primary = [
            'attribute_set',
            'attribute_set_id',
            'entity_id',
            'full_description',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'name',
            'short_description',
            'description',
            'sku',
            'status',
            'status_parent',
            'url',
            'url_key',
            'visibility',
            'type_id',
        ];

        $stock = [
            'is_in_stock',
            'qty',
            'qty_children',
            'manage_stock',
            'stock_status',
        ];

        $stockInventory = [
            'qty_inventory',
            'manage_stock_inventory',
            'is_in_stock_inventory'
        ];

        $price = [
            'tax_class_id',
            'special_from_date',
            'special_to_date',
            'cost',
            'msrp',
        ];

        if (in_array($code, $primary)) {
            $group = __('1. Primary Attributes');
        } elseif (in_array($code, $price) || strpos($code, 'price') !== false) {
            $group = __('2. Prices & Taxes');
        } elseif (strpos($code, 'category') !== false) {
            $group = __('3. Category');
        } elseif (strpos($code, 'image') !== false || strpos($code, 'thumbnail') !== false) {
            $group = __('4. Images');
        } elseif (in_array($code, $stock)) {
            $group = __('5. Stock Attributes');
        } elseif (strpos($code, 'msi_stock') !== false) {
            $group = __('6.1 Multi Source Inventory Stocks');
        } elseif (strpos($code, 'inventory') !== false) {
            $group = __('6.2 Multi Source Inventory Sources');
        } elseif (strpos($code, 'dynamic') !== false) {
            $group = __('7. Dynamic Attributes');
        } elseif (strpos($code, 'variable') !== false) {
            $group = __('8. Dynamic Variables');
        } elseif (strpos($code, 'mapping') === 0) {
            $group = __('9. Category Mappings');
        } else {
            $group = __('Others Attributes');
        }

        return $group->__toString();
    }

    public function getAttributeOperators(string $attributeCode): array
    {
        $conditions = [];

        $attribute = $this->getAttribute($attributeCode);

        $type = 'string';

        if ($attribute) {
            switch ($attribute->getFrontendInput()) {
                case 'select':
                    $type = 'select';
                    break;

                case 'multiselect':
                    $type = 'multiselect';
                    break;

                case 'date':
                    $type = 'date';
                    break;

                case 'boolean':
                    $type = 'boolean';
                    break;

                default:
                    $type = 'string';
            }
        }
        foreach ($this->operatorInputByType[$type] as $operator) {
            $operatorTitle = __($this->operatorOptions[$operator]);
            $conditions[]  = [
                'label' => $operatorTitle,
                'value' => $operator,
            ];
        }

        return $conditions;
    }

    public function getAttributeValues(string $attributeCode): array
    {
        $result = [];

        $attribute = $this->getAttribute($attributeCode);
        if ($attribute) {
            if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                $result[] = ['label' => __('not set'), 'value' => ''];
                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $result[] = [
                        'label' => $option['label'],
                        'value' => $option['value'],
                    ];
                }
            }
        }

        return $result;
    }

    protected function getAttribute(string $code): ?Attribute
    {
        $entityTypeId = $this->objectManager->get('Magento\Eav\Model\Entity')
            ->setType('catalog_product')->getTypeId();

        /** @var Collection $attributes */
        $attributes = $this->objectManager
            ->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
            ->setEntityTypeFilter($entityTypeId);

        $attribute = $attributes->getItemByColumnValue('attribute_code', $code);

        if ($attribute) {
            return $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
                ->setEntityTypeId($entityTypeId)
                ->load($attribute->getId());
        }

        return null;
    }
}
