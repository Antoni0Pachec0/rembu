<?php

namespace app\controllers;
use app\models\mainModel;

class userController extends mainModel{

    /* ----- CONTROLADOR REGISTRAR USUARIO ----- */
    public function registrarUsuarioControlador(){
        // Almacenando datos
        $correo = $this->limpiarCadena($_POST['registroEmail']);
        $contraseña1 = $this->limpiarCadena($_POST["registroPassword"]);
        $contraseña2 = $this->limpiarCadena($_POST["registroPassword2"]);
        $nombre = $this->limpiarCadena($_POST['registroNombre']);
        $apellido = $this->limpiarCadena($_POST['registroApellido']);

        //Verificando campos obligatorios
        if($correo == "" || $contraseña1 == "" || $contraseña2 == "" || $nombre == "" || $apellido == ""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        //verificando integridad de los datos
        if($this->verificarDatos("[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}", $correo)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El correo no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if(($this->verificarDatos("[a-zA-Z0-9$@.-]{4,50}", $contraseña1)) || ($this->verificarDatos("[a-zA-Z0-9$@.-]{4,50}", $contraseña2))){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La contraseña no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }
        
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El apellido no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }


        //Verificando email
        if($correo != ""){
            if(filter_var($correo, FILTER_VALIDATE_EMAIL)){
                $check_email = $this->ejecutarConsulta("SELECT correo FROM usuario WHERE correo = '$correo'");
                if($check_email->rowCount()>0){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"Este correo ya se ecuentra registrado",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit;
                }

            }else{
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Ha ingresado un correo electrónico no valido",
					"icono"=>"error"
				];
				return json_encode($alerta);
                exit;
            }
        }


        //Verificando contraseña
        if($contraseña1 != $contraseña2){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Las contraseñas no coinciden",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }else{
            $contraseña = password_hash($contraseña1, PASSWORD_BCRYPT,["cost"=>10]);
        }

        $token = md5(uniqid());

        $check_token = $this->ejecutarConsulta("SELECT token FROM usuario WHERE token = '$token'");

        if($check_token->rowCount()>0){
            $token = md5(uniqid());
        }

        

        //Datos de registro
        $usuario_datos_reg = [
            [
                "campo_nombre" => "token",
                "campo_marcador" => ":token",
                "campo_valor" => $token
            ],
            [
                "campo_nombre" => "correo",
                "campo_marcador" => ":correo",
                "campo_valor" => $correo
            ],
            [
                "campo_nombre" => "password",
                "campo_marcador" => ":password",
                "campo_valor" => $contraseña
            ],
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "apellido",
                "campo_marcador" => ":apellido",
                "campo_valor" => $apellido
            ],
            [
                "campo_nombre" => "idCargo",
                "campo_marcador" => ":idCargo",
                "campo_valor" => 2
            ]
        ];

        $registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);

        if($registrar_usuario->rowCount() == 1){
            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Perfecto!",
                "texto"=>"Se ha registrado con éxito",
                "icono"=>"success"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo registrar el usuario",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }

    public function actualizarUsuario(){
        //Almacenando datos
        $nombre = $this->limpiarCadena($_POST['editarNombre']);
        $apellido = $this->limpiarCadena($_POST['editarApellido']);


        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El nombre no cumple con el formato solicitado",
                "icono" => "error"
           ];
           return json_encode($alerta);
           exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El apellido no cumple con el formato solicitado",
                "icono" => "error"
           ];
           return json_encode($alerta);
           exit;
        }

        //Guardando datos
        $usuario_datos_act = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "apellido",
                "campo_marcador" => ":apellido",
                "campo_valor" => $apellido
            ]
        ];

        $condicion=[
            "condicion_campo"=>"idUsuario",
            "condicion_marcador"=>":idUsuario",
            "condicion_valor"=> $_SESSION['id']
        ];

        $actualizar_usuario = $this->actualizarDatos("usuario", $usuario_datos_act, $condicion);

        if($actualizar_usuario->rowCount() == 1){
            $_SESSION['usuario'] = $nombre;
            $_SESSION['apellido'] = $apellido;

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Perfecto!",
                "texto" => "Se ha actualizado correctamente",
                "icono" => "success"
            ];
            return json_encode($alerta);

        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo actualizar",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }
    }
}