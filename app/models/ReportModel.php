<?php

class ReportModel {

    public function getDues($data, $cid): array {

        $customer = new CustomerModel();
        $order = new SalesOrderModel();

        $customer_info = $customer->getCustomer($cid);
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
        return $info;
    }

    public function getPurchases($data, $cid): array {
        $customer = new CustomerModel();
        $order = new SalesOrderModel();

        $customer_info = $customer->getCustomer($cid);

        $info['status']['code'] = 1;
        $info['status']['message'] = "request successful";

        $info['data']['customer_id'] = $cid;
        $info['data']['name'] = $customer_info['data']['name'];
        $info['data']['orders'] = $order->getOrders($data, $cid);
        $info['data']['from'] = $data['from'];
        $info['data']['to'] = $data['to'];
        return $info;
    }
}