<?php
class controller {
    public function template(){
        include "views/template.php";
    }
    public function controladorPagina(){
        include "views/pagPrincipal.php";
    }
    // Funciones que imprimen los modales.
    public function modal_actualizacion_datos ($identificador, $cedula, $id, $estado){
        $modal = "
            <!--MODAL-->
            <div class='modal fade' id='exampleModal$identificador' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='exampleModalLabel'>Actualizar estado del equipo</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            <div class='alert alert-warning' role='alert'>
                                <h3>¿Desea cambiar el estado de este equipo?</h3>
                            </div>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>No</button>
                            <a href='index.php?cedula=$cedula&id=$id&estado=$estado' class='btn btn-primary'>Cambiar estado</a>
                        </div>
                    </div>
                </div>
            </div>
        ";

        return $modal;
    }

    public function buscar_usuario_controller(){
        // Si se recibe un dato cedula por post o por get.
        if(isset($_POST['cedula_usuario']) || isset($_GET['cedula'])){
            $crud = new datos();
            $form_registro = false;
            $form_edit = false;

            if(isset($_POST['cedula_usuario'])){
                $datos = $crud->identificar_usuario($_POST['cedula_usuario']);

            } if (isset($_GET['cedula']) && isset($_GET['id'])){
                $datos = $crud->identificar_usuario($_GET['cedula']);
                $ee = $crud->actualizar_estado_pc($_GET['id'], $_GET['estado']);

            } if (isset($_GET['registro'])){
                if($_GET['registro'] == 1){
                    $datos = $crud->identificar_usuario($_GET['cedula']);
                    $form_registro = true;    
                }
            } if (isset($_POST['registro_pc_nuevo'])){
                $descripcion = "$_POST[marca_dispositivo]"." - "."$_POST[color_pc]";
                if(isset($_POST['estado_equipo'])){
                    $crud->registrar_equipo_solo($_POST['cedula'], $_POST['id_dispositivo'], $descripcion, "F");
                } else {
                    $crud->registrar_equipo_solo($_POST['cedula'], $_POST['id_dispositivo'], $descripcion, "D");
                }
                // echo "<h1>$_POST[cedula_usuario] $_POST[id_dispositivo] $_POST[marca_dispositivo] $_POST[color_pc] </h1>";
            } if (isset($_GET['form_act'])){
                $datos = $crud->identificar_usuario($_GET['cedula']);
                $form_edit = true;
            } if(isset($_POST['editar'])){
                $descripcion = "$_POST[marca_dispositivo]"." - "."$_POST[color_pc]";
                $crud->editar_equipo($_POST['id_dispositivo'], $_POST['id'], $descripcion);

            } if (isset($_POST['color_dispositivo'])){
                // var_dump($_POST);
                // $crud = new datos();
                $crud->registrar_nuevo_usuario($_POST);
                echo "
                    <div class='col-12'>
                        <div class='alert alert-success' role='alert'>
                        <h4>¡El usuario se registró correctamente!</h4>
                    </div>
                    </div>
                    <div class='col-10'>
                        <input type='text' name='cedula_usuario' class='form-control is-valid' id='cedula' placeholder='cedula' autocomplete='off' required>
                    </div>
                    <div class='d-grid col-2'>
                        <input class='btn btn-primary' type='submit' value='Buscar'>
                    </div>
                ";
            } else {
                if($datos->num_rows > 0){
                    $columna = $datos->fetch_assoc();
                    echo "
                        <div class='col-12'>
                            <input type='text' class='form-control is-valid' id='nombre' value='$columna[cedula_usuario_con_pc]' name='cedula' outocomplete='off' required readonly>
                        </div>
                        <div class='col-6'>
                            <input type='text' class='form-control is-valid' id='nombre' value='$columna[nombre]' placeholder='Nombre completo' autocomplete='off' required readonly>
                        </div>
                        <div class='col-6'>
                            <input type='text' class='form-control is-valid' value='$columna[correo]' placeholder='Correo' autocomplete='off' required readonly>
                        </div>
                        <div class='col-12 text-center'>
                            <table class='table'>
                                <thead>
                                    <tr>
                                        <th scope='col'>No</th>
                                        <th scope='col'>ID en BD</th>
                                        <th scope='col'>ID fisica</th>
                                        <th scope='col'>Descripcion</th>
                                        <th scope='col'>Estado</th>
                                        <th scope='col'>Actualizar</th>
                                        <th scope='col'>Editar</th>
                                    </tr>
                                </thead>
                                <tbody>
                    ";
                    if (isset($_GET['cedula'])){
                        $pcs = $crud->filtrar_equipos_usuario($_GET['cedula']);
                    } else {
                        $pcs = $crud->filtrar_equipos_usuario($_POST['cedula_usuario']);
                    }
                    $cont = 1;
                    while($row = $pcs->fetch_assoc()){
                        if ($row['estado_equipo'] == 'F'){
                            echo "
                                <tr class='table-danger'>
                                    <th scope='row'>$cont</th>
                                    <td>$row[id_pc]</td>
                                    <td>$row[id_equipo_fisico]</td>
                                    <td>$row[descripcion_equipo]</td>
                                    <td>Fuera</td>
                                    <td class='d-grid'>
                                        <button type='button' class='btn btn-outline-danger' data-bs-toggle='modal' data-bs-target='#exampleModal$cont'>
                                            <i class='fas fa-exchange-alt'></i>
                                        </button>
                                    </td>
                                    <td>
                                        <a href='index.php?form_act=0&cedula=$columna[cedula_usuario_con_pc]&edit_id=$row[id_pc]&edit_id_equipo=$row[id_equipo_fisico]&c=$columna[cedula_usuario_con_pc]' class='btn btn-outline-danger'><i class='fas fa-pencil-alt'></i></a>
                                    </td>
                                </tr>
                            ";
                            // Imprime los modales de cada equipo
                            $modales = controller::modal_actualizacion_datos ($cont, $columna['cedula_usuario_con_pc'], $row['id_pc'], "F");
                            echo $modales;
                        } else {
                            echo "
                                <tr class='table-success'>
                                    <th scope='row'>$cont</th>
                                    <td>$row[id_pc]</td>
                                    <td>$row[id_equipo_fisico]</td>
                                    <td>$row[descripcion_equipo]</td>
                                    <td>Dentro</td>
                                    <td class='d-grid'>
                                        <button type='button' class='btn btn-outline-success' data-bs-toggle='modal' data-bs-target='#exampleModal$cont'>
                                            <i class='fas fa-exchange-alt'></i>
                                        </button>
                                    </td>
                                    <td>
                                        <a href='index.php?form_act=0&cedula=$columna[cedula_usuario_con_pc]&edit_id=$row[id_pc]&edit_id_equipo=$row[id_equipo_fisico]&c=$columna[cedula_usuario_con_pc]' class='btn btn-outline-success'><i class='fas fa-pencil-alt'></i></a>
                                    </td>
                                </tr>
                            ";
                            $modales = controller::modal_actualizacion_datos($cont, $columna['cedula_usuario_con_pc'], $row['id_pc'], "D");
                            echo $modales;
                        }
                        $cont ++;
                    }
                    echo "
                                </tbody>
                            </table>
                        </div>
                        <p class='d-grid col-12'>
                            <button class='btn btn-outline-success' type='button' data-bs-toggle='collapse' data-bs-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>
                                Historial de movimientos
                            </button>
                        </p>
                        <div class='collapse' id='collapseExample'>
                            <div class='card card-body'>
                    ";
                            $datos = $crud->imprimir_historial_movimientos($columna['cedula_usuario_con_pc']);
                            $contador_historiales = 1;
                    
                            while($historial = $datos->fetch_assoc()){
                                echo "
                                    <p></h5><b>$contador_historiales:</b> $historial[cedula_responsable] - <b>$historial[descripcion_equipo]</b> $historial[descripcion] el dia <b>$historial[fecha_historial_del_equipo]</b></h5></p>
                                ";
                                $contador_historiales ++;                
                            }
                    echo "        
                            </div>
                        </div>

                        <div class='d-grid col-6'>
                            <a href='index.php?cedula=$columna[cedula_usuario_con_pc]&registro=1' class='btn btn-primary'>Registrar equipo</a>
                        </div>
                        <div class='d-grid col-6'>
                            <a href='index.php' class='btn btn-primary'>salir</a>
                        </div>
                        <!-- <input type='hidden' name='actu'></input> -->
                    ";
                    if ($form_registro){
                        echo "
                            <div class='col-12'>
                                <div class='alert alert-info' role='alert' style='margin-top: 20px;'>
                                    <b>FORMULARIO REGISTRO EQUIPO</b>
                                </div>
                            </div>                        
                            <div class='col-12'>
                                <label class='form-label'>Identificador fisico del equipo</label>
                                <input type='text' class='form-control is-valid' placeholder='Id del dispositivo' name='id_dispositivo'>
                                <div class='valid-feedback'>
                                    Si el dispositivo posee una ID fisica registrala, si no la tiene deja el campo vacio.
                                </div>
                            </div>
                            <div class='col-6'>
                                <label class='form-label'>Marca del equipo</label>
                                <select class='form-select' aria-label='Default select example' name='marca_dispositivo'>
                                    <option selected>...</option>
                                    <option>Lenovo</option>
                                    <option>Asus</option>
                                    <option>Dell</option>
                                    <option>HP</option>
                                    <option>Acer</option>
                                    <option>Apple</option>
                                    <option>MSI</option>
                                    <option>Razer</option>
                                </select>
                                <div class='valid-feedback'>
                                    Cerciorese de seleccionar la marca del equipo.
                                </div>
                            </div>
                            <div class='col-6'>
                                <label class='form-label'>Color del equipo</label>
                                <select class='form-select' aria-label='Default select example' name='color_pc'>
                                    <option selected>...</option>
                                    <option>Amarillo</option>
                                    <option>Verde</option>
                                    <option>Azul</option>
                                </select>
                                <div class='valid-feedback'>
                                    Cerciorese de seleccionar el color del equipo.
                                </div>
                            </div>
                            <div class='col-12'>
                                <label class='form-label'>Estado del equipo</label>
                                <div class='form-check form-switch'>
                                    <input class='form-check-input' type='checkbox' id='flexSwitchCheckDefault' name='estado_equipo'>
                                    <label class='form-check-label' for='flexSwitchCheckDefault'>¿El equipo va para afuera?</label>
                                </div>
                            </div>
                            <div class='d-grid col-12'>
                                <input class='btn btn-primary' type='submit' value='Registrar'>
                            </div>
                            <input type='hidden' name='registro_pc_nuevo'></input>
                        ";
                    }
                    if ($form_edit){
                        echo "
                            <div class='col-12'>
                                <div class='alert alert-info' role='alert' style='margin-top: 20px;'>
                                    <b>FORMULARIO EDICION</b>
                                </div>
                            </div>
                            <div class='col-6'>
                                <label class='form-label'>Cedula</label>
                                <input type='text' name='cedula_usuario' class='form-control is-valid' id='cedula' value='$_GET[c]' autocomplete='off' readonly required>
                            </div>
                            <div class='col-6'>
                                <label class='form-label'>ID de equipo en base de datos</label>
                                <input type='text' name='id' class='form-control is-valid' id='cedula' value='$_GET[edit_id]' autocomplete='off' readonly required>
                            </div>
                            <div class='col-12'>
                                <label class='form-label'>Identificador fisico o serial</label>
                                <input type='text' class='form-control is-valid' placeholder='Id del dispositivo' name='id_dispositivo' value='$_GET[edit_id_equipo]'>
                                <div class='valid-feedback'>
                                    Si el dispositivo posee una ID fisica registrala, si no la tiene deja el campo vacio.
                                </div>
                            </div>
                            <div class='col-6'>
                                <label class='form-label'>Marca del equipo</label>
                                <select class='form-select' aria-label='Default select example' name='marca_dispositivo'>
                                    <option selected>...</option>
                                    <option>Lenovo</option>
                                    <option>Asus</option>
                                    <option>Dell</option>
                                    <option>HP</option>
                                    <option>Acer</option>
                                    <option>Apple</option>
                                    <option>MSI</option>
                                    <option>Razer</option>
                                </select>
                                <div class='valid-feedback'>
                                    Cerciorese de seleccionar la marca del equipo.
                                </div>
                            </div>
                            <div class='col-6'>
                                <label class='form-label'>Color del equipo</label>
                                <select class='form-select' aria-label='Default select example' name='color_pc'>
                                    <option selected>...</option>
                                    <option>Amarillo</option>
                                    <option>Verde</option>
                                    <option>Azul</option>
                                </select>
                                <div class='valid-feedback'>
                                    Cerciorese de seleccionar el color del equipo.
                                </div>
                            </div>
                            <input type='hidden' name='editar'>
                            <div class='d-grid col-12'>
                                <input class='btn btn-primary' type='submit' value='Actualizar'>
                            </div>
                        ";
                    }
                } else {
                    echo "
                        <label class='form-label'>Informacion de usuario</label>
                        <div class='col-12'>
                            <input type='text' name='cedula_usuario' class='form-control is-valid' id='cedula' value='$_POST[cedula_usuario]' autocomplete='off' readonly required>
                        </div>
                        <div class='col-12'>
                            <div class='col-12 alert alert-success' role='alert'>
                                Este usuario es primera vez que registra un equipo!
                            </div>
                        </div>
                        <div class='col-6'>
                            <label class='form-label'>Nombre completo del usuario</label>
                            <input type='text' class='form-control is-valid' id='nombre' placeholder='Nombre completo' name='nombre_usuario' required>
                        </div>
                        <div class='col-6'>
                            <label class='form-label'>Correo del usuario</label>
                            <input type='email' class='form-control' placeholder='Correo' name='correo_usuario' required>
                        </div>

                        <div class='col-12'>
                            <label class='form-label'>Identificador fisico del equipo</label>
                            <input type='text' class='form-control is-valid' placeholder='Id del dispositivo' name='id_dispositivo'>
                            <div class='valid-feedback'>
                                Si el dispositivo posee una ID fisica registrala, si no la tiene deja el campo vacio.
                            </div>
                        </div>
                        <div class='col-6'>
                            <label class='form-label'>Marca del equipo</label>
                            <select class='form-select' aria-label='Default select example' name='marca_dispositivo'>
                                <option selected>...</option>
                                <option>Lenovo</option>
                                <option>Asus</option>
                                <option>Dell</option>
                                <option>HP</option>
                                <option>Acer</option>
                                <option>Apple</option>
                                <option>MSI</option>
                                <option>Razer</option>
                            </select>
                            <div class='valid-feedback'>
                                Cerciorese de seleccionar la marca del equipo.
                            </div>
                        </div>
                        <div class='col-6'>
                            <label class='form-label'>Color del equipo</label>
                            <select class='form-select' aria-label='Default select example' name='color_dispositivo'>
                                <option selected>...</option>
                                <option>Amarillo</option>
                                <option>Verde</option>
                                <option>Azul</option>
                            </select>
                            <div class='valid-feedback'>
                                Cerciorese de seleccionar el color del equipo.
                            </div>
                        </div>
                        <div class='col-12'>
                            <label class='form-label'>Estado del equipo</label>
                            <div class='form-check form-switch'>
                                <input class='form-check-input' type='checkbox' id='flexSwitchCheckDefault' name='estado_equipo'>
                                <label class='form-check-label' for='flexSwitchCheckDefault'>¿El equipo va para afuera?</label>
                            </div>
                        </div>
                        <div class='d-grid col-12'>
                            <input class='btn btn-primary' type='submit' value='Registrar'>
                        </div>
                        <div class='d-grid col-12'>
                            <a href='index.php' class='btn btn-primary'>Salir</a>
                        </div>
                    ";
                }
            }
        } else {
            echo "
                <div class='col-10'>
                    <input type='text' name='cedula_usuario' class='form-control is-valid' id='cedula' placeholder='cedula' autocomplete='off' required>
                </div>
                <div class='d-grid col-2'>
                    <input class='btn btn-primary' type='submit' value='Buscar'>
                </div>
            ";
        }
    }
}
?>