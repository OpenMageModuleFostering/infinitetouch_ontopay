<?php

class InfiniteTouch_Ontopay_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_xmlPathModulePrefix = 'payment/infinitetouch_ontopay/';
    const ONTOPAY_DEV_VALIDATION_URL = 'https://dev-api.ontopay.com/transaction/validate/';
    const ONTOPAY_VALIDATION_URL = 'https://api.ontopay.com/transaction/validate/';
    const ORDER_STATUS_AFTER_PAYMENT = 'processing';
    const LOG_PATH = 'ontopay.log';

    /**
     * Return module config
     * @param string $path without module prefix
     * @return mixed
     */
    public function getStoreConfig($path)
    {
        $xmlPath = $this->_xmlPathModulePrefix . $path;
        return Mage::getStoreConfig($xmlPath);
    }

    public function logEnabled()
    {
        return $this->getStoreConfig('enable_logging');
    }

    public function checkIsPaid($order)
    {
        $fields_string = '';
        $url = $this->getStoreConfig('test_mode') ? self::ONTOPAY_DEV_VALIDATION_URL : self::ONTOPAY_VALIDATION_URL;
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $fields = array(
            "api_key" => $this->getStoreConfig('api_key'),
            "transaction_id" => $order->getOntopayTransactionId(),
            "amount" => $order->getBaseGrandTotal(),
            "currency" => $currency
        );
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        $url .= '?' . $fields_string;
        if ($this->logEnabled()) Mage::Log("Request: " . $url, null, self::LOG_PATH);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        if ($this->logEnabled()) Mage::Log("Response: " . $content, null, self::LOG_PATH);
        $json = json_decode($content);
        if ($json->status == "ok") { return true; }

        return false;
    }

    public function validateOrderPayment($order)
    {
        if ($this->checkIsPaid($order)) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            $order->addRelatedObject($invoice);
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
            $order->setStatus(self::ORDER_STATUS_AFTER_PAYMENT);
            $order->save();
            $order->sendNewOrderEmail();
            return true;
        }
        else {
            $order->registerCancellation('Gateway hasn\'t authorized the payment.');
            $order->save();
        }
        return false;
    }

}