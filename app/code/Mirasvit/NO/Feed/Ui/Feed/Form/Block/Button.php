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

class Button extends Form
{
    protected $formFactory;

    protected $_nameInLayout = "Mirasvit_Feed::html_ftp_button";

    public function __construct(
        FormFactory $formFactory,
        Context     $context
    ) {
        $this->formFactory = $formFactory;
        $this->_template   = 'Mirasvit_Feed::feed/ftp/button.phtml';

        parent::__construct($context);
    }

    protected function _prepareForm(): Form
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        return parent::_prepareForm();
    }

}

