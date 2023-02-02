<?php

class LogModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'log_type_id' => [
            'belongs-to-one' => '\LogTypeModel',
            'type' => \DB\SQL\Schema::DT_TINYINT,
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'log');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function getAll($pageno, $perPage): array {
        $offset = ($pageno - 1) * $perPage;
        $totalRecords = $this->db->exec("SELECT COUNT(*) FROM log")[0]['COUNT(*)'];
        $totalPages = ceil($totalRecords/$perPage);
        $status['total pages'] = $totalPages;
        $status['current page'] = (int)$pageno;
        $result['status'] = $status;
        $result['data'] = $this->db->exec("SELECT * FROM log LIMIT $perPage OFFSET $offset");
        return $result;
    }

    public function getByType($typeid, $pageno, $perPage): array {
        $offset = ($pageno - 1) * $perPage;
        $totalRecords = $this->db->exec("SELECT COUNT(*) FROM log")[0]['COUNT(*)'];
        $totalPages = ceil($totalRecords/$perPage);
        $status['total pages'] = $totalPages;
        $status['current page'] = (int)$pageno;
        $result['status'] = $status;
        $result['data'] = $this->db->exec("SELECT * FROM log WHERE log_type_id=$typeid LIMIT $perPage OFFSET $offset");
        return $result;
    }

    public function getByDate($data, $pageno, $perPage) {
        if($data['start'] !== "" && $data['end'] !== "") {
            $offset = ($pageno - 1) * $perPage;
//            $result['data'] = $this->db->exec("SELECT * FROM log WHERE date(event_time)='" . $time . "' LIMIT $perPage OFFSET $offset");
            $date_start = $data['start'];
            $date_end = $data['end'];
            return $this->afind(['date(event_time)>=? AND date(event_time)<=?', $date_start, $date_end], ['limit'=>$perPage, 'offset'=>$offset], 0, 0);
        }
    }

    public function add($data, $type) {
        $this->log_type_id = $type;
        $this->details = $data;
        $this->save();
    }

}