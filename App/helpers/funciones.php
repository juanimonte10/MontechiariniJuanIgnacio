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
    return $stmt -> get_result()->fetch_assoc();
}

//AGREGAR PRODUCTOS (PARA ADMINISTRADOR)
function verificarProductoExistente($conn, $nombre) {
    $sql = "SELECT COUNT(*) as total FROM productos WHERE nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] > 0;
}

function agregarproducto($conn,$nombre,$descripcion,$precio,$stock,$imagen){
    // Verificar si el producto ya existe
    if (verificarProductoExistente($conn, $nombre)) {
        return "duplicado";
    }
    
    $sql="INSERT INTO productos(nombre,descripcion,precio,stock,imagen) VALUES (?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssdis",$nombre,$descripcion,$precio,$stock,$imagen);
    return $stmt->execute();
}

//EDITAR PRODUCTOS (PARA ADMINISTRADOR)
function editarproducto($conn,$id_producto,$nombre,$descripcion,$precio,$stock,$imagen){
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

//FUNCIONES DEL USUARIO (GENERAL)
// REGISTRAR CLIENTE
function registrarCliente($conn, $nombre, $email, $password) {
    $rol="cliente";
    $sql = "INSERT INTO usuarios (nombre, email, password,rol) VALUES (?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $password,$rol);
    return $stmt->execute();
}

//REGISTRAR ADMIN
function registraradmin($conn,$nombre,$email,$password){
    $rol="admin";
    $sql="INSERT INTO usuarios (nombre,email, password, rol) VALUES(?, ?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssss",$nombre, $email, $password, $rol);
    return $stmt->execute();
}

// LOGIN USUARIO (LO REDUZCO PARA ADMIN Y CLIENTE)
function loginusuarios($conn, $email, $password) {
    $sql = "SELECT * FROM usuarios WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($password, $usuario['password'])) {
        return $usuario;
    }
    return false;
}


//FUNCIONES PARA EL CARRITO
//AGREGAR AL CARRITO (TIPO UNA SESSION)
function agregaralcarrito($id_producto,$cantidad){
    if(!isset($_SESSION['carrito'])){
        $_SESSION['carrito']=[];
    }
    if (isset($_SESSION['carrito'][$id_producto])){
        $_SESSION['carrito'][$id_producto] += $cantidad;
    }else{
        $_SESSION['carrito'][$id_producto] = $cantidad;
    }
}
// PARA OBTENER CARRITO
function obtenercarrito(){
    return isset($_SESSION['carrito'])?$_SESSION['carrito'] : [];
}
// PARA PODER VACIAR EL CARRITO
function vaciarcarrito(){
    unset($_SESSION['carrito']);
}

// FUNCIONES PARA PEDIDOS
function crearpedido($conn,$id_usuario,$carrito){
    $conn->begin_transaction();
    try{
        $total=0;

        foreach($carrito as $id_producto => $cantidad){
            $producto=obtenerproduxID($conn,$id_producto);
            if($producto['stock']<$cantidad){
                throw new Exception("No hay stock disponibles de ".$producto['nombre']);
            }
            $total += $producto['precio']*$cantidad;
        }

        //PARA INSERTAR PEDIDOS
        $sqlpedido="INSERT INTO pedidos (fecha,total,id_usuario) VALUES (NOW(), ?,?)";
        $stmt=$conn->prepare($sqlpedido);
        $stmt->bind_param("di",$total,$id_usuario);
        $stmt->execute();
        $id_pedido= $conn->insert_id;

    

        // PARA INSERTAR DETALLES DE PEDIDOS
        foreach($carrito as $id_producto =>$cantidad){
            $producto= obtenerproduxID($conn,$id_producto);
            $subtotal= $producto['precio']* $cantidad;
            $sqldetalle="INSERT INTO detalle_pedido (id_pedido,id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
            $stmtdetalle=$conn->prepare($sqldetalle);
            $stmtdetalle->bind_param("iiid",$id_pedido,$id_producto,$cantidad,$subtotal);
            $stmtdetalle->execute();
        // PARA RESTAR STOCK
            $nuevostock=$producto['stock']-$cantidad;
            $sqlstock="UPDATE productos SET stock=? WHERE id_producto =?";
            $stmtstock=$conn->prepare($sqlstock);
            $stmtstock->bind_param("ii",$nuevostock,$id_producto);
            $stmtstock->execute();
        }
        $conn->commit();
        vaciarcarrito();
        return true;
    } catch (Exception $e){
        $conn->rollback();
        return $e->getMessage();
    }
}

//OBTENER PEDIDOS (PARA ADMINISTRADOR)
function obtenerpedidos($conn){
    $sql= "SELECT p.id_pedido,p.fecha,p.total,u.nombre AS cliente
    FROM pedidos p JOIN usuarios u ON p.id_usuario = u.id_usuario
    ORDER BY p.fecha DESC";
    $result=$conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

//OBTENER DETALLES DE PEDIDOS ( PARA ADMINISTADOR)
function obtenerdetallepedido($conn,$id_pedido){
    $sql="SELECT dp.id_producto, pr.nombre, dp.cantidad, dp.subtotal
    FROM detalle_pedido dp JOIN productos pr ON dp.id_producto= pr.id_producto
    WHERE dp.id_pedido =? ";
    $stmt= $conn-> prepare($sql);
    $stmt-> bind_param("i",$id_pedido);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>