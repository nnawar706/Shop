<?php

class CustomerModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_customer_id' => [
            'has-many' => ['\SalesOrderModel','customer_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ],
        'name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||alpha_space|||max_len,100'
        ],
        'email' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'valid_email'
        ],
        'phone_no' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'required|||regex,/^(?:\+88|88)?(01[3-9]\d{8})$/'
        ],
        'address' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'max_len,100'
        ],
        'company_name' => [
            'type'=> \DB\SQL\Schema::DT_VARCHAR128,
            'validate' => 'alpha_dash|||max_len,100'
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'customer');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createCustomer($data): array {
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone_no = $data['phone_no'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->company_name = $data['company_name'] ?? '';
        $this->blacklist = 0;
        $this->image_url = $data['image_url'] ?? 'https://nafisa.selopian.us/ui/images/customers/customer_img.png';
        unset($data['submit']);

        if($this->validate()) {
            try {
                $this->save();
                $info = $this->cast(NULL, 0);
                $result['data'] = $info;
                $status['code'] = 1;
                $status['message'] = 'Customer Successfully Added.';
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

    /**
     * @throws Exception
     */
    public function updateCustomer($id, $data): array {
        $this->fields(['sales_order_customer_id']);
        $this->load(['id=?', $id]);
        if($this->id) {
            $this->name = $data['name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->phone_no = $data['phone_no'] ?? '';
            $this->address = $data['address'] ?? '';
            $this->company_name = $data['company_name'] ?? '';
            $this->blacklist = 0;
            $this->image_url = $data['image_url'] ?? 'https://nafisa.selopian.us/ui/images/customers/customer_img.png';
            unset($data['submit']);
            if($this->validate()) {
                try {
                    $this->save();
                    $info = $this->cast(NULL, 0);
                    $result['data'] = $info;
                    $status['code'] = 1;
                    $status['message'] = 'Customer Successfully Updated.';
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
            $status['message'] = 'Invalid customer id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getCustomer($id): array {
        $this->fields(['sales_order_customer_id']);
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'Customer Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Customer Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getAllCustomers(): array {
        $this->fields(['sales_order_customer_id']);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All customer successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No customer found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getByName($data, $pageno, $perPage): array {
        $this->fields(['sales_order_customer_id']);
        $name = $data['name'];
        $offset = ($pageno - 1) * $perPage;
        $result['data'] = $this->afind(['name=?',$name], ['limit'=>$perPage, 'offset'=>$offset], 0, 0);
        return $result;
    }

    public function getAll($pageno, $perPage): array {
        $offset = ($pageno - 1) * $perPage;
        $totalRecords = $this->db->exec("SELECT COUNT(*) FROM customer")[0]['COUNT(*)'];
        $totalPages = ceil($totalRecords/$perPage);
        $status['total pages'] = $totalPages;
        $status['current page'] = (int)$pageno;
        $result['status'] = $status;
        $result['data'] = $this->db->exec("SELECT * FROM customer LIMIT $perPage OFFSET $offset");
        return $result;
    }

    public function addImage($id, $fileName) {
        $this->load(['id=?', $id]);
        $this->image_url = $fileName;
        $this->save();
    }

    public function deleteCustomer($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Customer Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Customer Id.';
        }
        $result['status'] = $status;
        return $result;
    }
}