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

namespace Mirasvit\Feed\Ui\Feed\Form\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\Core\Api\Service\CronServiceInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterfaceFactory;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;

class Cron extends Form
{
    protected $_nameInLayout = "Mirasvit_Feed::html_cron";

    protected $formFactory;

    private   $scheduleCollectionFactory;

    protected $cronService;

    protected $timezoneFactory;

    public function __construct(
        FormFactory               $formFactory,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        CronServiceInterface      $cronService,
        TimezoneInterfaceFactory  $timezoneFactory,
        Context                   $context
    ) {
        $this->formFactory               = $formFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->cronService               = $cronService;
        $this->timezoneFactory           = $timezoneFactory;
        $this->_template                 = 'Mirasvit_Feed::feed/cron/info.phtml';

        parent::__construct($context);
    }

    protected function _prepareForm(): Form
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTimeNow(): string
    {
        return $this->timezoneFactory->create()->date()->format('h:i A');
    }

    public function getCronStatus(): string
    {
        return $this->cronService->isCronRunning(['feed_export']) ? 'OK' : 'Cron is not running';
    }

    public function getLastFeedCron(): string
    {
        $lastFeedCron = '-';
        $cron         = $this->scheduleCollectionFactory->create();
        $cron->addFieldToFilter('job_code', 'feed_export')
            ->addFieldToFilter('status', 'success')
            ->setOrder('executed_at', 'desc')
            ->getFirstItem()
            ->setPageSize(1);

        if ($cron->getSize()) {
            $timezone     = $this->timezoneFactory->create();
            $lastFeedCron = $timezone->date($cron->fetchItem()->getExecutedAt())->format('d.m.Y h:i A');
        }

        return $lastFeedCron;
    }
}

