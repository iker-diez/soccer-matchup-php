<?php
Class modelo_alumno extends Conectar{

    private $bd;
    private $row_alumno;

    public function __construct(){
        
        $this->bd=Conectar::conexion();

        $this->row_alumnos=array();
    }
    // public function insertar_alumno($Nombre,$CorreoElectronico,$Contraseña){
    //     //No parametrizada (funciona)
    //     // $insertar=$this->bd->query("INSERT INTO almuno (id_al,nombre,edad,id_curso)VALUES($id_al,'$nom',$edad,$curso)");
    //     // if(!$insertar){
    //     //     $mensaje="insertado";
    //     // }else{
    //     //     $mensaje="la insercion no ha sido realizada";
    //     // }
    //     // return $mensaje;
    //     //Consulta parametrizada
    //     $sql="INSERT INTO alumno values(?,?,?)";
    //     $preparada=$this->bd->prepare($sql);
    //     $preparada->bindParam(1,$Nombre,PDO::PARAM_STR,100);
    //     $preparada->bindParam(2,$CorreoElectronico,PDO::PARAM_STR,100);
    //     $preparada->bindParam(3,$Contraseña,PDO::PARAM_STR,100);
    //     $ok=$preparada->execute();
    //         $preparada->closeCursor();
    // }
    public function insertar_alumno($Nombre, $CorreoElectronico, $Contraseña,$rol_id){
        // Verificar si el usuario y la contraseña son para una cuenta de administrador
        $es_administrador = false;
        // var_dump($Nombre);
        // var_dump($Contraseña);die;
        if ($Nombre === 'admin' && $Contraseña === 'contraseña_admin') {
            $es_administrador = true;
        }

        // Establecer el rol_id predeterminado
        $rol_id_predeterminado = ($es_administrador) ? 1 : 2; // ID_Rol predeterminado para los usuarios normales
        // Consulta parametrizada
        $sql = "INSERT INTO alumno (Nombre, CorreoElectronico, Contraseña, rol_id) VALUES (?, ?, ?, ?)";
        $preparada = $this->bd->prepare($sql);
        $preparada->bindParam(1, $Nombre, PDO::PARAM_STR, 100);
        $preparada->bindParam(2, $CorreoElectronico, PDO::PARAM_STR, 100);
        $preparada->bindParam(3, $Contraseña, PDO::PARAM_STR, 100);
        $preparada->bindParam(4, $rol_id, PDO::PARAM_INT);
        $ok = $preparada->execute();
        $preparada->closeCursor();
    }
    public function insertar_equipo($id_equipo,$Nombre_Equipo, $Descripcion, $ruta_foto) {
        // Consulta parametrizada
        $sql = "INSERT INTO equipo (id_equipo, Nombre_Equipo, Descripcion, Foto) VALUES (?, ?, ?, ?)";
        $preparada = $this->bd->prepare($sql);
        $preparada->bindParam(1, $id_equipo, PDO::PARAM_INT, 3);
        $preparada->bindParam(2, $Nombre_Equipo, PDO::PARAM_STR, 100);
        $preparada->bindParam(3, $Descripcion, PDO::PARAM_STR, 255);
        $preparada->bindParam(4, $ruta_foto, PDO::PARAM_STR, 255); // Nueva línea para la ruta de la foto
        $ok = $preparada->execute();
        $preparada->closeCursor();
    }
    public function insertar_jugador($ID_Equipo, $nombre, $dorsal, $posicion){
      
        // Consulta parametrizada
        $sql = "INSERT INTO jugadores (ID_Equipo, nombre, dorsal, posicion) VALUES (?, ?, ?, ?)";
        $preparada = $this->bd->prepare($sql);
        $preparada->bindParam(1, $ID_Equipo, PDO::PARAM_STR, 100);
        $preparada->bindParam(2, $nombre, PDO::PARAM_STR, 100);
        $preparada->bindParam(3, $dorsal, PDO::PARAM_INT, 3);
        $preparada->bindParam(4, $posicion, PDO::PARAM_STR, 100);
        $ok = $preparada->execute();
        $preparada->closeCursor();
    }
    public function insertar_partido($fecha, $equipo_local, $equipo_visitante){
      
        // Consulta parametrizada
        $sql = "INSERT INTO partidos (fecha, equipo_local, equipo_visitante) VALUES (?, ?, ?)";
        $preparada = $this->bd->prepare($sql);
        $preparada->bindParam(1, $fecha, PDO::PARAM_STR, 100);
        $preparada->bindParam(2, $equipo_local, PDO::PARAM_STR, 100);
        $preparada->bindParam(3, $equipo_visitante, PDO::PARAM_STR, 100);
        $ok = $preparada->execute();
        $preparada->closeCursor();
    }
    // public function borrar_alumno($ID_Jugador){
    //      $sql="DELETE FROM jugadores where id_al=$ID_Jugador";
    //      $preparada=$this->bd->prepare($sql);
    //      $preparada->bindParam(1,$id_al,PDO::PARAM_INT);
    //      $ok=$preparada->execute();
    //      $preparada->closeCursor();
    // }
    // public function actualizar_alumno($id_al,$edad){
    //     try{
    //         //$consulta='SELECT edad FROM almuno where nombre="jorge"' ;
    //         $consulta='update almuno set edad=? where id_al=?';
    //         $preparada=$this->bd->prepare($consulta);
    //         $preparada->bindParam(1,$edad,PDO::PARAM_INT);
    //         $preparada->bindParam(2,$id_al,PDO::PARAM_INT);
    //         $ok=$preparada->execute();
    //         if($ok){
    //             if($preparada->rowCount()==0)
    //             echo "No existe el alumno";
    //         }else{
    //             echo"Modificacion completada";
    //         }
    //        // $fila=mysqli_fetch_array($resultados)
    //         // while($fila=mysqli_fetch_array($resultados)){
    //         //     echo  $fila[0]."<br>";
    //         // }
    //         $preparada=null;
    //     }
    //     catch(PDOexception $e){
    //     echo "Error...".$e->getMessage();
    //     }
    // }

    
}
?>