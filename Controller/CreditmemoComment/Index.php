<?php

namespace Salzano\EmailLivePreview\Controller\CreditmemoComment;

use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/creditmemo_comment/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_creditmemo_comment_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        $creditmemo = $order->getCreditmemosCollection()->getFirstItem();
        $comment = 'Testing creditmemo comments';

        return [
            'order' => $order,
            'creditmemo' => $creditmemo,
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
