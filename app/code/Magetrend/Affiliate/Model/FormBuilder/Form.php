<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Model\FormBuilder;

use Magento\Framework\Exception\LocalizedException;
use Magetrend\Affiliate\Model\ResourceModel\Account\Collection;

class Form extends \Magento\Framework\Model\AbstractModel
{
    public $fields = null;

    public $fieldFactory;

    public $optionFactory;

    public $fieldCollectionFactory;

    public $optionCollectionFactory;

    public $jsonHelper;

    public $storeManager;

    public $accountCollectionFactory;

    public $dataCollectionFactory;

    public $dataFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Model\FormBuilder\FieldFactory $fieldFactory,
        \Magetrend\Affiliate\Model\FormBuilder\OptionFactory $optionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Field\CollectionFactory $fieldCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Option\CollectionFactory $optionCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magetrend\Affiliate\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory,
        \Magetrend\Affiliate\Model\FormBuilder\DataFactory $dataFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->optionFactory = $optionFactory;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->dataFactory = $dataFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Form');
    }

    public function saveData($data, $objectType, $objectId)
    {
        $formFields = $this->getFields();
        if (empty($formFields)) {
            return;
        }

        $dataCollection = $this->dataCollectionFactory->create()
            ->addObjectFilter($objectId, $objectType);

        $fields = [];
        if ($dataCollection->getSize() > 0) {
            foreach ($dataCollection as $dataRecord) {
                $fields[$dataRecord->getFieldKey()] = $dataRecord;
            }
        }

        foreach ($formFields as $field) {
            $index = $field['name'];
            if (!isset($data[$index]) || empty($data[$index])) {
                continue;
            }

            if (isset($fields[$index])) {
                $dataField = $fields[$index];
            } else {
                $dataField = $this->dataFactory->create();
            }

            $dataField->addData([
                'object_id' => $objectId,
                'object_type' => $objectType,
                'field_key' => $index,
                'field_value' => $data[$index]
            ])->save();
        }
    }

    public function validateData($data)
    {
        $data = $this->removeUnusedFields($data);
        $this->validateRequiredFields($data);
        $this->validateEmailAddress($data['email'], $data['store_id']);
        return $data;
    }

    public function validateEmailAddress($email, $storeId = 0)
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $accountCollection = $this->accountCollectionFactory->create()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('website_id', $websiteId);

        if ($accountCollection->getSize() != 0) {
            throw new LocalizedException(__('This email is already registered'));
        }

        return true;
    }

    public function removeUnusedFields($data)
    {
        $filteredData = [];
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $name = $field['name'];
            if (isset($data[$name])) {
                $filteredData[$name] = $data[$name];
            }
        }

        foreach (['store_id', 'back_url'] as $name) {
            if (isset($data[$name])) {
                $filteredData[$name] = $data[$name];
            }
        }

        return $filteredData;
    }

    public function validateRequiredFields($data)
    {
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $index = $field['name'];
            $isRequired = $field['is_require'];
            if ((!isset($data[$index]) || empty($data[$index])) && $isRequired) {
                $errorMessage = __('Field "%1" is required', $field['label']);
                if (isset($field['error_message'])) {
                    $messages = $this->jsonHelper->jsonDecode($field['error_message']);
                    if (isset($messages['is_required'])) {
                        $errorMessage = $messages['is_required'];
                    }
                }
                throw new LocalizedException($errorMessage);
            }
        }
    }

    public function getFields()
    {
        if ($this->fields == null) {
            $collection = $this->fieldCollectionFactory->create()
                ->setFormFilter($this->getId())
                ->joinOptionCollection()
                ->sortByPositionCollection();

            $this->fields = $collection->getGroupedData();
        }
        return $this->fields;
    }

    public function save()
    {
        parent::save();
        $fieldData = $this->getData('field');
        $this->saveFieldCollection($fieldData);
    }

    public function saveFieldCollection($fieldData)
    {
        if (isset($fieldData['options'])) {
            $data = $fieldData['options'];
            if (!empty($data)) {
                foreach ($data as $fieldData) {
                    if (isset($fieldData['is_delete']) && $fieldData['is_delete'] == 1) {
                        $this->deleteField($fieldData['id']);
                    } else {
                        $this->saveField($fieldData);
                    }
                }
            }
        }
    }

    public function saveField($fieldData)
    {
        $newFieldId = $this->createField($fieldData, $this->getId());
        if (isset($fieldData['previous_group']) && $fieldData['previous_group'] == 'select') {
            if (isset($fieldData['values'])) {
                foreach ($fieldData['values'] as $fieldOption) {
                    if ($fieldOption['is_delete'] == 1) {
                        $this->deleteOption($fieldOption['option_type_id']);
                    } else {
                        $this->createOption($newFieldId, $fieldOption);
                    }
                }
            }
        }
    }

    public function createOption($fieldId, $postData)
    {
        $option = $this->optionFactory->create();
        if (isset($postData['option_type_id']) && $postData['option_type_id'] > 0) {
            $option->load($postData['option_type_id']);
        }

        $option->setData('field_id', $fieldId);
        $option->setData('value', $postData['value']);
        $option->setData('label', $postData['label']);
        $option->setData('position', $postData['sort_order']);
        $option->save();
    }

    public function createField($postData, $formId)
    {
        $field = $this->fieldFactory->create();
        if (isset($postData['id']) && $postData['option_id'] > 0) {
            $field->load($postData['id']);
        }

        if (!isset($postData['error_message'])) {
            $postData['error_message'] = [];
        }

        $postData['error_message'] = $this->jsonHelper->jsonEncode($postData['error_message']);

        $field->setData('form_id', $formId);
        $field->setData('name', $postData['name']);
        $field->setData('type', $postData['type']);
        $field->setData('position', $postData['sort_order']);
        $field->setData('is_required', $postData['is_require']);
        $field->setData('default_value', isset($postData['default_value'])?$postData['default_value']:'');
        $field->setData('frontend_label', isset($postData['frontend_label'])?$postData['frontend_label']:'');
        $field->setData('error_message', $postData['error_message']);
        $field->setData('label', $postData['label']);
        $field->save();
        return $field->getId();
    }

    public function deleteField($fieldId)
    {
        $field = $this->fieldFactory->create();
        $field->load($fieldId);
        $field->delete();

        $this->optionCollectionFactory->create()
            ->setFieldFilter($field->getId())
            ->walk('delete');

        return true;
    }

    public function deleteOption($optionId)
    {
        $option = $this->optionFactory->create();
        $option->load($optionId);
        $option->delete();

        return true;
    }

    public function delete()
    {
        $this->deleteFieldCollection();
        parent::delete();
    }

    public function deleteFieldCollection()
    {
        $collection = $this->fieldCollectionFactory->create()
            ->setFormFilter($this->getId());
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                $this->deleteField($item->getId());
            }
        }
    }
}
