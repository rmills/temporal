<?php 
namespace UserMod;
class Avatar extends \UserMod implements \iUserMod{
    public $_tag = '{profile_avatar}';
    public function __construct($uid) {
        parent::__construct(__CLASS__, $uid);
    }
    
    public function update(){
        $this->_data = $_POST['avatar'];
        $this->save();
    }
    
    public function edit_html(){
        $html[] = '<div class="control-group">';
        $html[] = '<label for="avatar">Avatar: </label>';
        $html[] = '<input type="file" name="avatar" value="'.$this->_data.'">';
        $html[] = '</div>';
        return array(implode(PHP_EOL, $html), 50);
    }
    
    public function profile($type){
        return array('<img src="'.$this->_data, 20);
    }
}
\CMS::register('Website', __NAMESPACE__);