<?php
require_once __DIR__."/../../config/db.php";

// Funciones para obtener todos los productos
function obtenerproductos($conn){
    $sql="SELECT * FROM productos";
    $result=$conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// obtener productos por sus respectivas ID
function obtenerproduxID($conn,$id_producto){
    $sql="SELECT * FROM productos WHERE id_producto = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("i",$id_producto);
    $stmt->execute();
    return $stmt -> get_results()->fetch_assoc();
}

//AGREGAR PRODUCTOS (PARA ADMINISTRADOR)
function agregarproducto($conn,$nombre,$descripcion,$precio,$stock,$imagen){
    $sql="INSERT INTO productos(nombre,descripcion,precio,stock,imagen) VALUES (?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssdis",$nombre,$descripcion,$precio,$stock,$imagen);
    return $stmt ->execute();
}

//EDITAR PRODUCTOS (PARA ADMINISTRADOR)
function ediatrproducto($conn,$id_producto,$nombre,$descripcion,$precio,$stock,$imagen){
    $sql="UPDATE productos
        SET nombre=?, descripcion=?, precio=?, stock=?, imagen=?
        WHERE id_producto=?";
    $stmt=$conn ->prepare($sql);
    $stmt->bind_param("ssdisi",$nombre,$descripcion,$precio,$stock,$imagen, $id_producto);
    return $stmt->execute();
}

//ELIMINAR PRODUCTO(PARA ADMINISTRADOR)
function eliminarproducto($conn,$id_producto){
$sql="DELETE FROM productos WHERE id_producto =?";
$stmt=$conn->prepare($sql);
$stmt->bind_param("i",$id_producto);
return $stmt->execute();
}
?>