<?php

namespace Magecomp\Chatgptaicontentpro\Controller\Adminhtml\Generate;

use Magecomp\Chatgptaicontentpro\Model\OpenAI\OpenAiException;
use InvalidArgumentException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magecomp\Chatgptaicontentpro\Model\CompletionConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ProductFactory;


class Bulkshortdiscription extends Action
{
    public const ADMIN_RESOURCE = 'Magecomp_Chatgptaicontentpro::generate';

    private JsonFactory $jsonFactory;
    private CompletionConfig $completionConfig;
    protected $productRepository;
    protected $request;
    protected $productCollections;
    protected $filter;
    protected $storeManager;
    protected $collectionFactory;
    protected $productFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CompletionConfig $completionConfig,
        ProductRepositoryInterface $productRepository,
        RequestInterface $request,
        Product $productCollections,
        Filter $filter,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->completionConfig = $completionConfig;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->productCollections = $productCollections;
        $this->filter = $filter;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $productCollection = $this->collectionFactory->create();
        $productCollection->addStoreFilter($storeId);
        $productCollection->addAttributeToSelect('name');
        $productIds = $this->filter->getCollection($this->productCollections->getCollection())->getAllIds();

        $selectedProducts = [];
        foreach ($productIds as $productId) {
            $product = $this->productFactory->create()->load($productId);
            $selectedProducts[] = $product;
        }

        $resultPage = $this->jsonFactory->create();
         try {

        foreach ($selectedProducts as $product) {

             $selectedProductsCounts = $selectedProducts;
             $process = is_countable($selectedProductsCounts) ?  count($selectedProductsCounts): 0;
             $product1 = $this->productRepository->get($product->getSku());
             $productNames= $product->getName();
             $type = $this->completionConfig->getByType('short_description');
             $prompt=$productNames;
             $result = $type->query($prompt);  
             $product1->setStoreId(0);
             $product1->setShortDescription($result); 
             $this->productRepository->save($product1);
                              
        } 
         $this->messageManager->addSuccessMessage(__('Short Description Updated Successfully for '.$process.' Products'));

        }catch (OpenAiException | InvalidArgumentException $e) {
           $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->_redirect('catalog/product/index');
     }
      /**
     * @inheritDoc
     */
   protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magecomp_Chatgptaicontentpro::generate');
    }
}
