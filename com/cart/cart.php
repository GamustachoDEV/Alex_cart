<?php

///////////////////////////////////////////////////////////////////////////

function AddToCart($user, $id_prod, $quantity){
    echo GetName($id_prod);

    if (ExistProduct($id_prod)){
        AddNewToCart($user, $id_prod, $quantity);
    } else {
        echo' no existe ese producto';
    }
}

///////////////////////////////////////////////////////////////////////////

function AddNewToCart($user, $id_prod, $quantity){
    $cart = GetCart($user);

    if (!NewQuantity($user, $cart, $id_prod, $quantity, True)) {
        if (UpdateStock($id_prod, $quantity, True)){
            $item = $cart->addChild('product_item');
            $item->addChild('id_product', $id_prod);
            $item->addChild('quantity', $quantity);

            $item_price = $item->addChild('price_item');
            $iva = GetIVA($item);
            $item_price->addChild('price', Addprice($user, $cart, $id_prod, (int)$quantity, GetIVA('EU')));
            $item_price->addChild('currency', 'EU');

            $cart->asXML('xmldb/' . $user . 'cart.xml');

            echo " añadido exitosamente";
        }
    }
}

///////////////////////////////////////////////////////////////////////////

function DeleteFromCart($user, $id_prod, $quantity){
    $cart = GetCart($user);

    if (ExistProduct($id_prod)){
        NewQuantity($user, $cart, $id_prod, $quantity, False);
    } else {
        echo' no existe ese producto';
    }
}

///////////////////////////////////////////////////////////////////////////

function DeleteCart($user){
    $file = 'xmldb/' . $user . 'cart.xml';

    if (file_exists($file)) {
        if (unlink($file)) {
            echo "El carrito ha sido vaciado.";
        } else {
            echo "Error al  vaciar el carrito.";
        }
    } else {
        echo "El carrito todavia no se a creado.";
    }
}

/////////////////////////////////////////////////////////////////////////////

function ViewCart($user) {
    $cart = GetCart($user);
    $total = 0;
    $m = '€';

    echo "<br><br>Carrito: ";

    foreach ($cart->product_item as $producto) {
        $id = (int)$producto->id_product;
        echo "<br>id: " . $producto->id_product;
        echo "<br>producto: " . GetName($id);
        echo "<br>cantidad: " . $producto->quantity;
        echo "<br>precio: " . $producto->price_item->price;
        $total = $total + (int)$producto->price_item->price;
        $currency = $producto->price_item->currency;
        switch ($currency) {
            case 'EU':
                $m = '€';
                echo $m;
                break;

            case 'DOL':
                $m = '$';
                echo $m;
                break;
        
            default:
            echo " ";
        }

        echo "<br>";
    }

    echo "<br>";
    echo 'total: '. $total . $m;
}

/////////////////////////////////////////////////////////////////////////////

function GetIVA($currency){
    switch ($currency) {
        case 'EU':
            $iva = 1.21;
            break;

        case 'DOL':
            $iva = 1;
            break;
    
        default:
            $iva = 1;
    }

    return $iva;
}

/////////////////////////////////////////////////////////////////////////////

function NewQuantity($user, $cart, $id_prod, $quantity, $DorI){
    $exists = False;

    foreach ($cart->product_item as $producto) {
        $currency = $producto->price_item->currency;
        $iva = GetIVA($currency);
        if ($producto->id_product == $id_prod) {
            if ($DorI) {
                if (UpdateStock($id_prod, $quantity, $DorI)) {
                    $producto->quantity = (int)$producto->quantity + $quantity;
                    $producto->price_item->price = Addprice($user, $cart, $id_prod, (int)$producto->quantity, $iva);
                }
                $exists = True;
            } else {
                $producto->quantity = (int)$producto->quantity - $quantity;
                UpdateStock($id_prod, $quantity, $DorI);
                $producto->price_item->price = Addprice($user, $cart, $id_prod, $producto->quantity, $iva);
                if ((int)$producto->quantity <= 0) {
                    echo "borrar";
                    DeleteProd($user, $id_prod);
                    return $exists;
                }
            }
        }
    }

    $cart->asXML('xmldb/' . $user . 'cart.xml');

    return $exists;
}

/////////////////////////////////////////////////////////////////////////////

