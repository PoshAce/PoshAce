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

namespace Mirasvit\Feed\Ui\Template\Form\Block\Schema;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\Template;
use Mirasvit\Feed\Model\TemplateFactory;

class FormCsv extends Form
{
    protected $_nameInLayout = 'Mirasvit_Feed::template_content_csv';

    protected $elementCsv;

    protected $formFactory;

    protected $registry;

    public function __construct(
        FormFactory $formFactory,
        Registry    $registry,
        Context     $context
    ) {
        $this->formFactory = $formFactory;
        $this->registry    = $registry;
        $this->_template   = 'Mirasvit_Feed::template/edit/tab/schema/csv.phtml';
        parent::__construct($context);
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create();

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getJsConfig(): array
    {

        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'schema_csv' => [
                            'component' => 'Mirasvit_Feed/js/edit/tab/schema/csv',
                            'config'    => [
                                'rows' => $this->getModel()->getCsvSchema(),
                                'form' => 'mst_feed_template_form'
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getModel(): ?Template
    {
        return $this->registry->registry('current_model');
    }

}
