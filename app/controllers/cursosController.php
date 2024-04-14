<?php

namespace app\controllers;
use app\models\mainModel;

class cursosController extends mainModel{

    /* ============= OBTENER DATOS INDIVIDUALES O TIPO LISTA ============== */

    public function obtenerCursos($parametro){
        if($parametro == "academicos" || $parametro == "culturales"){
            $query = "SELECT * FROM curso WHERE categoria = '$parametro'";
        }else{
            $query = "SELECT * FROM curso WHERE idUsuario = '$parametro'";
        }

        $stmt = $this->ejecutarConsulta($query);
        return $stmt;
    }

    //OBTENER CONTENIDO DE LECCIONES Y ARTICULOS DE UN CURSO ESPECIFICO (CONJUNTO DE ELEMENTOS)
    public function obtenerContenido($tabla, $idCurso){
        $query = "SELECT * FROM $tabla WHERE idCurso = '$idCurso'";
        $stmt = $this -> ejecutarConsulta($query);
        return $stmt;
    }

    //OBTENER LOS DATOS DE UN ELEMENTO EN ESPECIFICO
    public function obtenerElemento($tabla, $columna, $id){
        $query = "SELECT * FROM $tabla WHERE $columna = '$id'";

        $stmt = $this->ejecutarConsulta($query);
        return $stmt -> fetch();
    }




    /* =============== CONTROLADORES PARA LOS CURSOS ================ */

