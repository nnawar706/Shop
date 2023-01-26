<?php

class ProductRawMaterialModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'product_unit_id' => [
            'belongs-to-one' => '\ProductUnitModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha|||max_len,100',
        ],
        'unit_size' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,7'
        ],
        'cost_price' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,11'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'product_raw_material');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function createMaterial($data)
    {
    }

    public function getAll(): array {
        $this->fields(['product_unit_id.product_unit_type','product_unit_id.product_raw_material_product_unit_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All Product Raw Material successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No Product Raw Material found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getMaterial($id): array {
        $this->fields(['product_unit_id.product_unit_type','product_unit_id.product_raw_material_product_unit_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Product Raw Material Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Raw Material Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateMaterial($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->copyfrom($data);
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Product Raw Material Successfully Updated.';
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
            $status['message'] = 'Invalid Product Raw Material Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteMaterial($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Product Raw Material Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Raw Material Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}