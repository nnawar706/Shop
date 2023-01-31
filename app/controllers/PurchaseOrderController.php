<?php

class PurchaseOrderController extends MainController {

    public function index() {
        $purchase = new PurchaseOrderModel();
        $data = $purchase->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @throws Exception
     */
    public function create() {
        if ($this->f3->VERB == 'POST' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
            $this->f3->set('BODY', file_get_contents('php://input'));
            if (strlen($this->f3->get('BODY'))) {
                $data = json_decode($this->f3->get('BODY'), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $order = new PurchaseOrderModel();
                    $purchase_order = $order->createOrder($data);
                    if ($purchase_order['id'] != 0) {
                        $product = new PurchaseProductModel();
                        $status['status']['code'] = 1;
                        $status['status']['message'] = "order placed";
                        $status['purchase_data'] = $product->createPurchase($data, $purchase_order['id']);
                        if(!in_array("0", $status['purchase_data']['code'])) {
                            $trace = new InventoryTraceModel();
                            $inventory = new InventoryModel();
                            $inv_trace = $trace->createTrace($data, $purchase_order['id']);
                            foreach ($inv_trace as $value) {
                                $status['inventory_data'][] = $inventory->incrementStock($value);
                            }
                        }
                    } else {
                        $status['status']['code'] = 0;
                        $status['status']['message'] = $purchase_order['message'];
                    }
                    header('Content-Type: application/json');
                    echo json_encode($status);
                }
            }
        }
    }

    public function read($f3, $params) {
        $purchase = new PurchaseOrderModel();
        $data = $purchase->getPurchase($params['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

//    /**
//     * @throws Exception
//     */
//    public function update($f3, $params) {
//        if ($this->f3->VERB == 'PUT' && str_contains($this->f3->get('HEADERS[Content-Type]'), 'json')) {
//            $this->f3->set('BODY', file_get_contents('php://input'));
//            if (strlen($this->f3->get('BODY'))) {
//                $data = json_decode($this->f3->get('BODY'),true);
//                if (json_last_error() == JSON_ERROR_NONE) {
//                    $purchase = new PurchaseOrderModel();
//                    $status = $purchase->updatePurchase($params['id'], $data);
//                    header('Content-Type: application/json');
//                    echo json_encode($status);
//                }
//            }
//        }
//    }

    public function delete($f3, $params) {
        $purchase = new PurchaseOrderModel();
        $status = $purchase->deletePurchase($params['id']);
        header('Content-Type: application/json');
        echo json_encode($status);
    }
}