    //REGISTRAR UN CURSO
    public function crearCurso(){
        //Almacenando datos
        $nombre = $this->limpiarCadena($_POST['nombreCurso']);
        $descripcion = $this->limpiarCadena($_POST['descripcionCurso']);
        $categoria = $this->limpiarCadena($_POST['categoriaCurso']);

        if($_FILES['imagenCurso']['name'] != null){
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/CasaLila/app/uploads/image/";

            $image_name = $_FILES['imagenCurso']['name'];
            $image_tmp = $_FILES['imagenCurso']['tmp_name'];

            //Subiendo la nueva imagen al servidor
            $uploadPath = $uploadDirectory . $image_name;
            move_uploaded_file($image_tmp, $uploadPath);

            // Generar la URL para guardarla en la base de datos
            $url = APP_URL . "app/uploads/image/" . $image_name;
        }

        //Verificando campos obligatorios
        if($nombre == "" || $descripcion == "" || $categoria == ""){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos obligatorios",
                "icono"=>"error"
            ];

            return json_encode($alerta);
            exit;
        }

        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,100}", $nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,600}", $descripcion)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La descripcion no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $categoria)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La categoria no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        //Pasando datos
        $curso_datos_reg = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "descripcion",
                "campo_marcador" => ":descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "categoria",
                "campo_marcador" => ":categoria",
                "campo_valor" => $categoria
            ],
            [
                "campo_nombre" => "idUsuario",
                "campo_marcador" => ":idUsuario",
                "campo_valor" => $_SESSION['id']
            ],
            [
                "campo_nombre" => "URL",
                "campo_marcador" => ":imagen",
                "campo_valor" => $url
            ]
        ];

        $crearCurso = $this->guardarDatos("curso", $curso_datos_reg);

        if($crearCurso->rowCount() == 1){
            $alerta=[
                "tipo"=>"redireccionar",
                "titulo"=>"Perfecto!",
                "texto"=>"Curso creado con éxito",
                "icono"=>"success",
                "url" => APP_URL."admin-perfil/"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo crear el curso",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }


    // ACTUALIZAR CURSO

    public function actualizarCurso(){
        //ALMACENANDO DATOS
        $nombre = $this->limpiarCadena($_POST['actNombre']);
        $descripcion = $this->limpiarCadena($_POST['actDescripcion']);
        $SEC = $this->limpiarCadena($_POST['actSEC']);
        $LQA = $this->limpiarCadena($_POST['actLQA']);
        $PDE = $this->limpiarCadena($_POST['actPDE']);
        $idCurso = $this->limpiarCadena($_POST['idCurso']);
        $oldImage = $_POST['oldImage'];

        if($_FILES['imagenCurso']['name'] != null){
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/CasaLila/app/uploads/image/";

            $image_name = $_FILES['imagenCurso']['name'];
            $image_tmp = $_FILES['imagenCurso']['tmp_name'];
        
            //Eliminando la imagen antigua del servidor
            if ($oldImage != null) {
                $oldImagePath = str_replace('http://localhost/', $_SERVER['DOCUMENT_ROOT'] . '/', $oldImage);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            //Subiendo la nueva imagen al servidor
            $uploadPath = $uploadDirectory . $image_name;
            move_uploaded_file($image_tmp, $uploadPath);

            // Generar la URL para guardarla en la base de datos
            $url = APP_URL . "app/uploads/image/" . $image_name;
        }else{
            $url = $oldImage;
        }

        //Verificando campos obligatorios
        if($nombre == "" || $descripcion == ""){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos obligatorios",
                "icono"=>"error"
            ];

            return json_encode($alerta);
            exit;
        }

        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,100}", $nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,250}", $descripcion)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La descripción no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{0,5000}", $SEC)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Sobre este curso no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{0,5000}", $LQA)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Lo que aprenderás no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{0,5000}", $PDE)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Plan de estudio no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }


        //PASANDO DATOS
        $curso_datos_act = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "descripcion",
                "campo_marcador" => ":descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "sobre_este_curso",
                "campo_marcador" => ":SEC",
                "campo_valor" => $SEC
            ],
            [
                "campo_nombre" => "lo_que_aprenderas",
                "campo_marcador" => ":LQA",
                "campo_valor" => $LQA
            ],
            [
                "campo_nombre" => "plan_de_estudios",
                "campo_marcador" => ":PDE",
                "campo_valor" => $PDE
            ],
            [
                "campo_nombre" => "URL",
                "campo_marcador" => ":imagen",
                "campo_valor" => $url
            ]
        ];

        $condicion = [
            "condicion_campo"=>"idCurso",
            "condicion_marcador"=>":idCurso",
            "condicion_valor"=> $idCurso
        ];

        $actualizarCurso = $this->actualizarDatos("curso", $curso_datos_act, $condicion);

        if($actualizarCurso->rowCount() == 1){
            $alerta=[
                "tipo"=>"redireccionar",
                "titulo"=>"Perfecto!",
                "texto"=>"Curso actualizado con éxito",
                "icono"=>"success",
                "url" => APP_URL."admin-curso/" . $idCurso . "/"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo actualizar el curso",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }



    /* =========================== CONTROLADORES PARA LECCIONES =============== ===============*/

    //REGISTRAR LECCION
    public function crearLeccion($idCurso){
        $nombre = $this->limpiarCadena($_POST['nombreLeccion']);
        $descripcion = $this->limpiarCadena($_POST['descripcionLeccion']);
        
        if($_FILES['videoLeccion']['name'] != null){
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/CasaLila/app/uploads/video/";

            $video_name = $_FILES['videoLeccion']['name'];
            $video_size = $_FILES['videoLeccion']['size'];
            $video_tmp = $_FILES['videoLeccion']['tmp_name'];
            $video_type = $_FILES['videoLeccion']['type'];
            $file_parts = explode('.', $_FILES['videoLeccion']['name']);

            // Obtener la extensión del archivo (la última parte después del último punto)
            $video_ext = strtolower(end($file_parts));

            //Extensiones permitidas
            $extensions = array("mp4", "webm", "ogg");

            //Comprobar la extension del archivo
            if (!in_array($video_ext, $extensions)) {
                $errors[] = "Extensión no permitida, por favor elige un archivo de vídeo válido.";
            }

            // Comprobar el tamaño del archivo (en bytes)
            $maxFileSize = 50 * 1024 * 1024; // 50 MB
            if($video_size > $maxFileSize){
                $errors[] = 'El archivo es demasiado grande. Tamaño máximo permitido: 50 MB';
            }

            // Si no hay errores, mover el archivo al directorio de subidas y generar la URL
            if(empty($errors)){
                $uploadPath = $uploadDirectory . $video_name;
                move_uploaded_file($video_tmp, $uploadPath);

                // Generar la URL para guardarla en la base de datos
                $url = APP_URL . "app/uploads/video/" . $video_name;
            }else{
                print_r($errors);
            }
        }else{
            $url = $this->limpiarCadena($_POST['urlExterna']);
        }

        //Verificando campos obligatorios
        if($nombre == "" || $descripcion == ""){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos obligatorios",
                "icono"=>"error"
            ];

            return json_encode($alerta);
            exit;
        }

        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,100}", $nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,600}", $descripcion)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La descripcion no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        $leccion_datos_reg = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "descripcion",
                "campo_marcador" => ":descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "URL",
                "campo_marcador" => ":url",
                "campo_valor" => $url
            ],
            [
                "campo_nombre" => "idCurso",
                "campo_marcador" => ":idCurso",
                "campo_valor" => $idCurso
            ]
        ];

        $crearLeccion = $this->guardarDatos("lecciones", $leccion_datos_reg);

        if($crearLeccion->rowCount() == 1){
            $alerta=[
                "tipo"=>"redireccionar",
                "titulo"=>"Perfecto!",
                "texto"=>"Leccion creada con éxito",
                "icono"=>"success",
                "url" => APP_URL."admin-curso/" . $idCurso . "/"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo crear la leccion",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }


    //ACTUALIZAR LECCION
    public function editarLeccion(){
        //GUARDANDO DATOS
        $nombre = $this->limpiarCadena($_POST['nombreLeccion']);
        $descripcion = $this->limpiarCadena($_POST['descripcionLeccion']);
        $idLeccion = $this->limpiarCadena($_POST['idLeccion']);
        $idCurso = $this->limpiarCadena($_POST['idCurso']);

        if(isset($_FILES['videoLeccion'])){
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/CasaLila/app/uploads/video/";

            $video_name = $_FILES['videoLeccion']['name'];
            $video_size = $_FILES['videoLeccion']['size'];
            $video_tmp = $_FILES['videoLeccion']['tmp_name'];
            $video_type = $_FILES['videoLeccion']['type'];
            $file_parts = explode('.', $_FILES['videoLeccion']['name']);
            $oldVideo = $_POST['oldVideo'];

            // Obtener la extensión del archivo (la última parte después del último punto)
            $video_ext = strtolower(end($file_parts));

            //Extensiones permitidas
            $extensions = array("mp4", "webm", "ogg");

            //Comprobar la extension del archivo
            if (!in_array($video_ext, $extensions)) {
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Extensión no permitida",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit;
            }

            // Comprobar el tamaño del archivo (en bytes)
            $maxFileSize = 50 * 1024 * 1024; // 50 MB
            if($video_size > $maxFileSize){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El archivo supera el tamaño máximo permitido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit;
            }

            //Eliminando el video antiguo del servidor si hay uno
            $url = explode("/", $_POST['oldVideo']);
            $resultado = $url[2];

            if($resultado == APP_NAME){
                $oldVideoPath = str_replace('http://localhost/', $_SERVER['DOCUMENT_ROOT'] . '/', $oldVideo);
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }

            $uploadPath = $uploadDirectory . $video_name;
            move_uploaded_file($video_tmp, $uploadPath);

            // Generar la URL para guardarla en la base de datos
            $url = APP_URL . "app/uploads/video/" . $video_name;
            
        }else{
            //Eliminando el video antiguo del servidor si hay uno
            $url = explode("/", $_POST['oldVideo']);
            $resultado = $url[2];
            $oldVideo = $_POST['oldVideo'];

            if($resultado == APP_NAME){
                $oldVideoPath = str_replace('http://localhost/', $_SERVER['DOCUMENT_ROOT'] . '/', $oldVideo);
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }

            $url = $_POST['urlExterna'];
        }

        //Verificando campos obligatorios
        if($nombre == "" || $descripcion == ""){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos obligatorios",
                "icono"=>"error"
            ];

            return json_encode($alerta);
            exit;
        }

        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,100}", $nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,600}", $descripcion)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La descripcion no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        //GUARDANDO DATOS
        $leccion_datos_act = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "descripcion",
                "campo_marcador" => ":descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "URL",
                "campo_marcador" => ":url",
                "campo_valor" => $url
            ]
        ];

        $condicion=[
            "condicion_campo"=>"idLeccion",
            "condicion_marcador"=>":idLeccion",
            "condicion_valor"=> $idLeccion
        ];

        $actualizarLeccion = $this->actualizarDatos("lecciones", $leccion_datos_act, $condicion);

        if($actualizarLeccion->rowCount() == 1){
            $alerta=[
                "tipo"=>"redireccionar",
                "titulo"=>"Perfecto!",
                "texto"=>"Leccion actualizada con éxito",
                "icono"=>"success",
                "url" => APP_URL."admin-curso/" . $idCurso . "/"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo actualizar la leccion",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }



    /* ====================== CONTROLADORES ARTICULOS ========================== */

    //CREAR ARTICULO
    public function crearArticulo(){
        //Obteniendo datos
        $nombre = $this->limpiarCadena($_POST['nombreArticulo']);
        $idCurso = $_POST['id'];

        if($nombre == ""){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos obligatorios",
                "icono"=>"error"
            ];

            return json_encode($alerta);
            exit;
        }

        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,100}", $nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        if(isset($_FILES['docArticulo'])){
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/CasaLila/app/uploads/documents/";

            $doc_name = $_FILES['docArticulo']['name'];
            $doc_size = $_FILES['docArticulo']['size'];
            $doc_tmp = $_FILES['docArticulo']['tmp_name'];

            // Comprobar el tamaño del archivo (en bytes)
            $maxFileSize = 50 * 1024 * 1024; // 50 MB
            if($doc_size > $maxFileSize){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El archivo es demasiado grande",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit;
            }

            $uploadPath = $uploadDirectory . $doc_name;
             move_uploaded_file($doc_tmp, $uploadPath);

            // Generar la URL para guardarla en la base de datos
            $url = APP_URL . "app/uploads/documents/" . $doc_name;

            $articulo_datos_reg = [
                [
                    "campo_nombre" => "nombre",
                    "campo_marcador" => ":nombre",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "URL",
                    "campo_marcador" => ":url",
                    "campo_valor" => $url
                ],
                [
                    "campo_nombre" => "idCurso",
                    "campo_marcador" => ":idCurso",
                    "campo_valor" => $idCurso
                ]
            ];

        }else{
            $contenido = $_POST['contenido'];

            $articulo_datos_reg = [
                [
                    "campo_nombre" => "nombre",
                    "campo_marcador" => ":nombre",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "contenido",
                    "campo_marcador" => ":contenido",
                    "campo_valor" => $contenido
                ],
                [
                    "campo_nombre" => "idCurso",
                    "campo_marcador" => ":idCurso",
                    "campo_valor" => $idCurso
                ]
            ];

        }

        $crearArticulo = $this->guardarDatos("articulos", $articulo_datos_reg);

        if($crearArticulo->rowCount() == 1){
            $alerta=[
                "tipo"=>"redireccionar",
                "titulo"=>"Perfecto!",
                "texto"=>"Artículo creado con éxito",
                "icono"=>"success",
                "url" => APP_URL."admin-curso/" . $idCurso . "/"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo crear el artículo",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

    }


    //EDITAR ARTICULO
    public function editarArticulo(){
        $nombre = $this->limpiarCadena($_POST['nombreArticulo']);
        $idArticulo = $_POST['id'];

        if($nombre == ""){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos obligatorios",
                "icono"=>"error"
            ];

            return json_encode($alerta);
            exit;
        }

        //Verificando integridad de los datos
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9,() ]{3,100}", $nombre)){
            $alerta = [
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El nombre no cumple con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
            exit;
        }

        $check = $this->obtenerElemento("articulos", "idArticulo", $idArticulo);

        if(isset($_FILES['docArticulo'])){
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . "/CasaLila/app/uploads/documents/";
            $oldDoc = $check['URL'];

            $doc_name = $_FILES['docArticulo']['name'];
            $doc_size = $_FILES['docArticulo']['size'];
            $doc_tmp = $_FILES['docArticulo']['tmp_name'];

            // Comprobar el tamaño del archivo (en bytes)
            $maxFileSize = 50 * 1024 * 1024; // 50 MB
            if($doc_size > $maxFileSize){
                $alerta = [
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El archivo es demasiado grande",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
                exit;
            }

            $oldDocPath = str_replace('http://localhost/', $_SERVER['DOCUMENT_ROOT'] . '/', $oldDoc);
            if(file_exists($oldDocPath)) {
                unlink($oldDocPath);
            }

            $uploadPath = $uploadDirectory . $doc_name;
            move_uploaded_file($doc_tmp, $uploadPath);

            // Generar la URL para guardarla en la base de datos
            $url = APP_URL . "app/uploads/documents/" . $doc_name;

            $articulo_datos_act = [
                [
                    "campo_nombre" => "nombre",
                    "campo_marcador" => ":nombre",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "URL",
                    "campo_marcador" => ":url",
                    "campo_valor" => $url
                ],
                [
                    "campo_nombre" => "contenido",
                    "campo_marcador" => ":contenido",
                    "campo_valor" => null
                ],
                [
                    "campo_nombre" => "idCurso",
                    "campo_marcador" => ":idCurso",
                    "campo_valor" => $check['idCurso']
                ]
            ];
        }else{
            $oldDoc = $check['URL'];
            $oldDocPath = str_replace('http://localhost/', $_SERVER['DOCUMENT_ROOT'] . '/', $oldDoc);
            if(file_exists($oldDocPath)) {
                unlink($oldDocPath);
            }

            $contenido = $_POST['contenido'];

            $articulo_datos_act = [
                [
                    "campo_nombre" => "nombre",
                    "campo_marcador" => ":nombre",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "contenido",
                    "campo_marcador" => ":contenido",
                    "campo_valor" => $contenido
                ],
                [
                    "campo_nombre" => "URL",
                    "campo_marcador" => ":url",
                    "campo_valor" => null
                ]
            ];
        }

        $condicion = [
            "condicion_campo"=>"idArticulo",
            "condicion_marcador"=>":idArticulo",
            "condicion_valor"=> $idArticulo
        ];

        $actualizarArticulo = $this->actualizarDatos("articulos", $articulo_datos_act, $condicion);

        if($actualizarArticulo->rowCount() == 1){
            $alerta=[
                "tipo"=>"redireccionar",
                "titulo"=>"Perfecto!",
                "texto"=>"Artículo actualizado con éxito",
                "icono"=>"success",
                "url" => APP_URL."admin-curso/" . $check['idCurso'] . "/"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo crear el artículo",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }



    /* ====================== CONTROLADORES GLOBALES ======================== */


    //ELIMINAR ELEMENTO (CURSO, LECCION O ARTICULO)

    public function eliminarElemento(){
        //OBTENER DATOS
        $id = $this->limpiarCadena($_POST['id']);
        $tabla = $this->limpiarCadena($_POST['tabla']);
        $columna = $this->limpiarCadena($_POST['columna']);

        $elemento = $this->obtenerElemento($tabla,$columna,$id);

        if(is_array($elemento)){
            $oldElementPath = str_replace('http://localhost/', $_SERVER['DOCUMENT_ROOT'] . '/', $elemento['URL']);
            if (file_exists($oldElementPath)) {
                unlink($oldElementPath);
            }
        }

        $eliminarElemento=$this->eliminarRegistro($tabla,$columna,$id);

        if($eliminarElemento->rowCount() == 1){
            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Perfecto!",
                "texto"=>"Eliminado con éxito",
                "icono"=>"success"
            ];
            return json_encode($alerta);
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado!",
                "texto"=>"No se pudo eliminar",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
    }
} 