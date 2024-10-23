<?php

    //Login de ususario Alex
    //http://localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra

    //Registro de usuario Nombre
    //http://localhost:40080/M4_UF1_control_carrito/main.php?login=register&user=Nombre&password=contra&dni=1234567J

    $login = $_GET['login'];

    if ($login === 'login') {
        $user = $_GET['user'];
        $passwd = $_GET['password'];
        include_once("com/user/user.php");

        if (UserExists($user, $passwd)) {
            echo "Usuario: ";
            echo "<b>" . $user . "</b>";
            echo "<br><br>";

            $action = $_GET['action'];
            include_once("com/cart/cart.php");

            switch ($action) {
                case 'add_to_cart':
                    $id = $_GET['id'];
                    $quantity = $_GET['quantity'];

                    AddToCart($user, $id, (int)$quantity);
                    ViewCart($user);
                    break;

                case 'delete_quantity':
                    $id = $_GET['id'];
                    $quantity = $_GET['quantity'];

                    DeleteFromCart($user, $id, (int)$quantity);
                    ViewCart($user);
                    break;

                case 'delete_cart':
                    DeleteCart($user);
                    break;

                case 'delete_product':
                    $id = $_GET['id'];

                    DeleteProd($user, $id);
                    ViewCart($user);
                    break;

                case 'discount':
                    $code = $_GET['discount_code'];
    
                    Discount($user, $code);
                    ViewCart($user);
                    break;

                case 'view_cart':
                    ViewCart($user);
                    break;

                default:
                    echo "invalid action;";
            }
        } else if ($user=='ALEX' && $passwd=='ALEX') {
            $action = $_GET['action'];
            include_once("com/catalog/catalog.php");

            echo "<b> Administrador </b>";
            echo "<br><br>";

            switch ($action) {
                case 'add_to_catalog':
                    $name = $_GET['name'];
                    $price = $_GET['price'];
                    $stock = $_GET['stock'];

                    AddNewToCatalog($name, $price, $stock);
                    ViewCatalog();
                    break;
                
                case 'add_stock':
                    $id = $_GET['id'];
                    $quantity = $_GET['quantity'];

                    UpdateStock($id, $quantity, True);
                    ViewCatalog();
                    break;

                case 'take_stock':
                    $id = $_GET['id'];
                    $quantity = $_GET['quantity'];
    
                    UpdateStock($id, $quantity, False);
                    ViewCatalog();
                    break;

                case 'delete_product':
                    $id = $_GET['id'];
    
                    DeleteProd($id);
                    ViewCatalog();
                    break;

                case 'view_catalog':
                    ViewCatalog();
                    break;

                default:
                    echo "invalid action;";
            }
        } else {
            echo "El Usuario no ha sido encontrado.";
        }
    } else if ($login === 'register') {
        $dni = $_GET['dni'];
        $user = $_GET['user'];
        $password = $_GET['password'];
            
        UserRegister($dni, $user, $password);
    } else {
        echo "<b>Comando no identificado</b>";
    }

    //Vista de carro de Usuario Alex
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra&action=view_cart

    //Añadir producto a carro de Usuario Alex
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra&action=add_to_cart&id=1&quantity=1

    //Quitar cantidad de carro de Usuario Alex
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra&action=delete_quantity&id=1&quantity=1

    //Quitar producto de carro de Usuario Alex
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra&action=delete_product&id=1

    //Utilizacion de descuento de carro de Usuario Alex
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra&action=discount&discount_code=1234

    //Delete de carro de Usuario Alex
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=Alex&password=contra&action=delete_cart


    //Vista de catalogo
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=ALEX&password=ALEX&action=view_catalog

    //Añadir producto al catalogo
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=ALEX&password=ALEX&action=add_to_catalog&name=Super Mario Odyssey&price=45&stock=25

    //Añadir stock producto al catalogo
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=ALEX&password=ALEX&action=add_stock&id=5&quantity=10

    //Quitar stock producto al catalogo
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=ALEX&password=ALEX&action=take_stock&id=5&quantity=10

    //Eliminar producto al catalogo
    //localhost:40080/M4_UF1_control_carrito/main.php?login=login&user=ALEX&password=ALEX&action=delete_product&id=5
?>