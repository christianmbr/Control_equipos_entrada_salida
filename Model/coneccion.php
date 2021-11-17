<?php
    class conexion{
        public function conectar(){
            $con = new mysqli("localhost","root","","control_equipos");
            return $con;
        }
    }
?>