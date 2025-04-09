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

namespace Mirasvit\Feed\Validator;


class RecommendedValueValidator implements ValidatorInterface
{
    const CODE = 'recommended';
    const NAME = 'Recommended Value';

    public function getCode(): string
    {
        return self::CODE;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function isValid($value): bool
    {
        return !empty($value) || is_numeric($value);
    }

    public function getMessage(bool $isHtml = false): string
    {
        $message = (string)__('Missing recommended attribute');

        if ($isHtml) {
            $message = '<span class="grid-severity-major grid-severity-optional"><span>' . $message . '</span></span>';
        }

        return $message;
    }

    public function getHint(string $attribute = ''): string
    {
        return (string)__("This attribute value is recommended. "
            . "While you can leave it empty, it is still recommended that you fill in the value for this attribute. "
            . "To fix this error, open invalid products and fill in a value for this attribute "
            . "or change an attribute/pattern used for this field in the product feed itself."
        );
    }
}
