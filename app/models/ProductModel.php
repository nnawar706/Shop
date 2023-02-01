<?php

class ProductModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'inventory_product_id' => [
            'has-many' => ['\InventoryModel','product_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'sales_product_product_id' => [
            'has-many' => ['\SalesProductModel','product_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'purchase_product_product_id' => [
            'has-many' => ['\PurchaseProductModel','product_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'inventory_trace_product_id' => [
            'has-many' => ['\InventoryTraceModel','product_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'brand_id' => [
            'belongs-to-one' => '\BrandModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'category_id' => [
            'belongs-to-one' => '\CategoryModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'name' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||alpha_space|||max_len,100'
        ],
        'description' => [
            'type' => \DB\SQL\Schema::DT_VARCHAR512,
            'validate' => 'required|||max_len,500'
        ],
        'unit_id' => [
            'belongs-to-one' => '\ProductUnitModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'unit_size' => [
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||max_len,6'
        ],
        'cost_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,7'
        ],
        'mrp' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,7'
        ],
        'wholesale_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,7'
        ],
        'retail_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required|||max_len,7'
        ],
        'discount_amount' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'max_len,7'
        ]

    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'product');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createProduct($data): array {
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->unit_id = $data['unit_id'] ?? '';
        $this->unit_size = $data['unit_size'] ?? '';
        $this->brand_id = $data['brand_id'] ?? '';
        $this->category_id = $data['category_id'] ?? '';
        $this->cost_price = $data['cost_price'] ?? '';
        $this->mrp = $data['mrp'] ?? '';
        $this->wholesale_price = $data['wholesale_price'] ?? '';
        $this->retail_price = $data['retail_price'] ?? '';
        $this->discount_amount = $data['discount_amount'] ?? '';
        $this->product_image_url = $data['product_image_url'] ?? 'https://nafisa.selopian.us/ui/images/products/product_img.png';
        unset($data['submit']);
        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $log = new LogModel();
                $stat = "Product ID: " . $this->id . " has been created.";
                $log->add($stat, 11);
                $status['code'] = 1;
                $status['message'] = 'Product Successfully Added.';
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
        $this->fields(['inventory_trace_product_id','inventory_product_id','sales_product_product_id','unit_id.product_unit_id',
            'unit_id.product_raw_material_unit_id','brand_id.logo_url','brand_id.description','brand_id.product_brand_id',
            'category_id.description','category_id.featured','purchase_product_product_id','category_id.parent_id',
            'category_id.category_parent_id','category_id.product_category_id','category_id.product_formula_category_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All product successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No product found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getProduct($id): array {
        $this->fields(['inventory_trace_product_id','inventory_product_id','sales_product_product_id','unit_id.product_unit_id',
            'unit_id.product_raw_material_unit_id','brand_id.logo_url','brand_id.description','brand_id.product_brand_id'
            ,'category_id.description','category_id.featured','purchase_product_product_id','category_id.parent_id',
            'category_id.category_parent_id','category_id.product_category_id','category_id.product_formula_category_id'], true);
        $this->load(['id=?', $id]);
        if($this->id) {
            $info = $this->cast();
            $result['data'] = $info;
            $status['code'] = 1;
            $status['message'] = 'Product Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function addImage($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->product_image_url = $fileName;
        $this->save();
    }

    public function deleteProduct($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $result['data']['id'] = $this->id;
                $log = new LogModel();
                $stat = "Product ID: " . $this->id . " has been deleted.";
                $log->add($stat, 11);
                $status['code'] = 1;
                $status['message'] = 'Product Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1452) ? "Invalid input data." : (($e->errorInfo[1] == 1451) ? "Deletion of this product is restricted." : $e->errorInfo[2]);
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Product Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateProduct($data): array {
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->unit_id = $data['unit_id'] ?? '';
        $this->unit_size = $data['unit_size'] ?? '';
        $this->brand_id = $data['brand_id'] ?? '';
        $this->category_id = $data['category_id'] ?? '';
        $this->cost_price = $data['cost_price'] ?? '';
        $this->mrp = $data['mrp'] ?? '';
        $this->wholesale_price = $data['wholesale_price'] ?? '';
        $this->retail_price = $data['retail_price'] ?? '';
        $this->discount_amount = $data['discount_amount'] ?? '';
        unset($data['submit']);

        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $log = new LogModel();
                $stat = "Product ID: " . $this->id . " has been updated.";
                $log->add($stat, 11);
                $status['code'] = 1;
                $status['message'] = 'Product Successfully Added.';
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

}