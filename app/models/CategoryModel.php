<?php

class CategoryModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'category_parent_id' => [
            'has-many' => ['\CategoryModel','parent_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'product_category_id' => [
            'has-many' => ['\ProductModel','category_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'product_formula_category_id' => [
            'has-many' => ['\ProductFormulaModel','category_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||unique|||alpha_space|||min_len,5|||max_len,50'
        ],
        'description' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR512,
            'validate' => 'required|||max_len,500|||alpha_space'
        ],
        'featured' => [
            'type'=> \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required|||exact_len,1'
        ],
        'parent_id' => [
            'belongs-to-one' => '\CategoryModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'category');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createCategory($data): array {
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->featured = $data['featured'] ?? '';
        $this->parent_id = $data['parent_id'] ?? 0;
        if($this->validate()) {
            try {
                $this->save();
                $result = $this->getCategory($this->id);
                $log = new LogModel();
                $stat = "Category ID: " . $this->id . " has been created.";
                $log->add($stat, 6);
                $status['code'] = 1;
                $status['message'] = 'Category Successfully Added.';
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
        $this->fields(['category_parent_id', 'product_category_id','product_formula_category_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $status['code'] = 1;
            $status['message'] = 'All category successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No category found.';
        }
        $result['data'] = $data;
        $result['status'] = $status;
        return $result;
    }

    public function getAllCategory($id): array {
        $this->fields(['category_parent_id', 'product_category_id','product_formula_category_id'], true);
        $data = $this->afind(['parent_id=?', $id], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'categories under one parent are successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No category found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getCategory($id): array {
        $this->fields(['category_parent_id','product_formula_category_id','product_category_id'], true);
        $this->fields(['parent_id.id','parent_id.name']);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 1);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Category Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Category Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateCategory($id, $data): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->featured = $data['featured'] ?? '';
            $this->parent_id = $data['parent_id'] ?? 0;
            if($this->validate()) {
                try {
                    $this->save();
                    $result = $this->getCategory($this->id);
                    $log = new LogModel();
                    $stat = "Category ID: " . $this->id . " has been updated.";
                    $log->add($stat, 6);
                    $status['code'] = 1;
                    $status['message'] = 'Category Successfully Updated.';
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
            $status['message'] = 'Invalid Category Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteCategory($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $log = new LogModel();
                $stat = "Category ID: " . $this->id . " has been deleted.";
                $log->add($stat, 6);
                $status['code'] = 1;
                $status['message'] = 'Category Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = ($e->errorInfo[1] == 1451) ? "Cannot delete this category since it has products." : $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Category Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getName($id): string {
        $this->load(['id=?', $id]);
        return $this->name;
    }

}