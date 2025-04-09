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

class Pool
{
    protected $resolvers;

    public function __construct(
        array $resolvers = []
    ) {
        $this->resolvers = $resolvers;
    }


    public function findResolver(object $object): ?AbstractResolver
    {
        foreach ($this->resolvers as $resolver) {
            if ($object instanceof $resolver['for']) {
                if (!isset($resolver['type_id'])
                    || $object->getData('type_id') == $resolver['type_id']) {
                    return $resolver['resolver'];
                }
            }
        }

        return null;
    }

    public function getResolvers(): array
    {
        $list = [];

        foreach ($this->resolvers as $resolver) {
            $list[] = $resolver['resolver'];
        }

        return $list;
    }
}
