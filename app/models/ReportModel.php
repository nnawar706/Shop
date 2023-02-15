<?php

class ReportModel {

    //reports

    public function customerDues($data, $cid): array {
        $customer = new CustomerModel();
        $order = new SalesOrderModel();

        $customer_info = $customer->getCustomer($cid);

        if($customer_info['status']['code'] == 1){
            $due_and_paid = $order->getTotalDueAndPaid($data, $cid);

            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";

            $info['data']['customer_id'] = $cid;
            $info['data']['name'] = $customer_info['data']['name'];
            $info['data']['total_ordered'] = $due_and_paid['total'];
            $info['data']['total_paid'] = $due_and_paid['paid'];
            $info['data']['total_due'] = $due_and_paid['due'];
            $info['data']['total_number_of_orders'] = $order->getTotalOrders($data, $cid);
            $info['data']['total_completed_orders'] = $order->completedOrders($data, $cid);
            $info['data']['total_pending_orders'] = $info['data']['total_number_of_orders'] - $info['data']['total_completed_orders'];
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];

            $info1['data'][0] = $info['data'];
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function allCustomerDues($data)
    {
        $customer = new CustomerModel();
        $order = new SalesOrderModel();

        $allCustomers = $customer->getAllIds();
        for($i=0;$i<count($allCustomers);$i++) {
            $customer_info = $customer->getCustomer($allCustomers[$i]);

            $due_and_paid = $order->getTotalDueAndPaid($data, $allCustomers[$i]);

            $info['data']['customer_id'] = $allCustomers[$i];
            $info['data']['name'] = $customer_info['data']['name'];
            $info['data']['total_ordered'] = $due_and_paid['total'];
            $info['data']['total_paid'] = $due_and_paid['paid'];
            $info['data']['total_due'] = $due_and_paid['due'];
            $info['data']['total_number_of_orders'] = $order->getTotalOrders($data, $allCustomers[$i]);
            $info['data']['total_completed_orders'] = $order->completedOrders($data, $allCustomers[$i]);
            $info['data']['total_pending_orders'] = $info['data']['total_number_of_orders'] - $info['data']['total_completed_orders'];
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];

            $info1['data'][] = $info['data'];
        }
        $info1['status']['code'] = 1;
        $info1['status']['message'] = "request successful";
        return $info1;
    }

    public function supplierDues(mixed $data, $sid): array {
        $supplier = new SupplierModel();
        $order = new PurchaseOrderModel();

        $supplier_info = $supplier->getSupplier($sid);
        if($supplier_info['status']['code'] == 1) {
            $due_and_paid = $order->getTotalDueAndPaid($data, $sid);
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";
            $info['data']['customer_id'] = $sid;
            $info['data']['name'] = $supplier_info['data']['name'];
            $info['data']['total_ordered'] = $due_and_paid['total'];
            $info['data']['total_paid'] = $due_and_paid['paid'];
            $info['data']['total_due'] = $due_and_paid['due'];
            $info['data']['total_number_of_orders'] = $order->getTotalOrders($data, $sid);
            $info['data']['total_completed_orders'] = $order->completedOrders($data, $sid);
            $info['data']['total_pending_orders'] = $info['data']['total_number_of_orders'] - $info['data']['total_completed_orders'];
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][0] = $info['data'];
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function allSupplierDues($data): array {
        $supplier = new SupplierModel();
        $order = new PurchaseOrderModel();
        $allSuppliers = $supplier->getAllIds();
        $info1['status']['code'] = 1;
        $info1['status']['message'] = "request successful";
        for($i=0;$i<count($allSuppliers);$i++) {
            $supplier_info = $supplier->getSupplier($allSuppliers[$i]);
            $due_and_paid = $order->getTotalDueAndPaid($data, $allSuppliers[$i]);
            $info['data']['name'] = $supplier_info['data']['name'];
            $info['data']['total_ordered'] = $due_and_paid['total'];
            $info['data']['total_paid'] = $due_and_paid['paid'];
            $info['data']['total_due'] = $due_and_paid['due'];
            $info['data']['total_number_of_orders'] = $order->getTotalOrders($data, $allSuppliers[$i]);
            $info['data']['total_completed_orders'] = $order->completedOrders($data, $allSuppliers[$i]);
            $info['data']['total_pending_orders'] = $info['data']['total_number_of_orders'] - $info['data']['total_completed_orders'];
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][] = $info['data'];
        }
        return $info1;
    }

