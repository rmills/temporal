<?php

class UserMod{
    protected $_table = false;
    protected $_uid = false;
    protected $_data = false;
    protected $_class = false;
    
    function __construct($class, $uid) {
        $this->_uid = $uid; 
        $this->_class = $class; 
        return $this->init();
    }
    
    function init(){
        $sql = 'SELECT data FROM `usermod` WHERE `mid` = "'.DB::clean($this->_class).'" AND `uid` = "'.DB::clean($this->_uid).'" LIMIT 1';
        $response = DB::q($sql);
        if(count($response)){
            $this->_data = unserialize($response[0]['data']);
        }else{
            $this->init_db_data();
        }
    }
    
    function init_db_data(){
        $sql = '
            INSERT INTO `usermod` (
                `mid`,
                `uid`,
                `data`
            ) VALUES (
                \''.DB::clean( $this->_class ).'\',
                \''.DB::clean( $this->_uid ).'\',
                \''.DB::clean( serialize($this->_data) ).'\'
            )';
        DB::q( $sql );
        $this->_data = $this->_data;
    }
    
    public function save(){
        $sql = 'UPDATE `usermod` SET `data` = \''.DB::clean(  serialize($this->_data) ).'\' WHERE `mid` = "'.DB::clean($this->_class).'" AND `uid` = "'.DB::clean($this->_uid).'" LIMIT 1';
        DB::q( $sql );
    }
}

interface iUserMod
{
    public function update();
    public function edit_html();
    public function profile($type);
}
