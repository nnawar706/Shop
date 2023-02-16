<?php

class SalesProductModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'sales_order_id' => [
            'belongs-to-one' => '\SalesOrderModel',
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'product_id' => [
            'belongs-to-one' => '\ProductModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
            'validate' => 'required'
        ],
        'buying_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'selling_price' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'discount_amount' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
        'amount_unit' => [
            'type' => \DB\SQL\Schema::DT_INT,
            'validate' => 'required'
        ],
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'sales_product');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    /**
     * @throws Exception
     */
    public function createSales($data, $sales_id): array {
        $product = new ProductModel();
        $inv = new InventoryModel();
        $status['sales_order_id'] = $sales_id;
        $total_amount = 0;
        foreach ($data['product_name_list'] as $item) {
            $this->sales_order_id = $sales_id;
            $prod = $product->getProduct($item['product_id']);
            if($prod['status']['code'] == 1) {
                if($data['sales_type_id'] == 1) {
                    $this->selling_price = $prod['data']['wholesale_price'];
                    $total = $prod['data']['wholesale_price'] * $item['amount_unit'] - $item['discount_amount'];
                } else {
                    $this->selling_price = $prod['data']['retail_price'];
                    $total = $prod['data']['retail_price'] * $item['amount_unit'] - $item['discount_amount'];
                }
                $total_amount = $total_amount + $total;
                $this->product_id = $item['product_id'];
                $this->buying_price = $prod['data']['cost_price'];
                $this->amount_unit = $item['amount_unit'] ?? 1;
                $this->discount_amount = $item['discount_amount'] ?? 0;
                if($this->validate()) {
                    $this->save();
                    $status['sales_product_list'][] = $this->cast(NULL, 0);
                    $inv->saleProduct($data['branch_id'], $item['product_id'], $item['amount_unit']);
                    $this->reset();
                } else {
                    $this->db->rollback();
                }
            } else {
                $this->db->rollback();
            }
        }
        $order = new SalesOrderModel();
        $order->addTotalAmount($sales_id, $total_amount);
        $status['total_amount'] = $total_amount;
        return $status;
    }

    private function totalUnit($product_id, $data, $branch) {
        $to = $data['to'];
        $from = $data['from'];
        $result = $this->db->exec("SELECT SUM(amount_unit) AS total FROM sales_order JOIN sales_product ON 
    sales_order.id=sales_product.sales_order_id WHERE sales_product.product_id=$product_id AND date(sales_order.sold_at)>='" . $from . "' AND date(sales_order.sold_at)<='" . $to . "' AND sales_order.branch_id='" . $branch . "'");
        return $result[0]['total'];
    }

    public function getAll(): array {
        $this->fields(['sales_order_id.id', 'product_id.id', 'product_id.name']);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 1);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All sales product successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No sales product found.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getSales($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            $data = $this->cast(NULL, 0);
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'sales product Successfully Fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid sales product Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function deleteSales($id): array {
        $this->load(['id=?', $id]);
        if($this->id) {
            try {
                $this->erase();
                $data['id'] = $this->id;
                $result['data'] = $data;
                $status['code'] = 1;
                $status['message'] = 'Sales Product Successfully Deleted.';
            } catch(PDOException $e) {
                $status['code'] = 0;
                $status['message'] = $e->errorInfo[2];
            }
        } else {
            $status['code'] = 0;
            $status['message'] = 'Invalid Sales Product Id.';
        }
        $result['status'] = $status;
        return $result;
    }

    public function getProducts($data, $branch): array {
        $prod = new ProductModel();
        $products = [];
        $to = $data['to'];
        $from = $data['from'];
        $result = $this->db->exec("SELECT DISTINCT sales_product.product_id FROM sales_order JOIN sales_product ON
    sales_order.id=sales_product.sales_order_id WHERE date(sales_order.sold_at)>='" . $from . "' AND date(sales_order.sold_at)<='" . $to . "' AND sales_order.branch_id='" . $branch . "' ORDER BY sales_product.product_id DESC");
        for($i=0;$i<count($result);$i++) {
            $products[$i]['product_id'] = $result[$i]['product_id'];
            $products[$i]['product_name'] = $prod->getName($result[$i]['product_id']);
            $products[$i]['buying_price'] = $prod->getBuyingPrice($result[$i]['product_id']);
            $products[$i]['total_units_sold'] = $this->totalUnit($products[$i]['product_id'], $data, $branch);
            $products[$i]['total_selling_price'] = $this->totalSellingPrice($products[$i]['product_id'], $data, $branch);
            $products[$i]['total_profit'] = $products[$i]['total_selling_price'] - ($products[$i]['buying_price'] * $products[$i]['total_units_sold']);
        }
        return $products;
    }

    private function totalSellingPrice($product_id, $data, $branch) {
        $to = $data['to'];
        $from = $data['from'];
        $result = $this->db->exec("SELECT SUM(selling_price*amount_unit) AS total FROM sales_order JOIN sales_product ON 
    sales_order.id=sales_product.sales_order_id WHERE sales_product.product_id=$product_id AND date(sales_order.sold_at)>='" . $from . "' AND date(sales_order.sold_at)<='" . $to . "' AND sales_order.branch_id='" . $branch . "'");
        return $result[0]['total'];
    }

    public function getAmount($data, $product_list) {
        $to = $data['to'];
        $from = $data['from'];
        $sold_amount = 0;
        foreach ($product_list as $product) {
            $result = $this->db->exec("SELECT SUM(selling_price*amount_unit-discount_amount) AS total FROM sales_product JOIN sales_order ON sales_product.sales_order_id=sales_order.id 
                                                               WHERE DATE(sales_order.sold_at)>='" . $from . "' AND date(sales_order.sold_at)<='" . $to . "' AND sales_product.product_id='" . $product . "'");
            $sold_amount = $sold_amount + $result[0]['total'];
        }
        return $sold_amount;
    }

    public function total_sold_amount($data, $product_list)
    {
        $total_sold_count = 0;
        $to = $data['to'];
        $from = $data['from'];
        foreach ($product_list as $product) {
            $result = $this->db->exec("SELECT COUNT(*) AS total FROM sales_product JOIN sales_order ON sales_product.sales_order_id=sales_order.id 
                                                               WHERE DATE(sales_order.sold_at)>='" . $from . "' AND date(sales_order.sold_at)<='" . $to . "' AND sales_product.product_id='" . $product . "'");
            $total_sold_count = $total_sold_count + $result[0]['total'];
        }
        return $total_sold_count;
    }

}