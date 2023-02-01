<?php

class UserDesignationModel extends \DB\Cortex {

    use \Validation\Traits\CortexTrait;

    protected $fieldConf = [
        'user_profile_designation_id' => [
            'has-many' => ['\UserProfileModel','designation_id'],
            'type' => \DB\SQL\Schema::DT_TINYINT
        ]
    ];

    public function __construct() {
        $db = Base::instance()->get('DB');
        parent::__construct($db,'user_designation');
        $vd = Validation::instance();
        $vd->onError(function($text,$key) {
            Base::instance()->set('error_msg', $text);
        });
    }

    public function getAll(): array {
        $this->fields(['user_profile_designation_id'], true);
        $data = $this->afind([], ['order'=>'id DESC'], 0, 0);
        if($data) {
            $result['data'] = $data;
            $status['code'] = 1;
            $status['message'] = 'All user designation successfully fetched.';
        } else {
            $status['code'] = 0;
            $status['message'] = 'No user designation found.';
        }
        $result['status'] = $status;
        return $result;
    }
}