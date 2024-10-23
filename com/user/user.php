<?php

//////////////////////////////////////////////////////////////////////////

function UserRegister($dni, $name, $password){
    echo "UserRegister <br>";

    $users = GetUser();

    $user_ex = 1;

    foreach ($users->user as $user) {
        if ($user->name == $name) {
            $user_ex = 0;
        }
    }

    if ($user_ex == 1) {
        $user = $users->addChild('user');
        $user->addChild('DNI', $dni);
        $user->addChild('name', $name);
        $user->addChild('password', $password);

        $users->asXML('xmldb/users.xml');

        echo "Usuario registrado con exito";
    } else {
        echo "El usuario ya existe";
    }
    
}

//////////////////////////////////////////////////////////////////////////

function UserExists($UserGive, $passwd){
    $users = GetUser();

    foreach ($users->user as $user) {
        if ($user->name == $UserGive & $user->password == $passwd) {
            return True;
        }
    }

    return False;
}

//////////////////////////////////////////////////////////////////////////

function GetUser(){
    $file='xmldb/users.xml';

    if (file_exists($file)) {
        $users = simplexml_load_file($file); 
    } else {
        $users = new SimpleXMLElement('<users></users>'); 
    }

    return $users;
}

//////////////////////////////////////////////////////////////////////////

?>