function DeleteProd($user, $id_prod){
    $cart = GetCart($user);
    $Tcart = new SimpleXMLElement('<' . $user . 'cart></' . $user . 'cart>'); 

    foreach ($cart->product_item as $producto) {
        if (((string)$producto->id_product != (string)$id_prod)) {
            $nuevoProducto = $Tcart->addChild('product_item');
            $nuevoProducto->addChild('id_product', $producto->id_product);
            $nuevoProducto->addChild('quantity', $producto->quantity);
            
            // Añadir el precio y la moneda
            $nuevoPrecio = $nuevoProducto->addChild('price_item');
            $nuevoPrecio->addChild('price', $producto->price_item->price);
            $nuevoPrecio->addChild('currency', $producto->price_item->currency);
        } else {
            echo 'prodcuto '.GetName($id_prod).' borrado con exito';
        }
    }
  
    $Tcart->asXML('xmldb/' . $user . 'cart.xml');
}

/////////////////////////////////////////////////////////////////////////////

function ExistProduct($id_prod){
    $catalog = GetCatalog();
    $exists = False;

    foreach ($catalog->producto as $producto) {
        if ($producto->id == $id_prod) {
            $exists = True;
        }
    }

    return $exists;
}

/////////////////////////////////////////////////////////////////////////////

function UpdateStock($id_prod, $quantity, $DorI) {
    $catalog = GetCatalog();

    foreach ($catalog->producto as $producto) {
        if ($producto->id == $id_prod) {
            if ($DorI) {
                if ((int)$producto->stock-$quantity<0) {
                    echo "<b> no hay stock </b>";
                    return False;
                } else {
                    $producto->stock = (int)$producto->stock - $quantity;
                }
            } else {
                $producto->stock = (int)$producto->stock + $quantity;
            }
        }
    }

    $catalog->asXML('xmldb/catalog.xml');
    
    return True;
}

/////////////////////////////////////////////////////////////////////////////

function AddPrice($user, $cart, $id_prod, $quantity, $iva) {
    $catalog = GetCatalog();

    foreach ($catalog->producto as $producto) {
        if ($producto->id == $id_prod) {
            $price_final = ((int)$producto->precio * (int)$quantity) * $iva;
            return $price_final;
        }
    }
}

/////////////////////////////////////////////////////////////////////////////

function Discount($user, $code){ 
    $discounts = GetDiscounts();

    foreach ($discounts->descuento as $act_discount) {
        if ($act_discount->codigo == $code) {
            $cart = GetCart($user);
            foreach ($cart->product_item as $producto) {
                if ((int)$producto->id_product == (int)$act_discount->id_prod) {
                    $producto->price_item->price = ((int)$producto->price_item->price*80)/100;
                    DeleteDiscount($code);
                    $cart->asXML('xmldb/' . $user . 'cart.xml');
                    break;
                } else {
                    echo 'producto no añadido al carrito';
                }
            }
        } else {
            echo 'descuento no encontrado';
        }
    }
}



/////////////////////////////////////////////////////////////////////////////

function DeleteDiscount($code){
    $discounts = GetDiscounts();
    $Tdiscounts = new SimpleXMLElement('<discounts></discounts>'); 

    foreach ($discounts->descuento as $descuento) {
        if (((int)$descuento->codigo != (int)$code)) {
            $descuento_p = $Tdiscounts->addChild('descuento');
            $descuento_p->addChild('codigo', $descuento->codigo);
            $descuento_p->addChild('id_prod', $descuento->id_prod);
            $descuento_p->addChild('porcentaje', $descuento->porcentaje);
        }
    }
  
    $Tdiscounts->asXML('xmldb/discounts.xml');
}

/////////////////////////////////////////////////////////////////////////////

function GetName($id_prod) {
    $catalog = GetCatalog();

    foreach ($catalog->producto as $producto) {
        if ($producto->id == $id_prod) {
            return $producto->nombre;
        }
    }
}

/////////////////////////////////////////////////////////////////////////////

function GetDiscounts(){ 
    $file='xmldb/discounts.xml';

    if (file_exists($file)) {
        $discounts = simplexml_load_file($file); 
    } else {
        echo "No hay descuentos disponibles.";
    }

    return $discounts;
}

/////////////////////////////////////////////////////////////////////////////

function GetCart($user){ 
    $file='xmldb/' . $user . 'cart.xml';

    if (file_exists($file)) {
        $cart = simplexml_load_file($file); 
    } else {
        $cart = new SimpleXMLElement('<' . $user . 'cart></' . $user . 'cart>'); 
    }

    return $cart;
}

/////////////////////////////////////////////////////////////////////////////

function GetCatalog(){ 
    $file='xmldb/catalog.xml';

    if (file_exists($file)) {
        $catalog = simplexml_load_file($file); 
    } else {
        $catalog = new SimpleXMLElement('<catalog></catalog>'); 
    }

    return $catalog;
}

//////////////////////////////////////////////////////////////////////////

?>