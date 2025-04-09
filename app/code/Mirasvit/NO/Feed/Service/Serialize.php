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

namespace Mirasvit\Feed\Service;

use Exception;
use InvalidArgumentException;
use Magento\Framework\Serialize\Serializer\Json;
use Mirasvit\Core\Service\CompatibilityService;

class Serialize
{
    private $serializer;

    public function __construct(
        Json                $serializer
    ) {
        $this->serializer         = $serializer;
    }

    /**
     * @param array|mixed $data
     */
    public function serialize($data): string
    {
        if (is_resource($data)) {
            throw new InvalidArgumentException('Unable to serialize value.');
        }

        return $this->serializer->serialize($data);
    }

    public function unserialize(string $string): array
    {
        if ('[]' == $string || false === $string || null === $string || '' === $string) {
            return [];
        }
        try {
            $result = $this->serializer->unserialize($string);
        } catch (Exception $e) {
            /** mp comment start **/
            $result = unserialize($string);
            /** mp comment end **/
            /** mp uncomment start
            return [];
            mp uncomment end **/
        }

        return is_array($result) ? $result : [];
    }
}
