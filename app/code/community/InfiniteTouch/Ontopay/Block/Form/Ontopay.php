<?php

class InfiniteTouch_Ontopay_Block_Form_Ontopay extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('infinitetouch/ontopay/form/ontopay.phtml');
        $this->setTemplate('infinitetouch/ontopay/form/redirect.phtml')
            ->setRedirectMessage(
                Mage::helper('infinitetouch_ontopay')->__('You will be redirected to the OntoPay website.')
            )
            ->setMethodTitle('') // Output PayPal mark, omit title
            ->setMethodLabelAfterHtml($mark->toHtml())
        ;
        return parent::_construct();
    }
}