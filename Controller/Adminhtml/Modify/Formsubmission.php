<?php

namespace Faiz\SKUmod\Controller\Adminhtml\Modify;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class Formsubmission extends Action implements HttpPostActionInterface
{
    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;


    /**
     * Constructor
     *
     * @param FormKeyValidator $formKeyValidator
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param ProductRepository $productRepository
     */
    public function __construct(
        FormKeyValidator $formKeyValidator,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        ProductRepository $productRepository,
        Context $context
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    public function execute()
    {

        // form Key validation
        if (!$this->formKeyValidator->validate($this->getRequest()))
        {
            $this->messageManager->addError(__('Invalid form key. Please refresh the page.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('custom/modify/sku');
        }

        // if form is valid then change the SKU of the products after comparing with old ones.
        try {
            $modifiedCount = 0;
            $skus = $this->getRequest()->getParam('sku', []);
            foreach ($skus as $productId => $newSku) {
                $product = $this->productRepository->getById($productId, true);
                if ($product->getSku() !== $newSku) {
                    if (strlen($newSku) <= 2) {
                        throw new LocalizedException(
                            __('SKU length must be greater than 2 for product with ID %1.', $productId)
                        );
                    }

                    $product->setSku($newSku);
                    $this->productRepository->save($product);
                    $modifiedCount++;
                }
            }

            $this->messageManager->addSuccess(__("$modifiedCount SKUs have been successfully modified."));
        }

        catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }
        catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while modifying SKUs.'));
        }

        // Return to the same form page
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultForward->setPath('custom/modify/sku');

        return $resultForward;
    }
}
