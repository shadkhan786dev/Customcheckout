<?php

namespace Customcheckout\Testpayment\Block\Adminhtml\Order\View;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class PaymentInfo extends Template
{
    protected $orderRepository;

    public function __construct(
        Template\Context $context,
        OrderRepositoryInterface $orderRepository,
         LoggerInterface $logger,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);

    }

    /**
     * Get order by ID
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->orderRepository->get($orderId);
    }

    /**
     * Get payment additional information
     *
     * @return array
     */
    public function getPaymentAdditionalInformation()
    {
         $order = $this->getOrder();
        $payment = $order->getPayment();
        $additionalInfo = $payment->getAdditionalInformation();

        return $additionalInfo;
    }
}
