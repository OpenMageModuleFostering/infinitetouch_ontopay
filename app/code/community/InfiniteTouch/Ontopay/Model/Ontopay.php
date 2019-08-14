<?php

class InfiniteTouch_Ontopay_Model_Ontopay extends Mage_Payment_Model_Method_Abstract
{
    const LOG_PATH = 'ontopay.log';
    const SUBMIT_URL = 'https://www.ontopay.com/checkout/';
    const DEV_SUBMIT_URL = 'https://www.ontopay.com/checkout-dev/';

    protected $_code = 'infinitetouch_ontopay';
    protected $_formBlockType = 'infinitetouch_ontopay/form_ontopay';

    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;

    public function authorize(Varien_Object $payment, $amount)
    {
        $helper = Mage::helper('infinitetouch_ontopay');
        /** @var $order Mage_Sales_Model_Order */
        $order = $payment->getOrder();
        $orderId = $order->getIncrementId();
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();

        $redirectUrl = ($helper->getStoreConfig('test_mode') ? self::DEV_SUBMIT_URL : self::SUBMIT_URL)
            . "?to=" . $helper->getStoreConfig('merchant_id')
            . "&amount=" . $amount
            . "&currency=" . $currency
            . "&order_id=" . $orderId;
        Mage::getSingleton('core/session')->setRedirectUrl($redirectUrl);
    }

    public function getOrderPlaceRedirectUrl()
    {
        return (string)Mage::getSingleton('core/session')->getRedirectUrl();
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED);
        return $this;
    }


}

