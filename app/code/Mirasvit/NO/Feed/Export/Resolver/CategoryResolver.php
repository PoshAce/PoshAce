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

use Magento\Catalog\Model\Category;

class CategoryResolver extends AbstractResolver
{

    protected static $categories = [];

    public function getAttributes(): array
    {
        return [];
    }

    public function getParentCategory(Category $category): Category
    {
        return $category->getParentCategory();
    }

    public function getPath(Category $category): string
    {
        $value = [];
        foreach ($category->getParentCategories() as $parent) {
            $value[$parent->getLevel()] = $parent->getName();
        }
        if (!count($value)) {
            $value[$category->getLevel()] = $category->getName();
        }

        ksort($value);

        return implode(" > ", $value);
    }

    public function toString($value, string $key = null): string
    {
        if (is_object($value) && $value instanceof Category) {
            return $value->getName();
        }

        return parent::toString($value, $key);
    }

    /**
     * @param Category $object
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function resolve($object, ?string $key, array $args = [])
    {
        $category = $this->getCategory($object);
        $result   = parent::resolve($category, $key, $args);

        if ($result === false) {
            $result = $category->getData($key);
        }

        return $result;
    }

    protected function getCategory(Category $object): Category
    {
        if (!isset(self::$categories[$object->getId()])) {
            self::$categories[$object->getId()] = $object->load($object->getId());
        }

        return self::$categories[$object->getId()];
    }
}
