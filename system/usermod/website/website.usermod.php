<?php 
namespace UserMod;
class Website extends \UserMod implements \iUserMod{
    public $_tag = '{profile_website}';
    public function __construct($uid) {
        parent::__construct(__CLASS__, $uid);
    }
    
    public function update(){
        $this->_data = $_POST['website'];
        $this->save();
    }
    
    public function edit_html(){
        $html[] = '<div class="control-group">';
        $html[] = '<label for="website">Website: </label>';
        $html[] = '<input type="text" name="website" value="'.$this->_data.'">';
        $html[] = '</div>';
        return array(implode(PHP_EOL, $html), 50);
    }
    
    public function profile($type){
        return array('<i>Website:</i>'.$this->_data, 20);
    }
}
\CMS::register('Website', __NAMESPACE__);