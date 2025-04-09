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
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\Config;

class Data extends AbstractHelper
{

    protected $storeManager;

    protected $backendUrl;

    private   $config;

    public function __construct(
        StoreManagerInterface $storeManager,
        BackendUrlInterface   $backendUrl,
        Config                $config,
        Context               $context
    ) {
        $this->storeManager = $storeManager;
        $this->backendUrl   = $backendUrl;
        $this->config       = $config;

        parent::__construct($context);
    }

    public function getFeedDeliverUrl(Feed $feed): string
    {
        return $this->backendUrl->getUrl('*/*/delivery', ['id' => $feed->getId()]);
    }

    public function getFeedPreviewUrl(Feed $feed): string
    {
        return $this->backendUrl->getUrl(
            '*/*/preview',
            [
                'id'   => $feed->getId(),
                'skip' => 'rules',
            ]
        );
    }

    public function getFeedExportUrl(): string
    {
        $url = $this->backendUrl->getUrl('*/feed/execute');

        $url = strtok($url, '?');

        return $url;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFeedProgressUrl(Feed $feed): string
    {
        $stateUrl = $this->backendUrl->getUrl('*/*/progress');
        $stateUrl = strtok($stateUrl, '?');

        return $stateUrl;
    }

    public function getEntityConfigPath(object $entity, string $entityName): string
    {
        $methodName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $entityName))) . 'Path';
        $path       = $this->config->$methodName() . '/' . $entity->getName() . '.yaml';

        return $path;
    }

    public function removeJS(array $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = preg_replace('#<script(.*?)>(.*?)</script>#is', ' ', $value);
                }
            }

            return $data;
        } else {
            return preg_replace('#<script(.*?)>(.*?)</script>#is', ' ', $data);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function processDeleteFlags(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->processDeleteFlags($value);
            }

            if (isset($value['delete-flag'])) {
                if ($value['delete-flag']) {
                    unset($data[$key]);
                    continue;
                } else {
                    unset($data[$key]['delete-flag']);
                }
            }

            if (isset($value['arg-count'])) {
                if (isset($value['args']) && is_numeric($value['arg-count'])) {
                    $count = (int)$value['arg-count'];

                    if ($count > 0) {
                        $data[$key]['args'] = array_slice($value['args'], 0, $count);
                    } elseif ($count === 0) {
                        unset($data[$key]['args']);
                    }
                }

                unset($data[$key]['arg-count']);
            }
        }

        return $this->resetNumericKeys($data);
    }

    private function resetNumericKeys(array $data): array
    {
        $isNumeric = false;

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->resetNumericKeys($value);
            }

            if (is_numeric($key)) {
                $isNumeric = true;
            }
        }

        return $isNumeric ? array_values($data) : $data;
    }
}
