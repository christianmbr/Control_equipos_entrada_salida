<?php
require_once "coneccion.php";

class datos{
    public function editar_equipo ($id_dispo, $id, $descripcion){
        $salida = datos::query_normal ("UPDATE `ingreso_pc` SET `id_equipo_fisico`='$id_dispo',`descripcion_equipo`='$descripcion', ultima_fecha_actualizacion=DEFAULT WHERE id_pc = $id");
    }
    public function imprimir_historial_movimientos ($cedula){
        $salida = datos::query_normal ("SELECT h.id_pc, h.descripcion, h.fecha_historial_del_equipo, pc.cedula_responsable, pc.descripcion_equipo FROM historial_de_equipos h INNER JOIN ingreso_pc pc on pc.id_pc = h.id_pc WHERE pc.cedula_responsable = {$cedula};");

        return $salida;
    }
    public function registrar_equipo_solo ($cedula, $serial, $descripcion, $estado){
        $salida = datos::query_normal ("INSERT INTO `ingreso_pc`(`id_pc`, `cedula_responsable`, `id_equipo_fisico`, `descripcion_equipo`, `estado_equipo`, `ultima_fecha_actualizacion`) VALUES (DEFAULT, $cedula, '$serial', '$descripcion', '$estado', DEFAULT);");
    }
    public function filtrar_equipos_usuario ($cedula){
        $salida = datos::query_normal ("SELECT `id_equipo_fisico`, `id_pc`, `estado_equipo`, `descripcion_equipo` FROM `ingreso_pc` WHERE cedula_responsable = {$cedula}");

        return $salida;
    }
    public function actualizar_estado_pc ($id, $estado){
        if($estado == 'F') {
            $query = "UPDATE `ingreso_pc` SET `estado_equipo` = 'D' WHERE `id_pc` = {$id}";
        } elseif ($estado == 'D') {
            $query = "UPDATE `ingreso_pc` SET `estado_equipo` = 'F' WHERE `id_pc` = {$id}";
        }
        $salida = datos::query_normal($query);

        return $salida;
    }
    public function identificar_usuario($cedula){
        $query = "SELECT cedula_usuario_con_pc, nombre, correo FROM `usuarios_pc` WHERE cedula_usuario_con_pc = {$cedula}";
        $salida = datos::query_normal($query);

        return $salida;
    }

    public function registrar_nuevo_usuario($datos){
        $descripcion = $datos['marca_dispositivo'].' - '.$datos['color_dispositivo'];
        if(isset($datos['estado_equipo'])){
            // CALL ingresar_nuevo_pc (cedula, nombre, correo, id_equipo_fisico, descripcion_pc, estado_pc)
            $salida = datos::query_normal("CALL ingresar_nuevo_pc ($datos[cedula_usuario], '$datos[nombre_usuario]', '$datos[correo_usuario]', '$datos[id_dispositivo]', '$descripcion', 'F')");
        } else {
            $salida = datos::query_normal("CALL ingresar_nuevo_pc ($datos[cedula_usuario], '$datos[nombre_usuario]', '$datos[correo_usuario]', '$datos[id_dispositivo]', '$descripcion', 'D')");
        }
    }

    public function query_normal($query){
        $conexion = new conexion();
        $con = $conexion->conectar();

        if(!$resultado = $con->query($query)){
            echo "Falló CALL: (" . $mysqli->errno . ") " . $con->error;
        }
        $con->close();

        return $resultado;
    }
}
?>