<?php

class InfiniteTouch_Ontopay_TransactionController extends Mage_Core_Controller_Front_Action
{
    const ORDER_STATUS_AFTER_PAYMENT = 'processing';

    public function saveAction()
    {
        $arrParams = $_GET;
        $orderId = $arrParams['order_id'];
        $transactionId = $arrParams['transaction_id'];

        try{
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $order->setOntopayTransactionId($transactionId);
            $order->save();
//            print "Transaction #{$transactionId} was saved into order #{$orderId}";
            $resultUrl = Mage::helper('infinitetouch_ontopay')->validateOrderPayment($order) ? 'checkout/onepage/success' : 'checkout/onepage/failure';
            $this->_redirect($resultUrl);
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function checkAction() {
        $arrParams = $_GET;
        $orderId = $arrParams['order_id'];

        try {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            Mage::helper('infinitetouch_ontopay')->validateOrderPayment($order);
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

}