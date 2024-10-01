<?php
namespace Customcheckout\Testpayment\Model;

use Magento\Payment\Model\InfoInterface;

class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'testpayment';

    public function authorize(InfoInterface $payment, $amount)
    {
        // Basic authorization logic
        $payment->setIsTransactionClosed(false);

        return $this;
    }
}
