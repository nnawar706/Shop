<?php

class PurchaseStatusModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'purchase_transaction_payment_status_id' => [
            'has-many' => ['\PurchaseTransactionModel','payment_status_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'status' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||max_len,50'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'purchase_status');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

}