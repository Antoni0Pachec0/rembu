<?php

require_once("./config/app.php");
require_once("./autoload.php");


/* Si en la URL viene "views" divide su valor utilizando "/" como delimitador y lo guarda en la variable $url
la cual se convierte en un array, si no viene, establece el valor de la variable como "login" */
if(isset($_GET['views'])){
    $url=explode("/", $_GET['views']);
}else{
    $url=["inicio"];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once("./app/views/inc/head.php"); ?>
</head>
<body>
    <?php
    
    use app\controllers\viewsController;
    use app\controllers\loginController;

    $insLogin = new loginController();

    $viewsController= new viewsController();
    $vista=$viewsController->obtenerVistasControlador($url[0]);

    if($vista=="inicio" || $vista=="404"){
        require_once "./app/views/content/".$vista."-view.php";
    }else{
        require_once $vista;
    }

    
    ?>
</body>
</html>