    public function getPurchases($data, $cid): array {
        $customer = new CustomerModel();
        $orders = new SalesOrderModel();
        $customer_info = $customer->getCustomer($cid);
        if($customer_info['status']['code'] == 1) {
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";
            $info['data']['customer_name'] = $customer_info['data']['name'];
            $info['data']['total_order_cost'] = $orders->getOrderCost($data, $cid);
            $info['data']['total_discount'] = $orders->getDiscount($data, $cid);
            $info['data']['product_list'] = $orders->getProductList($data, $cid);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];

            $info1['data'][] = $info['data'];
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function getAllPurchases($data): array {
        $customer = new CustomerModel();
        $orders = new SalesOrderModel();
        $allCustomers = $customer->getAllIds();
        for($i=0;$i<count($allCustomers);$i++) {
            $customer_info = $customer->getCustomer($allCustomers[$i]);
            $info['data']['customer_name'] = $customer_info['data']['name'];
            $info['data']['total_order_cost'] = $orders->getOrderCost($data, $allCustomers[$i]);
            $info['data']['total_discount'] = $orders->getDiscount($data, $allCustomers[$i]);
            $info['data']['product_list'] = $orders->getProductList($data, $allCustomers[$i]);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][] = $info['data'];
        }
        $info1['status']['code'] = 1;
        $info1['status']['message'] = "request successful";
        return $info1;
    }

    public function getProductSales($data): array {
        $branch = new BranchModel();
        $products = new SalesProductModel();
        $branch_info = $branch->getBranch($data['branch_id']);
        if($branch_info['status']['code'] == 1) {
            $info['data']['branch_name'] = $branch_info['data']['name'];
            $info['data']['products'] = $products->getProducts($data, $branch_info['data']['id']);
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][] = $info['data'];
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function getAllProductSales($data): array {
        $branch = new BranchModel();
        $products = new SalesProductModel();
        $allBranch = $branch->getAllIds();
        for($i=0;$i<count($allBranch);$i++) {
            $branch_info = $branch->getBranch($allBranch[$i]);
            $info['data']['branch_name'] = $branch_info['data']['name'];
            $info['data']['products'] = $products->getProducts($data, $branch_info['data']['id']);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][] = $info['data'];
        }
        $info1['status']['code'] = 1;
        $info1['status']['message'] = "request successful";
        return $info1;
    }

    public function getPerformance($data, $sid): ?array {
        $user = new UserModel();
        $order = new SalesOrderModel();
        $salesman = $user->getSalesmanInfo($sid);
        if($salesman != null) {
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";

            $info['data']['id'] = $salesman[0]['id'];
            $info['data']['name'] = $salesman[0]['profile_user_id']['name'];
            $info['data']['target_sales_kpi'] = $salesman[0]['sales_kpi_user_id']['target_sales_volume'];
            $info['data']['total_completed_kpi'] = $order->getCompletedKpi($data, $sid);
            if ($info['data']['target_sales_kpi'] > $info['data']['total_completed_kpi']) {
                $info['data']['kpi_status'] = "not completed";
            } else if ($info['data']['target_sales_kpi'] == $info['data']['total_completed_kpi']) {
                $info['data']['kpi_status'] = "completed";
            } else {
                $info['data']['kpi_status'] = "target kpi exceeded";
            }
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];

            $info1['data'][0] = $info['data'];
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function getAllPerformance($data): array {
        $user = new UserModel();
        $order = new SalesOrderModel();
        $kpi = new SalesKpiModel();
        $allUsers = $kpi->getAllUserIds();
        for($i=0;$i<count($allUsers);$i++) {
            $salesman = $user->getSalesmanInfo($allUsers[$i]);

            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";

            $info['data']['salesman_id'] = $salesman[0]['id'];
            $info['data']['name'] = $salesman[0]['profile_user_id']['name'];
            $info['data']['target_sales_kpi'] = $salesman[0]['sales_kpi_user_id']['target_sales_volume'];
            $info['data']['total_completed_kpi'] = $order->getCompletedKpi($data, $allUsers[$i]);
            if ($info['data']['target_sales_kpi'] > $info['data']['total_completed_kpi']) {
                $info['data']['kpi_status'] = "not completed";
            } else if ($info['data']['target_sales_kpi'] == $info['data']['total_completed_kpi']) {
                $info['data']['kpi_status'] = "completed";
            } else {
                $info['data']['kpi_status'] = "target kpi exceeded";
            }
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];

            $info1['data'][] = $info['data'];
        }
        return $info1;
    }

