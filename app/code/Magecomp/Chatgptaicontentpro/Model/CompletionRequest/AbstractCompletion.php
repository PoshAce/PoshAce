<?php

namespace Magecomp\Chatgptaicontentpro\Model\CompletionRequest;

use Magecomp\Chatgptaicontentpro\Model\Config;
use Magecomp\Chatgptaicontentpro\Model\OpenAI\ApiClient;
use Magecomp\Chatgptaicontentpro\Model\OpenAI\OpenAiException;
use InvalidArgumentException;
use Laminas\Json\Decoder;
use Laminas\Json\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Magecomp\Chatgptaicontentpro\Model\Normalizer;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\App\RequestInterface;

abstract class AbstractCompletion
{
    public const TYPE = '';
    protected const CUT_RESULT_PREFIX = '';
    protected ScopeConfigInterface $scopeConfig;
    protected ?ApiClient $apiClient = null;
    protected $helperdata;
     protected $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helperdata,
         RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helperdata = $helperdata;
         $this->request = $request;
    }

    abstract public function getApiPayload(string $text): array;

    private function getClient(): ApiClient
    {
        $storeid = $this->request->getParam('storeid');
        $token = $this->helperdata->getApikey($storeid);
        if (empty($token)) {
            throw new InvalidArgumentException('API token is missing');
        }
        if ($this->apiClient === null) {
            $this->apiClient = new ApiClient(
                $this->helperdata->getBaseurl($storeid),
                $this->helperdata->getApikey($storeid)
            );
        }
        return $this->apiClient;
    }

    public function getQuery(array $params): string
    {
        
        return $params['prompt'] ?? '';
    }

    /**
     * @throws OpenAiException
     */
   
      public function query(string $prompt): string
    {
        $payload = $this->getApiPayload(
            Normalizer::htmlToPlainText($prompt)
        );

        $model = $this->helperdata->getChatgptModel();
        if (strpos((string)$model, 'gpt') !== false) {
            $endpoint = '/v1/chat/completions';
        } else {
            $endpoint = '/v1/completions';
        }

        $result = $this->getClient()->post(
            $endpoint,
            $payload
        );

        $this->validateResponse($result);

        return $this->convertToResponse($result->getBody());
    }

    protected function validateRequest(string $prompt): void
    {
        if (empty($prompt) || strlen($prompt) < 4) {
            throw new InvalidArgumentException('Invalid query (must be at least 4 characters)');
        }
    }

    /**
     * @throws OpenAiException
     */
    protected function validateResponse(ResponseInterface $result): void
    {
        if ($result->getStatusCode() === 401) {
            throw new OpenAiException(__('API unauthorized. Token could be invalid.'));
        }

        if ($result->getStatusCode() >= 500) {
            throw new OpenAiException(__('Server error: %1', $result->getReasonPhrase()));
        }

        $data = Decoder::decode($result->getBody(), Json::TYPE_ARRAY);

        if (isset($data['error'])) {
            throw new OpenAiException(__(
                '%1: %2',
                $data['error']['type'] ?? 'unknown',
                $data['error']['message'] ?? 'unknown'
            ));
        }

        if (!isset($data['choices'])) {
            throw new OpenAiException(__('No results were returned by the server'));
        }
    }

    public function convertToResponse(StreamInterface $stream): string
    {
        $streamText = (string) $stream;
        $data = Decoder::decode($streamText, Json::TYPE_ARRAY);

        $choices = $data['choices'] ?? [];
        $textData = reset($choices);

        $text = $textData['text'] ?? '';
        $text = trim($text);
        $text = trim($text, '"');

        if (empty($text)) {
            $text = $textData['message']['content'] ?? '';
        }
        if (substr($text, 0, strlen(static::CUT_RESULT_PREFIX)) == static::CUT_RESULT_PREFIX) {
            $text = substr($text, strlen(static::CUT_RESULT_PREFIX));
        }

        return $text;
    }


    public function getType(): string
    {
        return static::TYPE;
    }
}
