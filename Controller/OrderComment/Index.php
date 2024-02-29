<?php

namespace Salzano\EmailLivePreview\Controller\OrderComment;


use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/order_comment/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_order_comment_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        $comment = 'Testing order comments';

        return [
            'order' => $order,
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
