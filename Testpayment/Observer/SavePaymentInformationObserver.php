<?php

namespace Customcheckout\Testpayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\OfflinePayments\Model\Purchaseorder;
use Magento\Framework\App\Request\DataPersistorInterface;

class SavePaymentInformationObserver implements ObserverInterface
{
    protected $logger;
    protected $inputParamsResolver;
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver,
        \Magento\Framework\App\State $state
    ) {
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->inputParamsResolver = $inputParamsResolver;
        $this->_state = $state;
    }

    public function execute(Observer $observer)
    {
       $order = $observer->getOrder();
        $inputParams = $this->inputParamsResolver->resolve();
        if ($this->_state->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML) {
            foreach ($inputParams as $inputParam) {
                if ($inputParam instanceof \Magento\Quote\Model\Quote\Payment) {
                    $paymentData = $inputParam->getData('additional_data');
                    
                    $paymentOrder = $order->getPayment();
                    $order = $paymentOrder->getOrder();
                    $quote = $this->quoteRepository->get($order->getQuoteId());
                    $paymentQuote = $quote->getPayment();
                    $method = $paymentQuote->getMethodInstance()->getCode();
                    $accountHolderName ='';
                    $accountNumber ='';
                    if ($method == 'testpayment') {
                        $accountHolderName = $paymentData['account_holder_name'];
                        $accountNumber = $paymentData['account_number'];
                    }
                }
            }
        }
        

        // Validate data
        if (!$this->validatePersonName($accountHolderName) || !$this->validateAccountNumber($accountNumber)) {
            throw new LocalizedException(__('Invalid name or account number.'));
        }

        if (isset( $accountHolderName)) {
            $paymentQuote->setAdditionalInformation('account_holder_name',  $accountHolderName);
            $paymentOrder->setAdditionalInformation('account_holder_name',  $accountHolderName);
        }
        if (isset($accountNumber)) {
            $paymentQuote->setAdditionalInformation('account_number', $accountNumber);
            $paymentOrder->setAdditionalInformation('account_number', $accountNumber);
        }
       

        return $this;
    }

    // Custom validation functions
    private function validatePersonName($name)
    {
        return preg_match('/^[a-zA-Z\s]+$/', $name) && strlen($name) <= 48;
    }

    private function validateAccountNumber($accountNumber)
    {
        return preg_match('/^\d{16}$/', $accountNumber);
    }
}
