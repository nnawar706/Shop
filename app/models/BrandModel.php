<?php

class BrandModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'product_brand_id' => [
            'has-many' => ['\ProductModel','brand_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha|||max_len,50'
        ],
        'description' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR512,
            'validate' => 'required|||max_len,500'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'brand');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createBrand($data): array {
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->logo_url = $data['logo_url'] ?? 'https://nafisa.selopian.us/ui/images/brands/brand_img.png';
        unset($data['submit']);

        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Brand Successfully Added.';
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

    public function addImage($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->logo_url = $fileName;
        $this->save();
    }

    public function getAll(): array {
        $this->fields(['product_brand_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All brand successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No brand found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getBrand($id): array {
        $this->fields(['product_brand_id'], true);
        $data = [];
        $this->fields(['user_profile_shop_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $status['code'] = 1;
            $status['message'] = 'brand Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid brand Id.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateBrand($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? '';
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Brand Successfully Updated.';
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
            $status['message'] = 'Invalid brand Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteBrand($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'brand Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid brand Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}