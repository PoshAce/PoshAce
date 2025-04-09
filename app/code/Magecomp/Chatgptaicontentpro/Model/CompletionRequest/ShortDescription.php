<?php


namespace Magecomp\Chatgptaicontentpro\Model\CompletionRequest;

use Magecomp\Chatgptaicontentpro\Api\CompletionRequestInterface;
use Magecomp\Chatgptaicontentpro\Model\Config;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ShortDescription extends AbstractCompletion implements CompletionRequestInterface
{
    public const TYPE = 'short_description';
    protected const CUT_RESULT_PREFIX = 'Short Description: ';
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
            'attribute_label' => 'Short Description',
            'container' => 'product_form.product_form.content.container_short_description',
            'prompt_from' => 'product_form.product_form.content.container_description.description',
            'target_field' => 'product_form.product_form.content.container_short_description.short_description',
            'component' => 'Magecomp_Chatgptaicontentpro/js/wysiwyg/button',
        ];
    }

    public function getApiPayload(string $text): array
    {
        
        $storeid = $this->request->getParam('storeid');
        $shortDisLenValue = (int)$this->languages->getShortDisLen($storeid);
        $languages = $this->languages->getLanguage($storeid);

        if($shortDisLenValue==null){
           $shortDisLenValue = 500;
        }

        parent::validateRequest($text);
            $promp=explode(",",$text);


            $shortdiscprom = (string)$this->languages->getShortDisPromt($storeid);
         
          if ($shortdiscprom==null || str_contains($shortdiscprom,"[Product Name]")==null || str_contains($shortdiscprom,"[Product Category]")==null){
               $configprompt= "Please provide a product short description for:, $text";
            }
        else{
            $disPromot =["[Product Name]","[Product Category]"];
            if(isset($promp[1])){
            $disPromotarr =["$promp[0]","$promp[1]"];
            $configprompt=str_replace($disPromot,$disPromotarr, $shortdiscprom);
            }else{
                 $configprompt= "Please provide a product short description for:, $text";
            }
           
          }

        $model = $this->languages->getChatgptModel($storeid);
        $payload =  [
            "model" => $model,
            "n" => 1,
            "temperature" => 0.5,
            "max_tokens" => $shortDisLenValue,
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
                    'content' => sprintf($configprompt.' in '.$languages),
                ),
            );
        } else {
            $payload['prompt'] = sprintf($configprompt.' in '.$languages);
        }
        return $payload;
    }
}
