<?php

require "config.php";
require "functions.php";

$conexion = conexion_db($bd_config);

//Errores
$exito = '';
$error_clave = '';
$error_usuario = '';
$error_coincidencia = '';

//Captura de datos de formulario

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$destino = 'webmaster@univercity.com.co';
    $nombre = limpiarTexto($_POST['nombre']);
    $apellido = limpiarTexto($_POST['apellido']);
	$correo = limpiarCorreo($_POST['email']);
    $clave = $_POST['clave'];
    $clave_cifrada = password_hash($clave, PASSWORD_DEFAULT);
    $conf_clave = $_POST['clave2'];
    $conf_clave_cifrada = password_hash($conf_clave, PASSWORD_DEFAULT);

    $verificacion = password_verify($clave, $conf_clave_cifrada);
    
    validar_clave($clave,$error_clave);

    if (!$error_clave) {
        if ($clave == $conf_clave) {
                $sql_usuario = 'SELECT * FROM users WHERE (correo = :correo)';
                $statement = $conexion->prepare($sql_usuario);
                $statement->execute(array(':correo' => $correo));
                $resultadoUsuario = $statement->fetch();
                
            if (!$resultadoUsuario) {
                $contenido = "Nombre: " . $nombre . "\nApellido: " . $apellido . "\nCorreo: " . $correo;
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: Pruebas - UniverCity <webmaster@univercity.com>\r\n";

                insertarUsuario($conexion, $nombre, $apellido, $correo, $clave_cifrada);
            
                mail($destino, "Contacto web", $contenido, $headers);

                header('Location:registro-exitoso.php');
            } else {
               $error_usuario .="Ya existe un usuario registrado con ese correo";
            }
        } else {
            $error_coincidencia .="<br/>Las contraseñas deben conincidir";
        }
    }


    /*if ($clave == $conf_clave) {
        $contenido = "Nombre: " . $nombre . "\nApellido: " . $apellido . "\nCorreo: " . $correo;

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: Pruebas - UniverCity <webmaster@univercity.com>\r\n";
    
        mail($destino, "Contacto web", $contenido, $headers);

        header('Location:registro-exitoso.php');


    
    } else{
        $error .="Las contraseñas deben ser conincidir";
    }*/

}


$clPag ="secondary";
$idPag ="register-nav";
include_once "header.php";
include "views/registro.view.php";
include_once "footer.php";
