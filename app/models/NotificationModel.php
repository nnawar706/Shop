<?php

class NotificationModel extends \DB\Cortex {

    protected $fieldConf = [
        'branch_id' => [
            'belongs-to-one' => '\BranchModel',
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'inventory_id' => [
            'belongs-to-one' => '\InventoryModel',
            'type' => \DB\SQL\Schema::DT_INT
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'notification');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function addCron($row) {
        $this->inventory_id = $row['id'];
        $this->branch_id = $row['branch_id'];
        $this->notification_text = "Stock refill alert for product_id " . $row['product_id'];
        $this->event_time = date('y-m-d h:i:s');
        $this->save();
        $this->reset();
    }
}