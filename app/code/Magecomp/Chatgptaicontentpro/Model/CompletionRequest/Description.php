<?php
namespace Magecomp\Chatgptaicontentpro\Model\CompletionRequest;

use Magecomp\Chatgptaicontentpro\Api\CompletionRequestInterface;
use Magecomp\Chatgptaicontentpro\Model\Config;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;

class Description extends AbstractCompletion implements CompletionRequestInterface
{
    public const TYPE = 'description';
    protected const CUT_RESULT_PREFIX = 'Description: ';
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
            'attribute_label' => 'Description',
            'container' => 'product_form.product_form.content.container_description',
            'prompt_from' => 'product_form.product_form.content.container_description.description',
            'target_field' => 'product_form.product_form.content.container_description.description',
            'component' => 'Magecomp_Chatgptaicontentpro/js/wysiwyg/button',
        ];
    }

    public function getApiPayload(string $text): array
    {  
        $storeid = $this->request->getParam('storeid');
        $discLenValue = (int)$this->languages->getDisLen($storeid);
         
        $languages = $this->languages->getLanguage($storeid);
        if($discLenValue== null){
           $discLenValue = 800;
        }
        parent::validateRequest($text);
         $promp=explode(",",$text);
         $discprom = (string)$this->languages->getDisPromt($storeid);

         if(str_contains($text,"categorypage")){
             $test=str_replace(',categorypage', '', $text);
             $configprompt= "Please provide a category page description for:, $test";
         }
          if(str_contains($text,"cmspage")){
             $test=str_replace(',cmspage', '', $text);
             $configprompt= "Please provide a cms page description for:, $test";
         }

      if ($discprom==null || str_contains($discprom,"[Product Name]")==null || str_contains($discprom,"[Product Category]")==null){
           $configprompt= "Please provide a product description for:, $text";
        }
    else{
        $disPromot =["[Product Name]","[Product Category]"];
        if(isset($promp[1])){
        $disPromotarr =["$promp[0]","$promp[1]"];
        $configprompt=str_replace($disPromot,$disPromotarr, $discprom);
        }else{
                 $configprompt= "Please provide a product description for:, $text";
            }
        }

        $model = $this->languages->getChatgptModel($storeid);
        $payload =  [
            "model" => $model,
            "n" => 1,
            "temperature" => 0.5,
            "max_tokens" => $discLenValue,
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
