<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Plugin\Framework\Mail\Template;

class TransportBuilder
{
    public $templateVarManager;

    public $varsHelper;

    public $moduleHelper;

    public $templateFactory;

    public $templateIdentifier = null;

    public function __construct(
        \Magetrend\Email\Model\TemplateVarManager $templateVarManager,
        \Magetrend\Email\Helper\Vars $varsHelper,
        \Magetrend\Email\Helper\Data $moduleHelper,
        \Magento\Email\Model\TemplateFactory $templateFactory
    ) {
        $this->templateVarManager = $templateVarManager;
        $this->varsHelper = $varsHelper;
        $this->moduleHelper = $moduleHelper;
        $this->templateFactory = $templateFactory;
    }

    public function beforeSetTemplateVars($subject, $vars)
    {
        if (!isset($vars['mtVar'])) {
            $this->templateVarManager->reset();
            $this->templateVarManager->setVariables($vars);
            $vars['mtVar'] = $this->templateVarManager;
        }

        $vars = $this->addAdditionalVariables($vars);
        $this->varsHelper->varRegister = $vars;

        return [$vars];
    }

    public function beforeSetTemplateIdentifier($subject, $templateId)
    {
        $this->templateIdentifier = $templateId;
        return [$templateId];
    }

    public function beforeSetTemplateOptions($subject, $templateOptions)
    {
        if (!$this->moduleHelper->isActive() || !is_numeric($this->templateIdentifier)) {
            return [$templateOptions];
        }

        $template = $this->templateFactory->create()
            ->load($this->templateIdentifier);

        if (!$template || !$template->getId()) {
            return [$templateOptions];
        }

        if ($template->getIsMtEmail() != 1) {
            return [$templateOptions];
        }

        if (isset($templateOptions['area'])) {
            $templateOptions['area'] = 'frontend';
        }

        return [$templateOptions];
    }

    public function addAdditionalVariables($vars)
    {
        if (isset($vars['store']) && !empty($vars['store'])) {
            $store = $vars['store'];
            $vars['store_url'] = $store->getBaseUrl();
        }

        return $vars;
    }
}
