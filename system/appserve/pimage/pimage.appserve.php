<?php
namespace Appserve;
class Pimage extends Appserve{
    public static function __registar_callback() {
        Appserve::register('Pimage');
    }
    
    public static function call(){
        $ext = pathinfo($_FILES['qqfile']['name'], PATHINFO_EXTENSION);
        
        $name = \Image::random_filename($ext);
        $fullname = IMAGE_ORGINAL_PATH.$name;
        
        move_uploaded_file($_FILES['qqfile']['tmp_name'], $fullname);
        $image = new \Image();
        $id = $image->create($name);
        $newimage = new \Image($id);
        $html = $newimage->thumbnail( constant('PIMAGE_'.$_POST['zone'].'_H'), constant('PIMAGE_'.$_POST['zone'].'_SQUARE') );
        self::set($_POST['zone'], $id);
        $data = array('success'=>true, 'html'=>$html);
        return $data;
    }
    
    private static function set($zone, $image_id){
        $sql = '
            INSERT INTO pimage (
                `zone`,
                `image`
            ) VALUES (
                \'' . \DB::clean($zone) . '\',
                \'' . \DB::clean($image_id) . '\'
            )';
        \DB::q($sql);
    }
}
