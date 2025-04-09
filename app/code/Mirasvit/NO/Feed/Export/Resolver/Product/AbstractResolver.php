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

namespace Mirasvit\Feed\Export\Resolver\Product;

use Mirasvit\Feed\Model\Feed;

abstract class AbstractResolver
{

    private $feed;

    public function setFeed(?Feed $feed): self
    {
        $this->feed = $feed;

        return $this;
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function resolve(object $object, string $key)
    {
        $exploded = explode(':', $key);

        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $exploded[0])));
        $args   = [];
        for ($i = 1; $i < count($exploded); $i++) {
            $args[] = $exploded[$i];
        }

        if (method_exists($this, 'prepareObject')) {
            $object = $this->{'prepareObject'}($object);
        }

        if (method_exists($this, $method)) {
            return $this->{$method}($object, $args);
        }

        if (method_exists($this, 'getData')) {
            return $this->getData($object, $key);
        }

        return false;
    }
}
