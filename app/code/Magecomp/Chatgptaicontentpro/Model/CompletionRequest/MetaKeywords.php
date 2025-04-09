<?php


namespace Magecomp\Chatgptaicontentpro\Model\CompletionRequest;

use Magecomp\Chatgptaicontentpro\Api\CompletionRequestInterface;
use Magecomp\Chatgptaicontentpro\Model\Config;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;


class MetaKeywords extends AbstractCompletion implements CompletionRequestInterface
{
    public const TYPE = 'meta_keywords';
    protected const CUT_RESULT_PREFIX = 'Meta Keywords: ';
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
            'attribute_label' => 'Meta Keywords',
            'container' => 'product_form.product_form.search-engine-optimization.container_meta_keyword',
            'prompt_from' => 'product_form.product_form.content.container_description.description',
            'target_field' => 'product_form.product_form.search-engine-optimization.container_meta_keyword.meta_keyword',
            'component' => 'Magecomp_Chatgptaicontentpro/js/button',
        ];
    }

    public function getApiPayload(string $text): array
    {
        parent::validateRequest($text);

        $storeid = $this->request->getParam('storeid');

        $languages = $this->languages->getLanguage($storeid);
    
         if(str_contains($text,"categorypage")){
             $test=str_replace(',categorypage', '', $text);
             $configprompt= "Create meta keywords (only content) of the following category without double inverted commas:, $test .'generate in '.$languages.' language'";
         }else if(str_contains($text,"cmspage")){
            $test=str_replace(',cmspage', '', $text);
             $configprompt= "Create meta keywords (only content) of the following cms page without double inverted commas:, $test .'generate in '.$languages.' language'";
         }else{
            $configprompt= "Create meta keywords (only content) of the following product without double inverted commas:, $text .'generate in '.$languages.' language'";
         }
        $model = $this->languages->getChatgptModel($storeid);
        $payload =  [
            "model" => $model,
            "n" => 1,
            "temperature" => 0.5,
            "max_tokens" => 100,
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
