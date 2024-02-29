<?php

namespace Salzano\EmailLivePreview\Controller\ShipmentComment;

use Magento\Sales\Model\Order;
use Salzano\EmailLivePreview\Controller\AbstractOrderEmailPreview;

class Index extends AbstractOrderEmailPreview
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'sales_email/shipment_comment/template';
    const XML_PATH_EMAIL_TEMPLATE_ID = 'sales_email_shipment_comment_template';

    /**
     * @inheritDoc
     */
    protected function getTransport(Order $order): array
    {
        $shipment = $order->getShipmentsCollection()->getFirstItem();
        $comment = 'Testing shipment comments';

        return [
            'order' => $order,
            'shipment' => $shipment,
            'comment' => $comment,
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];
    }
}
