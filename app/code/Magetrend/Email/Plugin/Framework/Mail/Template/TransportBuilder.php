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

    public function __construct(
        \Magetrend\Email\Model\TemplateVarManager $templateVarManager,
        \Magetrend\Email\Helper\Vars $varsHelper
    ) {
        $this->templateVarManager = $templateVarManager;
        $this->varsHelper = $varsHelper;
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

    public function addAdditionalVariables($vars)
    {
        if (isset($vars['store']) && !empty($vars['store'])) {
            $store = $vars['store'];
            $vars['store_url'] = $store->getBaseUrl();
        }

        return $vars;
    }
}
