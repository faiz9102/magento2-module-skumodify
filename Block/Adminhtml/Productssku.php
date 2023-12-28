<?php

namespace Faiz\SKUmod\Block\Adminhtml;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;

class Productssku extends Template
{
    protected $_productCollectionFactory;
    protected $_formKey;

    public function __construct(
        Template\Context $context,
        ProductCollectionFactory $productCollectionFactory,
        FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_formKey = $formKey;
    }

    public function getProducts()
    {
        try {
            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            return $productCollection;
        } catch (Exception $e) {
            return;
        }
    }
    public function getFormKey()
    {
        if(!$this->_formKey->isPresent()){
            return true;
        }
        else{
            return $this->_formKey->getFormKey();
        }
    }
}
