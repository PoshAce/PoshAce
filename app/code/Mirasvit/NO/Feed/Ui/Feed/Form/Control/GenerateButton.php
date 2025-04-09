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

namespace Mirasvit\Feed\Ui\Feed\Form\Control;

class GenerateButton extends AbstractButton
{
    public function getButtonData(): array
    {
        return [
            'label'      => (string)__('Generate'),
            'class'      => 'generate',
            'on_click'   => "require('uiRegistry').get('feed_export').generate()",
            'sort_order' => -70,
        ];
    }
}
