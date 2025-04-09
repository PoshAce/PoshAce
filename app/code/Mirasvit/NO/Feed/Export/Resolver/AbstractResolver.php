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

use Exception;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Model\Feed;

abstract class AbstractResolver
{

    protected $objectManager;

    protected $context;

    protected $storeManager;

    protected $filesystem;

    public function __construct(
        Context                $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->context       = $context;
        $this->storeManager  = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->filesystem    = $this->objectManager->get('Magento\Framework\Filesystem');
    }

    abstract public function getAttributes(): array;

    /**
     * @param object $object
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function resolve($object, ?string $key, array $args = [])
    {
        $this->context->setCurrentObject($object);

        /** @var Pool $pool */
        $pool = $this->objectManager->get('Mirasvit\Feed\Export\Resolver\Pool');

        $resolver = $pool->findResolver($object);

        if ($resolver && !($resolver instanceof $this)) {
            return $resolver->resolve($object, $key, $args);
        } elseif ($resolver instanceof $this && !$key) {
            return $resolver->toString($object);
        } else {
            $exploded = explode(':', $key);

            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $exploded[0])));
            for ($i = 1; $i < count($exploded); $i++) {
                $args[] = $exploded[$i];
            }

            if (method_exists($this, 'prepareObject')) {
                $object = $this->{'prepareObject'}($object);
            }

            $result = false;
            if (method_exists($this, $method)) {
                $result = $this->{$method}($object, $args);
            } elseif (method_exists($this, 'getData')) {
                $result = $this->getData($object, $key);
            } elseif (method_exists($object, $method)) {
                $result = $object->{$method}();
            } elseif (method_exists($object, 'getData')) {
                $result = $object->getData($exploded[0]);
            }

            return $result;
        }

    }

    /**
     * Return string value of object
     *
     * @param object|array|string $value
     */
    public function toString($value, string $key = null): string
    {
        if (!$key && is_object($value)) {
            return get_class($value);
        }

        if (is_array($value)) {
            # is multi-dimension array
            if (isset($value[0]) && is_array($value[0])) {
                return json_encode($value);
            } else {
                try {
                    return implode(', ', $value);
                } catch (Exception $e) {
                    return json_encode($value);
                }
            }
        }

        return $value;
    }

    public function getFeed(): ?Feed
    {
        return $this->context->getFeed();
    }
}
