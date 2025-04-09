<?php
namespace Magecomp\Chatgptaicontentpro\Model\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Modeltype extends AbstractSource
{
    public function getAllOptions()
    {
        return [
            ['value' => 'gpt-4', 'label' => 'gpt-4'],
            ['value' => 'gpt-4-0613', 'label' => 'gpt-4-0613'],
            ['value' => 'gpt-4-32k', 'label' => 'gpt-4-32k'],
            ['value' => 'gpt-4-32k-0613', 'label' => 'gpt-4-32k-0613'],
            ['value' => 'gpt-3.5-turbo', 'label' => 'gpt-3.5-turbo'],
            ['value' => 'gpt-3.5-turbo-1106', 'label' => 'gpt-3.5-turbo-1106'],
            ['value' => 'gpt-3.5-turbo-16k', 'label' => 'gpt-3.5-turbo-16k'],
            ['value' => 'gpt-3.5-turbo-16k-0613', 'label' => 'gpt-3.5-turbo-16k-0613'],
            ['value' => 'gpt-3.5-turbo-1106', 'label' => 'gpt-3.5-turbo-1106'],
        ];
    }
}
