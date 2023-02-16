<?php

class SalesTransactionModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_id' => [
            'belongs-to-one' => '\SalesOrderModel',
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'transaction_type_id' => [
            'belongs-to-one' => '\TransactionTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'sales_status_id' => [
            'belongs-to-one' => '\SalesStatusModel',
            'type' => \DB\SQL\Schema::DT_INT
        ],
        'amount_paid' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_transaction');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createTransaction($data): array {
        $this->sales_order_id = $data['sales_order_id'] ?? '';
        $this->transaction_type_id = $data['transaction_type_id'] ?? '';
        $this->amount_paid = $data['amount_paid'] ?? '';
        $this->transaction_document_url = $data['transaction_document_url'] ?? '';
        $this->ref_comment = $data['ref_comment'] ?? '';
        $this->transaction_at = date('y-m-d h:i:s');
        unset($data['submit']);
        if($this->validate()) {
            try {
                $order = new SalesOrderModel();
                $total = $order->getTotalAmount($data['sales_order_id']);
                if($total > $data['amount_paid']) {
                    $this->sales_status_id = 2;
                } else {
                    $this->sales_status_id = 1;
                }
                $order->updatePaidAmount($data['sales_order_id'], $data['amount_paid']);
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Sales Transaction Successfully Added.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $this->fields(['sales_order_id.id', 'transaction_type_id.id', 'transaction_type_id.name',
            'sales_status_id.id', 'sales_status_id.status']);
        $this->fields(['sales_transaction_sales_status_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Purchase transaction successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Purchase transaction found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function addFile($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->transaction_document_url = $fileName;
        $this->save();
    }

    public function deleteTransaction($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Sales Transaction Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid sales transaction Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getSales($id): array {
        $this->fields(['sales_order_id.id', 'transaction_type_id.id', 'transaction_type_id.name',
            'sales_status_id.id', 'sales_status_id.status']);
        $this->fields(['sales_transaction_sales_status_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Purchase transaction Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Purchase transaction Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}