    public function getSupplierSales($data, $sid): array {
        $supplier = new SupplierModel();
        $orders = new PurchaseOrderModel();

        $supplier_info = $supplier->getSupplier($sid);

        if($supplier_info['status']['code'] == 1) {
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";

            $info['data']['supplier_name'] = $supplier_info['data']['name'];
            $info['data']['total_purchase_cost'] = $orders->getTotalCost($data, $sid);
            $info['data']['product_list'] = $orders->getProductList($data, $sid);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][] = $info['data'];
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function getAllSupplierSales($data): array {
        $supplier = new SupplierModel();
        $orders = new PurchaseOrderModel();
        $allSuppliers = $supplier->getAllIds();
        for($i=0;$i<count($allSuppliers);$i++) {
            $customer_info = $supplier->getSupplier($allSuppliers[$i]);
            $info['data']['supplier_name'] = $customer_info['data']['name'];
            $info['data']['total_purchase_cost'] = $orders->getTotalCost($data, $allSuppliers[$i]);
            $info['data']['product_list'] = $orders->getProductList($data, $allSuppliers[$i]);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
            $info1['data'][] = $info['data'];
        }
        $info1['status']['code'] = 1;
        $info1['status']['message'] = "request successful";
        return $info1;
    }

    public function categoryWiseProduct($data): array {
        $product= new ProductModel();
        $info['status']['code'] = 1;
        $info['status']['message'] = "request successful";
        $info['data'] = $product->getList($data);
        return $info;
    }

    public function revenue($id): array {
        $order = new SalesOrderModel();

        $data = ($id == 1) ? $this->getYearCount() : (($id == 2) ? $this->getMonthCount() : (($id == 3) ? $this->getDayCount() : 1));

        if($id == 1 || $id == 2 || $id == 3) {
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";
            $info1['data'][] = $order->getData($data);
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function revenueByBranch($branch, $id): array {
        $order = new SalesOrderModel();
        $data = ($id == 1) ? $this->getYearCount() : (($id == 2) ? $this->getMonthCount() : (($id == 3) ? $this->getDayCount() : 1));
        if($id == 1 || $id == 2 || $id == 3) {
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";
            $info1['data'][] = $order->getDataByBranch($data, $branch);
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    public function revenueByAllBranch($id): array {
        $order = new SalesOrderModel();
        $branch = new BranchModel();
        $data = ($id == 1) ? $this->getYearCount() : (($id == 2) ? $this->getMonthCount() : (($id == 3) ? $this->getDayCount() : 1));
        if($id == 1 || $id == 2 || $id == 3) {
            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";
            $allBranch = $branch->getAllIds();
            for($i=0;$i<count($allBranch);$i++) {
                $branch_info = $branch->getBranch($allBranch[$i]);
                $info['data'][$i] = $order->getDataByBranch($data, $allBranch[$i]);
                $info['data'][$i]['branch_name'] = $branch_info['data']['name'];
            }
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = "invalid request";
        }
        return $info;
    }

    public function revenueByShop($shop, $id): array {
        $order = new SalesOrderModel();
        $data = ($id == 1) ? $this->getYearCount() : (($id == 2) ? $this->getMonthCount() : (($id == 3) ? $this->getDayCount() : 1));
        if($id == 1 || $id == 2 || $id == 3) {
            $info1['status']['code'] = 1;
            $info1['status']['message'] = "request successful";
            $info1['data'][] = $order->getDataByShop($data, $shop);
        } else {
            $info1['status']['code'] = 0;
            $info1['status']['message'] = "invalid request";
        }
        return $info1;
    }

    private function getYearCount(): array {
        $data['from'] = date("Y-m-d", (time() - (60 * 60 * 24 * 365)));
        $data['to'] = date("Y-m-d");
        return $data;
    }

    private function getMonthCount(): array {
        $data['from'] = date("Y-m-d", (time() - (60 * 60 * 24 * 30)));
        $data['to'] = date("Y-m-d");
        return $data;
    }

    private function getDayCount(): array {
        $data['from'] = date("Y-m-d", (time() - (60 * 60 * 24)));
        $data['to'] = date("Y-m-d");
        return $data;
    }


}