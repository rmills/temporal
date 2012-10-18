<?php 
namespace UserMod;
class Website extends \UserMod implements \iUserMod{
    public function __construct($uid) {
        parent::__construct(__CLASS__, $uid);
    }
    
    public function update(){
        $this->data = $_POST['website'];
        $this->save();
    }
    
    public function edit_html(){
        return '<label for="website">Website: </label><input type="text" name="website">';
    }
    
    public function display_html(){
        return $this->_data;
    }
}
\CMS::register('Website', __NAMESPACE__);