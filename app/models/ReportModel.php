<?php

class ReportModel {

    //reports

    public function customerDues($data, $cid): array {
        $customer = new CustomerModel();
        $order = new SalesOrderModel();

        $customer_info = $customer->getCustomer($cid);

        if($customer_info['status']['code'] == 1){
            $due_and_paid = $order->getTotalDueAndPaid($data, $cid);

            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";

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
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = "invalid request";
        }
        return $info;
    }

    public function supplierDues(mixed $data, $sid): array {
        $supplier = new SupplierModel();
        $order = new PurchaseOrderModel();

        $supplier_info = $supplier->getSupplier($sid);

        if($supplier_info['status']['code'] == 1) {
            $due_and_paid = $order->getTotalDueAndPaid($data, $sid);

            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";

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
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = "invalid request";
        }
        return $info;
    }

    public function getPurchases($data, $cid): array {
        $customer = new CustomerModel();
        $orders = new SalesOrderModel();

        $customer_info = $customer->getCustomer($cid);

        if($customer_info['status']['code'] == 1) {
            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";

            $info['data']['customer_id'] = $cid;
            $info['data']['name'] = $customer_info['data']['name'];
            $info['data']['orders'] = $orders->getOrders($data, $cid);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = "invalid request";
        }
        return $info;
    }

    public function getProductSales($data): array {
        $branch = new BranchModel();
        $order = new SalesOrderModel();
        $products = new SalesProductModel();

        $branch_info = $branch->getBranch($data['branch_id']);
        $orders = $order->getSalesOrders($data);
        $sales_product = $products->getTotalProduct($orders);

        if($branch_info['status']['code'] == 1) {
            $info['data']['branch_id'] = $data['branch_id'];
            $info['data']['name'] = $branch_info['data']['name'];
            $info['data']['products'] =
            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";

            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
        }

        return $info;
    }

    public function getPerformance($data, $sid): ?array {
        $user = new UserModel();
        $order = new SalesOrderModel();

        $salesman = $user->getSalesmanInfo($sid);

        if($salesman != null) {
            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";

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
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = "invalid request";
        }
        return $info;
    }

    public function getSupplierSales($data, $sid): array {
        $supplier = new SupplierModel();
        $orders = new PurchaseOrderModel();

        $supplier_info = $supplier->getSupplier($sid);

        if($supplier_info['status']['code'] == 1) {
            $info['status']['code'] = 1;
            $info['status']['message'] = "request successful";

            $info['data']['supplier_id'] = $supplier_info['data']['id'];
            $info['data']['name'] = $supplier_info['data']['name'];
            $info['data']['orders'] = $orders->getOrders($data, $sid);
            $info['data']['from'] = $data['from'];
            $info['data']['to'] = $data['to'];
        } else {
            $info['status']['code'] = 0;
            $info['status']['message'] = "invalid request";
        }
        return $info;
    }
}