<?php

namespace Salzano\EmailLivePreview\Controller\InvoiceComment;

use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/invoice_comment/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_invoice_comment_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        $invoice = current($order->getInvoiceCollection()->getItems());
        $comment = 'Testing invoice comments';

        return [
            'order' => $order,
            'invoice' => $invoice,
            'comment' => $comment,
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];
    }
}
