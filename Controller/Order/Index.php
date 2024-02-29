<?php

namespace Salzano\EmailLivePreview\Controller\Order;

use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/order/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_order_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'created_at_formatted' => $order->getCreatedAtFormatted(2),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];
    }
}
