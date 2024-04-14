<?php

namespace app\controllers;
use app\models\mainModel;

class loginController extends mainModel{
    public function iniciarSesionControlador(){

        $correo = $this->limpiarCadena($_POST['email']);
        $contraseña = $this->limpiarCadena($_POST['contraseña']);

        if($correo == "" || $contraseña == ""){
            echo "<script>
			        Swal.fire({
					  icon: 'error',
					  title: 'Ocurrió un error inesperado',
					  text: 'No has llenado todos los campos que son obligatorios'
					});
				</script>";

        }else{

            if($this->verificarDatos("[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}", $correo)){
                echo "<script>
                            Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error inesperado',
                            text: 'El correo no cumple con el formato solicitado'
                            });
                        </script>";

            }else{
                if($this->verificarDatos("[a-zA-Z0-9$@.-]{4,100}",$contraseña)){
                    echo "<script>
                            Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error inesperado',
                            text: 'La contraseña no cumple con el formato solicitado'
                            });
                        </script>";

                }else{

                    #Verificando usuario
                    $check = $this->ejecutarConsulta("SELECT * FROM usuario WHERE correo = '$correo'");

                    if($check -> rowCount() == 1){

                        $check = $check->fetch();

                        if($check["correo"] == $correo && password_verify($contraseña, $check["password"])){

                            $_SESSION['id'] = $check["idUsuario"];
                            $_SESSION['usuario'] = $check["nombre"];
                            $_SESSION['apellido'] = $check["apellido"];
                            $_SESSION['cargo'] = $check["idCargo"];
                            $_SESSION['foto'] = $check["imgUs"];

                            if(headers_sent()){
                                echo "<script> window.location.href='".APP_URL."inicio/'; </script>";
                            }else{
                                header("Location: ".APP_URL."inicio/");
                            }

                        }else{
                            echo "<script>
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Ocurrió un error inesperado',
                                    text: 'Contraseña incorrecta'
                                    });
                                </script>";
                        }

                    }else{
                        echo "<script>
                                Swal.fire({
                                icon: 'error',
                                title: 'Ocurrió un error inesperado',
                                text: 'Usuario no encontrado'
                                });
                            </script>";
                    }
                }
            }
        }

    }


    public function cerrarSesionControlador(){
        session_destroy();

		if(headers_sent()){
            echo "<script> window.location.href='".APP_URL."inicio/'; </script>";
        }else{
            header("Location: ".APP_URL."inicio/");
        }
    }


    public function verificarSesionControlador(){
        if((!isset($_SESSION['id']) || $_SESSION['id']=="") || (!isset($_SESSION['usuario']) || $_SESSION['usuario']=="")){
            $this->cerrarSesionControlador();
            exit;
        }else{
            return true;
        }
    }
}