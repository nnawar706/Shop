<?php

class TransactionTypeModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'purchase_transaction_transaction_type' => [
            'has-many' => ['\PurchaseTransactionModel','transaction_type_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||max_len,50',
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'transaction_type');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createTransactionType($data): array {
        $this->name = $data['name'] ?? '';

        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Transaction Type Successfully Added.';
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
        $this->fields(['purchase_transaction_transaction_type']);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Transaction Type successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Transaction Type found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getTransactionType($id): array {
        $data = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $status['code'] = 1;
            $status['message'] = 'Transaction Type Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Transaction Type Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateTransactionType($id, $data): array {
        $info = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $status['code'] = 1;
                    $status['message'] = 'Transaction Type Successfully Updated.';
                } catch(PDOException $e) {
                    $status['code'] = 0;
                    $status['message'] = $e->errorInfo[2];
                }
            } else {
                $status['code'] = 0;
                $status['message'] = Base::instance()->get('error_msg');
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Transaction Type Id.';
        }
        $result['data'] = $info;
        $result['status'] = $status;
        return $result;
    }

    public function deleteTransactionType($id): array {
        $data = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $status['code'] = 1;
                $status['message'] = 'Transaction Type Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Transaction Type Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

}