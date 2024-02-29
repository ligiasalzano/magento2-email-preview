<?php

namespace Salzano\EmailLivePreview\Controller\Shipment;

use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/shipment/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_shipment_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'shipment' => $shipment,
            'shipment_id' => $shipment->getId(),
            'comment' => $shipment->getCustomerNoteNotify() ? $shipment->getCustomerNote() : '',
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];
    }
}
