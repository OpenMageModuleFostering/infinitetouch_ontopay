<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stepantsevich_m
 * Date: 01.07.13
 * Time: 10:38
 * To change this template use File | Settings | File Templates.
 */ 
/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;


$installer->startSetup();

$installer->addAttribute(
    'order',
    'ontopay_transaction_id',
    array(
        'type' => 'varchar'
    )
);

$installer->endSetup();