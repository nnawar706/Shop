<?php

class TransferTypeModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'inventory_trace_transfer_type_id' => [
            'has-many' => ['\InventoryTraceModel','transfer_type_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||max_len,50',
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'transfer_type');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createTransferType($data): array {
        $info = [];
        $this->name = $data['name'] ?? '';

        if($this->validate()) {
            try {
                $this->save();
                $info['id'] = $this->id;
                $status['code'] = 1;
                $status['message'] = 'Transfer Type Successfully Added.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = Base::instance()->get('error_msg');
        }
        $result['data'] = $info;
        $result['status'] = $status;
        return $result;
    }

    public function getAll(): array {
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All Transfer Type successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Transfer Type found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getTransferType($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Transfer Type Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Transfer Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateTransferType($id, mixed $data): array {
        $info = [];
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info['id'] = $this->id;
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Transfer Type Successfully Updated.';
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
            $status['message'] = 'Invalid Transfer Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteTransferType($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Transfer Type Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Transfer Type Id.';
        }
        $result['status'] = $status;
        return $result;
    }

}