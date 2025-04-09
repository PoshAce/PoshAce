<?php

namespace Magecomp\Chatgptaicontentpro\Model\CompletionRequest;

use Magecomp\Chatgptaicontentpro\Api\CompletionRequestInterface;
use Magecomp\Chatgptaicontentpro\Model\Config;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Alttages extends AbstractCompletion implements CompletionRequestInterface
{
    public const TYPE = 'media_gallery';
    protected const CUT_RESULT_PREFIX = 'Alt Text: ';
    protected $languages;
    protected $request;

      public function __construct(
             ScopeConfigInterface $scopeConfig,
         RequestInterface $request,
        Data $languages
    ) {
        parent::__construct($scopeConfig,$languages,$request); // Ensure the parent constructor is called
        $this->languages = $languages;
        $this->request = $request;
    }

    public function getJsConfig(): ?array
    {
        return [
            'attribute_label' => 'Alt Text',
            'container' => 'product_form.product_form.gallery.container_media_gallery',
            'prompt_from' => 'product_form.product_form.content.container_media_gallery',
            'target_field' => 'product_form.product_form.gallery.container_media_gallery.media_gallery',
            'component' => 'Magecomp_Chatgptaicontentpro/js/button',
        ];
    }

    public function getApiPayload(string $text): array
    {
        $storeid = $this->request->getParam('storeid');
        $languages = $this->languages->getLanguage($storeid);
        parent::validateRequest($text);
        $configprompt= "Create image alt text (only content) of the following product without double inverted commas:, $text .'generate in '.$languages.' language'";
       $model = $this->languages->getChatgptModel($storeid);
     
        $payload =  [
            "model" => $model,
            "n" => 1,
            "temperature" => 0.5,
            "max_tokens" => 50,
            "frequency_penalty" => 0,
            "presence_penalty" => 0
        ];
        
        if (strpos((string)$model, 'gpt') !== false) {
            $payload['messages'] = array(
                array(
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.',
                ),
                array(
                    'role' => 'user',
                    'content' => sprintf($configprompt),
                ),
            );
        } else {
            $payload['prompt'] = sprintf($configprompt);
        }
        return $payload;
    }
}
