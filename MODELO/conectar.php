<?php
class Conectar{
    public static function conexion(){
try{
    $conexion=new PDO("mysql:host=localhost;dbname=ciclos",'root','');
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->exec("SET CHARACTER SET UTF8");
    return $conexion;
}catch(Exception $e){
   die("ERROR".$e->getMessage());
   echo "Linea del error".$e->getLine();
}
    }
}
?>