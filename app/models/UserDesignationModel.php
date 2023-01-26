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
}