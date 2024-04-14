<?php
	
	namespace app\models;
	use app\controllers\loginController;

	class viewsModel extends loginController{

		/*---------- Modelo obtener vista ----------*/
		protected function obtenerVistasModelo($vista){

			$listaBlanca=["prueba", "admin-perfil", "editor", "form-articulo", "form-curso", 
            "form-leccion", "academicos", "culturales", "verano", "login", "logout",
            "articulo", "curso", "leccion", "perfil", "recuperar-contraseña", "redactar-articulo"];

			if(in_array($vista, $listaBlanca)){
				if(is_file("./app/views/content/global/".$vista."-view.php")){
					$contenido="./app/views/content/global/".$vista."-view.php";	
				}else{
					$contenido="404";
				}

			}else if($vista=="inicio" || $vista=="index"){
				$contenido="inicio";

			}else{
				$contenido="404";
			}

			return $contenido;
		}

	}