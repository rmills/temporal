<?php 
namespace UserMod;

if (!defined('USERMOD_AVATAR_HEIGHT')) {
    define('USERMOD_AVATAR_HEIGHT', '400');
}

if (!defined('USERMOD_AVATAR_EDIT_HEIGHT')) {
    define('USERMOD_AVATAR_EDIT_HEIGHT', '150');
}

if (!defined('USERMOD_AVATAR_SQUARE')) {
    define('USERMOD_AVATAR_SQUARE', 0);
}

if (!defined('USERMOD_AVATAR_ALLOWED_TYPES')) {
    define('USERMOD_AVATAR_ALLOWED_TYPES', 'jpg,jpeg');
}


class Avatar extends \UserMod implements \iUserMod{
    public $_tag = '{profile_avatar}';
    public function __construct($uid) {
        parent::__construct(__CLASS__, $uid);
    }
    
    public function update(){
        $this->process_upload();
        $this->save();
    }
    
    public function edit_html(){
        if(is_numeric($this->_data)){
            $avatar = new \Image($this->_data);
        }
        $html[] = '<div class="control-group">';
        $html[] = '<p>'.$avatar->thumbnail(USERMOD_AVATAR_EDIT_HEIGHT, USERMOD_AVATAR_SQUARE).'</p>';
        $html[] = '<label for="avatar">Avatar: </label>';
        $html[] = '<input type="file" name="avatar" value="'.$this->_data.'">';
        $html[] = '</div>';
        return array(implode(PHP_EOL, $html), 50);
    }
    
    public function profile($type){
        if(is_numeric($this->_data)){
            $avatar = new \Image($this->_data);
            return array($avatar->thumbnail(USERMOD_AVATAR_HEIGHT, USERMOD_AVATAR_SQUARE), 20);
        }else{
            return array('<p>no avatar set</p>', 20);
        }
        
    }
    
    public function process_upload(){
        if($_FILES["avatar"]["name"] != ''){
            $allowed_types = explode(',', USERMOD_AVATAR_ALLOWED_TYPES);
            $ext = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION );
            $allow = false;
            foreach($allowed_types as $v){
                if(strtolower($ext) == strtolower($v)){
                    $allow = true;
                }
            }

            if($allow){
                $check = false;
                while(!$check){
                    $new_name = \Crypto::random_filename().'.'.strtolower($ext);
                    $new_image = IMAGE_ORGINAL_PATH.$new_name;
                    if(!is_file($new_image)){
                        $check = true;
                    }
                }
                $sql = '
                INSERT INTO images (
                    `file`,
                    `orginal`,
                    `name`
                ) VALUES (
                    \'' . \DB::clean($new_name) . '\',
                    \'' . \DB::clean($_FILES["avatar"]["name"]) . '\',
                    \'' . \DB::clean(\CMS::$_user->_data['name']) . '\'
                )';
                \DB::q($sql);
                $init_image = new \Image();
                move_uploaded_file($_FILES["avatar"]["tmp_name"], $new_image );
                $this->_data = \DB::$_lastid;
                $this->save();
                \Page\Profile::$_status[] = \Module\Notice::info('Avatar Updated');
            }else{
                \Page\Profile::$_status[] = \Module\Notice::error('Unable to save avatar, file missing or not a valid type (only use: '.USERMOD_AVATAR_ALLOWED_TYPES.')');
            }
        }
    }
}
\CMS::register('Avatar', __NAMESPACE__);