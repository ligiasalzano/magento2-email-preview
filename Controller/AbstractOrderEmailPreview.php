<?php

namespace Salzano\EmailLivePreview\Controller;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject;
use Magento\Email\Model\Template;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template as TemplateContainer;

abstract class AbstractOrderEmailPreview implements HttpGetActionInterface
{
    protected const XML_PATH_EMAIL_TESTING_FIELD = 'sales_email/sales_email_testing/order_id';

    const XML_PATH_EMAIL_TEMPLATE_FIELD = '';
    const XML_PATH_EMAIL_TEMPLATE_ID = '';

    public function __construct(
        private RawFactory $resultRawFactory,
        private PaymentHelper $paymentHelper,
        private OrderIdentity $identityContainer,
        private Renderer $addressRenderer,
        private Template $template,
        private OrderRepositoryInterface $orderRepository,
        private ScopeConfigInterface $config,
    ) {}

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $emailTemplate = $this->template->loadDefault(static::XML_PATH_EMAIL_TEMPLATE_ID);

        $configValue = (int) $this->config->getValue(static::XML_PATH_EMAIL_TEMPLATE_FIELD);
        if ($configValue) {
            $emailTemplate = $this->template->loadByConfigPath(static::XML_PATH_EMAIL_TEMPLATE_FIELD);
        }

        $orderId = (int) $this->config->getValue(self::XML_PATH_EMAIL_TESTING_FIELD);
        $order =  $this->orderRepository->get($orderId);
        $this->identityContainer->setStore($order->getStore());

        $transport = $this->getTransport($order);
        $transportObject = new DataObject($transport);

        $result = $this->resultRawFactory->create();
        $result->setContents($emailTemplate->getProcessedTemplate($transportObject->getData()));
        return $result;
    }

    abstract protected function getTransport(Order $order): array;

    /**
     * Get payment info block as html
     *
     * @param Order $order
     * @return string
     * @throws \Exception
     */
    protected function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * Render shipping address into html.
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Render billing address into html.
     *
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get template options.
     *
     * @return array
     */
    protected function getTemplateOptions()
    {
        return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->identityContainer->getStore()->getStoreId()
        ];
    }
}
