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

namespace Mirasvit\Feed\Block\Adminhtml\Feed\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\Feed\Exporter;


class Status extends AbstractRenderer
{
    protected $exporter;

    public function __construct(
        Context  $context,
        Exporter $exporter
    ) {
        $this->exporter = $exporter;

        parent::__construct($context);
    }

    public function render(DataObject $feed)
    {
        /** @var Feed $feed */
        $html = '';

        $handler = $this->exporter->getHandler($feed);
        if ($feed->getIsActive()) {
            if (in_array($handler->getStatus(), [Config::STATUS_COMPLETED, Config::STATUS_READY])) {
                if ($feed->getUrl()) {
                    $html = $this->getStatusHtml('notice', 'Ready');
                } else {
                    $html = $this->getStatusHtml('critical', 'Not generated');
                }
            } elseif ($handler->getStatus() == Config::STATUS_PROCESSING) {
                $html = $this->getStatusHtml('major', 'Processing', '');
            }
        } else {
            $html = $this->getStatusHtml('minor', 'Disabled');
        }

        return $html;
    }

    protected function getStatusHtml(string $severity, string $title, string $message = null): string
    {
        $html = '';
        $html .= sprintf('<span class="grid-severity status %s"><span>%s</span></span>', $severity, __($title));

        if ($message) {
            $html .= '<div class="state-message">' . $message . '</div>';
        }

        return $html;
    }
}
