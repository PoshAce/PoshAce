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

namespace Mirasvit\Feed\Export\Resolver;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Model\Product\Type;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Relation as ProductRelation;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface as ProductMetadata;
use Magento\Framework\App\ResourceConnection;
use Magento\Inventory\Model\Source;
use Magento\Inventory\Model\Stock;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Mirasvit\Feed\Export\Context;
use Magento\Swatches\Helper\Data as SwatchesHelper;
use Magento\Framework\Module\Manager;
use Mirasvit\Feed\Export\Resolver\Product\AbstractResolver as ProductAbstractResolver;
use Mirasvit\Feed\Model\Dynamic\Attribute;
use Magento\Eav\Model\Entity\Attribute as EntityAttribute;
use Mirasvit\Feed\Model\Dynamic\Variable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductResolver extends AbstractResolver
{

    private static $products = [];

    private static $attributes;

    private static $mappings = [];

    private        $stockRegistry;

    private        $productRelation;

    private        $configurableManager;

    private        $attributeCollectionFactory;

    private        $productMetadata;

    protected      $resource;

    private        $productFactory;

    private        $swatchesHelper;

    private        $moduleManager;

    private        $resolvers;

    private        $dynamicAttribute;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        StockRegistryInterface     $stockRegistry,
        ProductRelation            $productRelation,
        AttributeCollectionFactory $attributeCollectionFactory,
        ProductFactory             $productFactory,
        ProductMetadata            $productMetadata,
        ResourceConnection         $resource,
        SwatchesHelper             $swatchesHelper,
        Context                    $context,
        ObjectManagerInterface     $objectManager,
        Configurable               $configurableManager,
        Attribute                  $dynamicAttribute,
        Manager                    $moduleManager,
                                   $resolvers = []
    ) {
        $this->stockRegistry              = $stockRegistry;
        $this->productRelation            = $productRelation;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->productFactory             = $productFactory;
        $this->productMetadata            = $productMetadata;
        $this->resource                   = $resource;
        $this->swatchesHelper             = $swatchesHelper;
        $this->configurableManager        = $configurableManager;
        $this->dynamicAttribute           = $dynamicAttribute;
        $this->moduleManager              = $moduleManager;

        $this->resolvers = $resolvers;

        parent::__construct($context, $objectManager);
    }

    public function getAttributes(): array
    {
        $result = [
            'entity_id'          => 'Product Id',
            'is_in_stock'        => 'Is In Stock',
            'stock_status'       => 'Stock Status',
            'qty'                => 'Qty',
            'qty_children'       => 'Qty of children in stock products',
            'manage_stock'       => 'Manage Stock',
            'image'              => 'Image',
            'url'                => 'Product Url',
            'url_with_options'   => 'Product Url with custom options',
            'category.entity_id' => 'Category Id',
            'category.name'      => 'Category Name',
            'category.path'      => 'Category Path (Category > Sub Category)',
            'images'             => 'Gallery image collection',
            'gallery[0]'         => 'Image 2',
            'gallery[1]'         => 'Image 3',
            'gallery[2]'         => 'Image 4',
            'gallery[3]'         => 'Image 5',
            'attribute_set'      => 'Attribute Set',
            'type_id'            => 'Product Type',
            'price'              => 'Price',
            'regular_price'      => 'Regular Price',
            'special_price'      => 'Special Price',
            'final_price'        => 'Final Price',
            'final_price_tax'    => 'Final Price with Tax',
            'tax_rate'           => 'Tax Rate',
        ];

        $entityTypeId = $this->objectManager->get('Magento\Eav\Model\Entity')
            ->setType(Product::ENTITY)->getTypeId();

        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('entity_type_id', $entityTypeId);

        foreach ($collection as $attribute) {
            /** @var EntityAttribute $attribute */
            if ($attribute->getStoreLabel()) {
                $code = $attribute->getAttributeCode();
                if (!isset($result[$code])) {
                    $result[$code] = $attribute->getStoreLabel() . ' [' . $code . ']';
                }
            }
        }

        $mappingCollectionFactory
            = $this->objectManager->create('Mirasvit\Feed\Model\ResourceModel\Dynamic\Category\CollectionFactory');

        /** @var \Mirasvit\Feed\Model\Dynamic\Category $mapping */
        foreach ($mappingCollectionFactory->create() as $mapping) {
            $label                                  = $mapping->getName();
            $result['mapping:' . $mapping->getId()] = __('Category Mapping') . ': ' . $label;
        }

        $dynamicCollectionFactory
            = $this->objectManager->create('Mirasvit\Feed\Model\ResourceModel\Dynamic\Attribute\CollectionFactory');

        /** @var Attribute $attribute */
        foreach ($dynamicCollectionFactory->create() as $attribute) {
            $label                                      = $attribute->getName();
            $result['dynamic:' . $attribute->getCode()] = __('Dynamic Attribute') . ': ' . $label;
        }

        /** mp comment start **/
        $dynamicVariableCollectionFactory
            = $this->objectManager->create('Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable\CollectionFactory');
        /** mp comment end **/
        /** @var Variable $variable */
        /** mp comment start **/
        foreach ($dynamicVariableCollectionFactory->create() as $variable) {
            $label                                      = $variable->getName();
            $result['variable:' . $variable->getCode()] = __('Dynamic Variable') . ': ' . $label;
        }
        /** mp comment end **/

        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $inventorySourceCollection = $this->objectManager->get('\Magento\Inventory\Model\ResourceModel\Source\CollectionFactory');

            /** @var Source $source */
            foreach ($inventorySourceCollection->create() as $source) {
                $label                                           = $source->getName();
                $result['inventory:' . $source->getSourceCode()] = __('Inventory') . ': ' . $label;
            }
        }

        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $inventoryStockCollection = $this->objectManager->get('\Magento\Inventory\Model\ResourceModel\Stock\CollectionFactory');

            /** @var Stock $stock */
            foreach ($inventoryStockCollection->create() as $stock) {
                $label                                       = $stock->getName();
                $result['msi_stock:' . $stock->getStockId()] = __('MSI Stock') . ': ' . $label;
            }
        }

        return $result;
    }

    public function getUrl(Product $product, $options = false): string
    {
        $url = $product->getProductUrl();

        $getParams     = [];
        $customOptions = [];

        $feed = $this->getFeed();

        if ($feed && $feed->getReportEnabled()) {
            $getParams['ff'] = $feed->getId();
            $getParams['fp'] = $product->getId();
        }

        if ($feed && $options === true) {
            if ($product->getTypeId() == Type::TYPE_SIMPLE) {
                $parentProduct = $this->getParent($product);
                $url           = $parentProduct->getProductUrl();
                if ($parentProduct->getTypeId() == Configurable::TYPE_CODE) {
                    $productAttributeOptions = $this->configurableManager->getConfigurableAttributesAsArray($parentProduct);
                    foreach ($productAttributeOptions as $option => $optionVal) {
                        $productAttributeValue = $product[$optionVal['attribute_code']];
                        if (isset($productAttributeValue)) {
                            $customOptions[] = $option . '=' . $productAttributeValue;
                        }
                    }
                }
            }
        }

        $utmMap = [
            'utm_source'   => 'ga_source',
            'utm_medium'   => 'ga_medium',
            'utm_campaign' => 'ga_name',
            'utm_term'     => 'ga_term',
            'utm_content'  => 'ga_content',
        ];

        foreach ($utmMap as $key => $value) {
            if ($feed && $feed->getData($value)) {
                $getParams[$key] = $this->getFeed()->getData($value);
                if (preg_match('/{{product.*?}}/is', $getParams[$key])) {
                    $getParams[$key] = $this->dynamicAttribute->getLiquidValue(
                        $this,
                        $getParams[$key],
                        ['product' => $product]
                    );
                }
            }
        }

        if (count($getParams)) {
            $url .= strpos($url, '?') !== false ? '&' : '?';
            $url .= http_build_query($getParams);
        }

        if (!empty($customOptions)) {
            $url .= '#' . implode('&', $customOptions);
        }

        // some products, such as those without a URL key or any rewrites created, may include a URL with an admin path
        $backendFrontName = $this->objectManager->get(FrontNameResolver::class)->getFrontName();
        if (str_contains($url, $backendFrontName)) {
            $url = $this->storeManager->getStore()->getBaseUrl() . 'catalog/product/view/id/' . $product->getId();
        }

        return $url;
    }

    public function getUrlWithOptions(Product $product): string
    {
        $url = $this->getUrl($product, true);

        return $url;
    }

    public function getQty(Product $product): int
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        return (int)$stockItem->getQty();
    }

    public function getManageStock(Product $product): bool
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        return (bool)$stockItem->getManageStock();
    }

    public function getQtyChildren(Product $product): ?int
    {
        if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
            $children = $this->getAssociatedProducts($product);
            $stockQty = 0;
            if (count($children) > 0) {
                foreach ($children as $child) {
                    if ($child->isSalable() === true) {
                        $stockQty += $this->getQty($child);
                    }
                }

                return $stockQty;
            }

            return null;
        } else {
            return $this->getQty($product);
        }
    }

    public function getDescription(Product $product): string
    {
        $description = (string)$product->getDescription();
        $description = html_entity_decode($description);

        return $description;
    }

    public function getIsInStock(Product $product): bool
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $isInStock = false;
            $sourceItems = [];
            $getSourceItemsBySku = $this->objectManager->create('\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface');

            if ($product->getTypeId() === Type::TYPE_BUNDLE) {
                return $product->getTypeInstance()->isSalable($product);
            }

            if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
                foreach ($this->getAssociatedProducts($product) as $associatedProduct) {
                    $sourceItems = array_merge(
                        $sourceItems,
                        $getSourceItemsBySku->execute($associatedProduct->getSku())
                    );
                }
            } else {
                $sourceItems = $getSourceItemsBySku->execute($product->getSku());
            }

            foreach ($sourceItems as $item) {
                if ($item->getStatus() && $item->getQuantity() > 0) {
                    $isInStock = true;
                    break;
                }
            }

            return $isInStock;
        } else {
            $stockItem = $this->stockRegistry->getStockItem($product->getId());

            return (bool)$stockItem->getIsInStock();
        }
    }

    public function getStockStatus(Product $product): string
    {
        return $this->getIsInStock($product) ? 'in stock' : 'out of stock';
    }

    public function getAttributeSet(Product $product): string
    {
        $attributeSetModel = $this->objectManager->create('\Magento\Eav\Model\Entity\Attribute\Set');
        $attributeSetModel->load($product->getAttributeSetId());

        return $attributeSetModel->getAttributeSetName();
    }

    public function getParent(Product $product): Product
    {
        $parents = $this->getParents($product);

        foreach ($parents as $parent) {
            if (
                (int)$product->getId() !== (int)$parent->getId()
                && $parent->getTypeId() !== Type::TYPE_SIMPLE
            ) {
                return $parent;
            }
        }

        return $product;
    }

    public function getParents(Product $product): array
    {
        $magentoEdition = $this->productMetadata->getEdition();
        $select = $this->productRelation->getConnection()->select()->from(
            $this->productRelation->getMainTable(),
            ['parent_id']
        )->where(
            'child_id = ?',
            $product->getId()
        );
        $parentIds = $this->productRelation->getConnection()->fetchCol($select);
        $parents = [];

        foreach ($parentIds as $parentId) {
            if (in_array($magentoEdition, ['Enterprise', 'B2B'])) {
                $select = $this->productRelation->getConnection()->select()->from(
                    $this->resource->getTableName('catalog_product_entity'),
                    ['entity_id']
                )->where(
                    'row_id = ?',
                    $parentId
                );
                $parentId = $this->productRelation->getConnection()->fetchCol($select);
            }

            $parents[] = $this->productFactory->create()->load($parentId);
        }

        return $parents;
    }

    public function getOnlyParent(Product $product): ?Product
    {
        $parent = $this->getParent($product);
        if ($parent && $parent->getId() != $product->getId()) {
            return $parent;
        }

        return null;
    }

    /**
     * For simple products
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAssociatedProducts(Product $product): array
    {
        return [];
    }

    public function getRelatedProducts(Product $product): array
    {
        return $product->getRelatedProducts();
    }

    public function getCrossSellProducts(Product $product): array
    {
        return $product->getCrossSellProducts();
    }

    public function getUpSellProducts(Product $product): array
    {
        return $product->getUpSellProducts();
    }

    public function toString($value, string $key = null): string
    {
        if (!$key && $value instanceof Product) {
            return $value->getName();
        }

        return parent::toString($value, $key);
    }

    public function getMapping(Product $product, array $args): string
    {

        $mappingId = $args[0];

        /** @var \Mirasvit\Feed\Model\Dynamic\Category $mapping */
        $mapping = $this->getMappingData($mappingId);

        $category = $this->getCategory($product);

        return $category ? $mapping->getMappingValue((int)$category->getId()) : $mapping->getMappingValue(0);
    }

    private function getMappingData(string $id)
    {
        if (!isset(self::$mappings[$id])) {
            self::$mappings[$id] = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Category')->load($id);
        }

        return self::$mappings[$id];
    }

    public function getInventory(Product $product, array $args): int
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $quantity = 0;
            $code     = $args[0];

            /** @var GetSourceItemsBySkuInterface $sourceItemsBySku */
            $sourceItemsBySku = $this->objectManager->create('\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface')
                ->execute($product->getSku());

            foreach ($sourceItemsBySku as $sourceItem) {
                if ($sourceItem->getData('source_code') == $code) {
                    $quantity = $sourceItem->getQuantity();
                }
            }

            return (int)$quantity;
        }

        return 0;
    }

    public function getMsiStock(Product $product, array $args): int
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $quantity = 0;
            $code     = $args[0];

            if ($product->getTypeId() == Type::TYPE_SIMPLE) {
                /** @var GetProductSalableQtyInterface $quantity */
                $quantity = $this->objectManager->get('\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface')
                    ->execute($product->getSku(), (int)$code);
            }

            return (int)$quantity;
        }

        return 0;
    }

    public function getDynamic(Product $product, array $args): ?string
    {
        $code = $args[0];

        /** @var Attribute $attribute */
        $attribute = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Attribute')->load($code, 'code');

        if ($attribute) {
            return $attribute->getValue($product, $this);
        }

        return null;
    }

    public function getVariable(Product $product, array $args): string
    {
        $code = $args[0];

        /** @var Variable $variable */
        $variable = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Variable')->load($code, 'code');

        if ($variable) {
            return $variable->getValue($product, $this);
        }

        return '';
    }

    public function getMappings(Product $product, array $args): array
    {
        $mappingId = $args[0];

        /** @var \Mirasvit\Feed\Model\Dynamic\Category $mapping */
        $mapping = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Category')->load($mappingId);

        $result = [];
        foreach ($product->getCategoryCollection() as $category) {
            $result[] = $mapping->getMappingValue((int)$category->getId());
        }

        return $result;
    }

    public function getStoreId(): int
    {
        return $this->getFeed() ? (int)$this->getFeed()->getStore()->getId() : 0;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getData(Product $object, string $key)
    {
        $result = false;

        foreach ($this->resolvers as $resolver) {
            $resolver->setFeed($this->getFeed());
            $result = $resolver->resolve($object, $key);
            if ($result !== false) {

                return $result;
            }
        }

        $product = $this->getProduct($object);

        $exploded = explode(':', $key);

        $key      = $exploded[0];
        $modifier = count($exploded) == 2 ? $exploded[1] : "";

        $attribute = $this->getAttribute($key);

        if ($attribute && in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
            if (is_scalar($product->getData($key))) {
                if ($modifier == 'swatch') {
                    $value = $this->swatchesHelper->getSwatchesByOptionsId([$product->getData($key)]);
                    if ($value) {
                        $result = current($value)['value'] . '';
                    }
                } else {
                    $value = $product->getResource()
                        ->getAttribute($key)
                        ->getSource()
                        ->getOptionText($product->getData($key));

                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    $result = $value . '';
                }
            }
        } else {
            $result = $product->getDataUsingMethod($key);

            if (!$result) {
                $result = $product->getData($key);
            }
        }

        return $result;
    }

    protected function getAttribute(string $code): ?EntityAttribute
    {
        if (self::$attributes == null) {
            $entityTypeId = $this->objectManager->get('Magento\Eav\Model\Entity')
                ->setType(Product::ENTITY)->getTypeId();

            self::$attributes = $this->objectManager
                ->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
                ->setEntityTypeFilter($entityTypeId);
        }

        $attribute = self::$attributes->getItemByColumnValue('attribute_code', $code);

        return $attribute;
    }

    protected function getProduct(Product $object): Product
    {
        $hash = $this->getStoreId() . $object->getId();

        if (!isset(self::$products[$hash])) {
            if (count(self::$products) > 1000) {
                self::$products = [];
            }
            self::$products[$hash] = $object->load($object->getId());
        }

        return self::$products[$hash];
    }

    protected function prepareObject(Product $object): Product
    {
        return $this->getProduct($object);
    }

    public function getCategory(Product $product): ?Category
    {
        $rootCategory = $this->getFeed() ? $this->getFeed()->getStore()->getRootCategoryId() : 0;
        $collection   = $product->getCategoryCollection()
            ->addOrder('level')
            ->addFieldToFilter('path', [
                ['like' => '1/' . $rootCategory . '/%'],
                ['eq' => '1/' . $rootCategory]
            ]);
        $maximumLevel = $collection->getFirstItem()->getLevel();

        // get category with maximum level and lowest position
        $category        = null;
        $currentPosition = null;

        /** @var Category $cat */
        foreach ($collection as $cat) {
            if (
                (str_contains($cat->getPath(), '/' . $rootCategory . '/') || $cat->getPath() === '1/' . $rootCategory)
                && $cat->getLevel() == $maximumLevel
                && ($currentPosition === null || $cat->getPosition() < $currentPosition)
            ) {
                $category        = $cat;
                $currentPosition = $cat->getPosition();
            }
        }

        return $category;
    }

    public function getCategoryCollection(Product $product): array
    {
        $result     = [];
        $collection = $product->getCategoryCollection();

        /** @var Category $category */
        foreach ($collection as $category) {
            if ($category->isInRootCategoryList()) {
                $result[] = $category;
            }
        }

        return $result;
    }
}
