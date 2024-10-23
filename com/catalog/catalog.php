<?php

///////////////////////////////////////////////////////////////////////////

function AddNewToCatalog($name, $price, $stock){
    $catalogo = GetCatalog();

    $item = $catalogo->addChild('producto');
    $item->addChild('id', GetId());
    $item->addChild('nombre', $name);
    $item->addChild('precio', $price);
    $item->addChild('stock', $stock);

    $catalogo->asXML('xmldb/catalog.xml');

    echo $name . " añadido exitosamente";
}

///////////////////////////////////////////////////////////////////////////

function GetId(){
    $catalogo = GetCatalog();
    $i = 0;

    foreach ($catalogo->producto as $producto) {
        $i = $i + 1;
        if ((int)$producto->id != $i) {
            echo $i;
            return $i;
        }
    }

    return $i+1;
}

/////////////////////////////////////////////////////////////////////////////

function ViewCatalog() {
    $catalog = GetCatalog();

    echo "<br><br>Catalogo: ";

    foreach ($catalog->producto as $producto) {
        echo "<br>id: " . $producto->id;
        echo "<br>producto: " . $producto->nombre;
        echo "<br>precio: " . $producto->precio;
        echo "<br>stock: " . $producto->stock;

        echo "<br>";
    }
}

/////////////////////////////////////////////////////////////////////////////

function DeleteProd($id){
    $catalogo = GetCatalog();
    $Tcatalogo = new SimpleXMLElement('<catalog></catalog>'); 

    foreach ($catalogo->producto as $producto) {
        if (((string)$producto->id != (string)$id)) {
            $item = $Tcatalogo->addChild('producto');
            $item->addChild('id', $producto->id);
            $item->addChild('nombre', $producto->nombre);
            $item->addChild('precio', $producto->precio);
            $item->addChild('stock', $producto->stock);
        } else {
            echo 'producto '.$producto->nombre.' borrado con exito';
        }
    }
  
    $Tcatalogo->asXML('xmldb/catalog.xml');
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

function UpdateStock($id, $quantity, $DorI) {
    $catalog = GetCatalog();

    foreach ($catalog->producto as $producto) {
        if ($producto->id == $id) {
            if ($DorI) {
                $producto->stock = (int)$producto->stock + $quantity;
                echo "<b>Stock actualizado con exito</b>";
            } else {
                if ((int)$producto->stock - $quantity > 0) {
                    $producto->stock = (int)$producto->stock - $quantity;
                } else {
                    $producto->stock = 0;
                }
                    
                echo "<b>Stock actualizado con exito</b>";
            }
        }
    }

    $catalog->asXML('xmldb/catalog.xml');
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