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

namespace Mirasvit\Feed\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\Store;
use Mirasvit\Core\Helper\Io;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Feed\Service\Serialize;

/**
 * Feed Model
 * @method string getName()
 * @method $this setName($value)
 * @method $this setFilename($value)
 * @method $this setCreatedAt($value)
 * @method $this setUpdatedAt($value)
 * @method int getStoreId()
 * @method string getArchivation()
 * @method bool getIsActive()
 * @method bool getFtp()
 * @method string getFtpProtocol()
 * @method string getFtpHost()
 * @method string getFtpUser()
 * @method string getFtpPassword()
 * @method string getFtpPath()
 * @method string getFtpPassiveMode()
 * @method string getGeneratedAt()
 * @method $this setGeneratedAt($value)
 * @method $this setGeneratedCnt($value)
 * @method $this setGeneratedTime($value)
 * @method $this setUploadedAt($value)
 * @method $this setRuleIds($value)
 * @method string getGaSource()
 * @method string getGaMedium()
 * @method string getGaName()
 * @method string getGaTerm()
 * @method string getGaContent()
 * @method bool getReportEnabled()
 * @method string getNotificationEmails()
 */
class Feed extends AbstractTemplate
{
    protected $storeManager;

    protected $store;

    protected $storeFactory;

    protected $config;

    protected $urlManager;

    protected $serializer;

    protected $io;

    public function __construct(
        StoreManagerInterface $storeManager,
        StoreFactory $storeFactory,
        Config       $config,
        UrlInterface $urlManager,
        Context      $context,
        Registry     $registry,
        Serialize    $serializer,
        Io           $io
    ) {
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
        $this->config       = $config;
        $this->urlManager   = $urlManager;
        $this->serializer   = $serializer;
        $this->io           = $io;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\ResourceModel\Feed');
    }

    public function getStore(): Store
    {
        if (!$this->store) {
            if ($this->getStoreId() !== null) {
                $this->store = $this->storeFactory->create()->load($this->getStoreId());
            } else {
                $this->store = $this->storeManager->getStore();
            }
        }

        return $this->store;
    }

    public function getRuleIds(): array
    {
        return is_array($this->getData('rule_ids')) ? $this->getData('rule_ids') : [];
    }

    public function getNotificationEvents(): array
    {
        if (!is_array($this->getData('notification_events'))) {
            $this->setData('notification_events', explode(',', (string)$this->getData('notification_events')));
        }

        return $this->getData('notification_events');
    }

    public function getCronDay(): array
    {
        if (!is_array($this->getData('cron_day'))) {
            $this->setData('cron_day', explode(',', (string)$this->getData('cron_day')));
        }

        return $this->getData('cron_day');
    }

    public function getCronTime(): array
    {
        if (!is_array($this->getData('cron_time'))) {
            $this->setData('cron_time', explode(',', (string)$this->getData('cron_time')));
        }

        return $this->getData('cron_time');
    }

    public function getUrl(): ?string
    {
        $url = null;

        $filename = $this->getFilename();

        if ($this->getArchivation()) {
            $filename .= '.' . $this->getArchivation();
        }

        $path = $this->config->getBasePath() . DIRECTORY_SEPARATOR . $filename;

        if ($this->io->fileExists($path)) {
            if ($this->io->isRemoteStorageEnabled()) {
                $url = $this->io->getRealPath($path);
            } else {
                $url = $this->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) . DirectoryList::MEDIA . '/feed/' . $filename;
            }
        }

        return $url;
    }

    public function getType(): string
    {
        $type = $this->getData('type');

        if (in_array($type, ['xml', 'csv', 'txt'])) {
            return $type;
        }

        return 'txt';
    }

    public function getFilename(): string
    {
        return $this->getData('filename') . '.' . strtolower($this->getType());
    }

    public function getPreviewFilename(): string
    {
        return $this->getData('filename') . '.test' . '.' . strtolower($this->getType());
    }

    public function getFilePath(): string
    {
        return $this->config->getBasePath() . DIRECTORY_SEPARATOR . $this->getFilename();
    }

    public function getPreviewFilePath(): string
    {
        return $this->config->getBasePath() . DIRECTORY_SEPARATOR . $this->getPreviewFilename();
    }

    public function loadFromTemplate(Template $template): self
    {
        $this->addData($template->getData());

        return $this;
    }

    public function getFbMetadataEnabled()
    {
        return $this->getData('fb_metadata_enabled');
    }

    public function getFilterFastmodeEnabled()
    {
        return $this->getData('filter_fastmode_enabled');
    }

    public function isFeedAvailable(): bool
    {
        return $this->io->fileExists($this->getFilePath());
    }
}
