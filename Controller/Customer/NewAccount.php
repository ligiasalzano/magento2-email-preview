<?php

namespace Salzano\EmailLivePreview\Controller\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject;
use Magento\Email\Model\Template;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class NewAccount implements HttpGetActionInterface
{
    protected const XML_PATH_EMAIL_TESTING_FIELD = 'customer/customer_email_testing/customer_id';

    const XML_PATH_EMAIL_TEMPLATE_FIELD = '';
    const XML_PATH_EMAIL_TEMPLATE_ID = '';

    public function __construct(
        private RawFactory $resultRawFactory,
        private Template $template,
        private CustomerRepositoryInterface $customerRepository,
        private ScopeConfigInterface $config,
        private StoreManagerInterface $storeManager,
        private CustomerRegistry $customerRegistry,
        private DataObjectProcessor $dataProcessor,
        private CustomerViewHelper $customerViewHelper,
    ) {}

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $emailTemplate = $this->template->loadDefault('customer_create_account_email_template');

        $configValue = (int) $this->config->getValue('customer/create_account/email_template');
        if ($configValue) {
            $emailTemplate = $this->template->loadByConfigPath('customer/create_account/email_template');
        }

        $storeId = 1;

        $customerId = (int) $this->config->getValue(self::XML_PATH_EMAIL_TESTING_FIELD);
        $customer = $this->customerRepository->getById($customerId);
        $store = $this->storeManager->getStore($storeId);
        $customerEmailData = $this->getFullCustomerObject($customer);
        $transport = [
            'customer' => $customerEmailData,
            'back_url' => '',
            'store' => $store
        ];
        $transportObject = new DataObject($transport);

        $result = $this->resultRawFactory->create();
        $result->setContents($emailTemplate->getProcessedTemplate($transportObject->getData()));
        return $result;
    }

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param CustomerInterface $customer
     * @return CustomerSecure
     */
    private function getFullCustomerObject($customer): CustomerSecure
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     */
    private function getWebsiteStoreId($customer, $defaultStoreId = null): int
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }
}
