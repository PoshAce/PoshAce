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

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\Feed\Helper\Data as FeedHelper;
use Mirasvit\Feed\Model\Feed;

class Export extends Form
{
    protected $_nameInLayout = "Mirasvit_Feed::html_export";

    protected $registry;

    protected $dataHelper;

    protected $formFactory;

    public function __construct(
        FormFactory $formFactory,
        Registry    $registry,
        Context     $context,
        FeedHelper  $dataHelper
    ) {
        $this->formFactory = $formFactory;
        $this->registry    = $registry;
        $this->dataHelper  = $dataHelper;
        $this->_template   = 'Mirasvit_Feed::feed/export.phtml';
        parent::__construct($context);
    }

    protected function _prepareForm(): Form
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getJsConfig(): array
    {
        $exportUrl   = $this->dataHelper->getFeedExportUrl();
        $progressUrl = $this->dataHelper->getFeedProgressUrl($this->getFeed());

        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'feed_export' => [
                            'component' => 'Mirasvit_Feed/js/feed/export',
                            'config'    => [
                                'exportUrl' => $exportUrl,
                                'id'        => $this->getFeed()->getId(),
                            ],

                            'children' => [
                                'progress' => [
                                    'component' => 'Mirasvit_Feed/js/feed/progress',
                                    'config'    => [
                                        'url' => $progressUrl,
                                        'id'  => $this->getFeed()->getId(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getFeed(): Feed
    {
        return $this->registry->registry('current_model');
    }
}
