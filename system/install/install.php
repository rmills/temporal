<?php

if(!defined('INSTALL_SNAPSHOT')){
    define('INSTALL_SNAPSHOT', 'sql/install.sql.php');
}

if(!defined('ALLOW_INSTALL')){
    exit('not enabled');
}

$template = file_get_contents('system/install/templates/index.html');
$notice = array();

if(ALLOW_INSTALL){
    include('classes/installer.class.php');
    $check = Installer::checkdb();
    if($check){
        $template = str_replace('{db_status}', 'Connected To DB', $template);
    }else{
        $template = str_replace('{db_status}', 'Could not connect to the DB, Error: '.Installer::$db_error, $template);
    }
    
    if(isset($_POST['install'])){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        if($name == '' || $email == '' || $password == '' ){
            $notice[] = '<div class="warn"><p>Name, Email and Password are required</p></div>';
        }else{
            $try_db = Installer::init_db(INSTALL_SNAPSHOT);
            if($try_db){
                $notice[] = '<div class="notice"><p>The user with the email address "'.$email.'" has been created as a super user with the password "'.$password.'"</p></div>';
                $try_user = Installer::init_user($name, $email, $password);
                if($try_user){
                    $notice[] = '<div class="notice"><p>The user with the email address "'.$email.'" has been created as a super user with the password "'.$password.'"</p></div>';
                }else{
                    $notice[] = '<div class="warn"><p>Could not create user, Error: '.Installer::$db_error.'</p></div>';
                }
            }else{
                $notice[] = '<div class="warn"><p>Could init database, Error: '.Installer::$db_error.'</p></div>';
            }
        }
    }
    
    foreach($notice as $v){
        $template = str_replace('{notice}', $v.'{notice}', $template);
    }
    $template = str_replace('{notice}', '', $template);
    
    $form = file_get_contents('system/install/templates/form.html');
    $template = str_replace('{content}', $form, $template);
    echo $template;
}