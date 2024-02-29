<?php

namespace Salzano\EmailLivePreview\Controller\Creditmemo;

use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/creditmemo/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_creditmemo_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        $creditmemo = $order->getCreditmemosCollection()->getFirstItem();

        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'creditmemo' => $creditmemo,
            'creditmemo_id' => $creditmemo->getId(),
            'comment' => $creditmemo->getCustomerNoteNotify() ? $creditmemo->getCustomerNote() : '',
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
