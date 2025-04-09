<?php

namespace Magecomp\Chatgptaicontentpro\Model;

use Magecomp\Chatgptaicontentpro\Api\CompletionRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magecomp\Chatgptaicontentpro\Helper\Data;

class CompletionConfig
{
    /**
     * @var CompletionRequestInterface[]
     */
    private array $pool;
    private Config $config;
    private  $helperdata;
    private RequestInterface $request;

    public function __construct(
        array $pool,
        Config $config,
        Data $helperdata,
        RequestInterface $request
    ) {
        $this->pool = $pool;
        $this->config = $config;
        $this->helperdata = $helperdata;
        $this->request = $request;
    }

    public function getConfig(): array
    {

         $storeid = $this->request->getParam('storeid');
        if (!$this->helperdata->isEnabled($storeid)) {
            return [
                'targets' => []
            ];
        }


        $targets = [];

        foreach ($this->pool as $config) {

            $targets[$config->getType()] = $config->getJsConfig();
            
        }

        $targets = array_filter($targets);

        return [
            'targets' => $targets
        ];
    }

    public function getByType(string $type): ?CompletionRequestInterface
    {
         
        foreach ($this->pool as $config) {  
            if ($config->getType() === $type) {
                return $config;
            }
        }
        return null;
    }
}
