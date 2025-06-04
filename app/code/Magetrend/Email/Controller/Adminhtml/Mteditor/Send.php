<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Controller\Adminhtml\Mteditor;

class Send extends \Magetrend\Email\Controller\Adminhtml\Mteditor
{
    public $_emailConfig = null;

    public function execute()
    {
        $templateId = $this->getRequest()->getParam('id');
        $content = $this->getRequest()->getParam('content');
        $vars = $this->getRequest()->getParam('vars');
        $email = $this->getRequest()->getParam('email');
        $css = $this->getRequest()->getParam('css');
        $template = $this->_initTemplate('id');


$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

         $logger->info('----------------- get templateId --------------- ');
        $logger->info(print_r($templateId,true)); 

        
        $logger->info('----------------- get vars --------------- ');
        $logger->info(print_r($vars,true)); 

$logger->info('----------------- get email --------------- ');
        $logger->info(print_r($email,true)); 


$logger->info('----------------- get css --------------- ');
        $logger->info(print_r($css,true)); 


$logger->info('----------------- get template --------------- ');
        $logger->info(print_r($template,true)); 


        $vars = $this->jsonHelper->jsonDecode($vars);
        $css = $this->jsonHelper->jsonDecode($css);
        $content = $this->jsonHelper->jsonDecode($content);

        if (!$this->_coreRegistry->registry('mt_editor_preview_mode')) {
            $this->_coreRegistry->register('mt_editor_preview_mode', 1);
        }

        if (!$template->getId() && $templateId) {
            return $this->_error(__('This Email template no longer exists.'));
        } else {
            try {
                $this->_objectManager->get('Magetrend\Email\Model\Template')
                    ->sendTestEmail($email, $template, $content, $vars, $css);

                return $this->_jsonResponse([
                    'success' => 1,
                ]);
            } catch (\Exception $e) {
                return $this->_error($e->getMessage());
            }
        }
    }
}
