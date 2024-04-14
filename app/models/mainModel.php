<?php

namespace app\models;
use \PDO;

if(file_exists(__DIR__."/../../config/server.php")){
	require_once __DIR__."/../../config/server.php";
}

class mainModel{
    private $server=DB_SERVER;
	private $db=DB_NAME;
	private $user=DB_USER;
	private $pass=DB_PASS;


    //------- CONEXION A LA BASE DE DATOS -------//
    protected function conectar(){
        $conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db,$this->user,$this->pass);
        $conexion->exec("SET CHARACTER SET utf8");
        return $conexion;
    }


    //-------EJECUTAR CONSULTAS PREPARADAS-------//
    /* Recibe una consulta como parámetro y la ejecuta */
    protected function ejecutarConsulta($consulta){
        $stmt = $this->conectar()->prepare($consulta);
        $stmt->execute();
        return $stmt;
    }


    //--------LIMPIAR CADENA-------//
    /*Recibe una cadena como parámetro y la analiza en busca de palabras clave que se pueden usar para inyectar
    codigo, elimina esas palabras y devuelve la cadena "limpia" para evitar ataques */
    public function limpiarCadena($cadena){

        $palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM","INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==","=",";","::"];

        $cadena=trim($cadena);
        $cadena=stripslashes($cadena);

        foreach($palabras as $palabra){
            $cadena=str_ireplace($palabra, "", $cadena);
        }

        $cadena=trim($cadena);
        $cadena=stripslashes($cadena);

        return $cadena;
    }


    //-------VERIFICAR DATOS-------//
    /* Expresion regular que analiza una cadena en busca de caracteres especiales.
    Siempre que se vaya a analizar la cadena, se debe especificar tambien la longitud minima y maxima
    respectivamente entre llaves y separada por comas, por ejemplo: {3, 40}, es decir, la cadena debe tener
    entre 3 y 40 caracteres, en caso de no ponerlo es posible que ocurran errores que no se especifican 
    bien en consola, casi me suicido por un error de esos, pilas mi rey */
    protected function verificarDatos($filtro,$cadena){
        if(preg_match("/^".$filtro."$/", $cadena)){
            return false;
        }else{
            return true;
        }
    }


    //Funcion preparada INSERT para guardar datos
    protected function guardarDatos($tabla, $datos){

        /*En la variable $query se va a guardar un string que servirá como la consulta, primero la
        inicializamos declarando INSERT INTO y la variable tabla donde se guardarán los datos.*/
        $query = "INSERT INTO $tabla (";

        $c = 0;

        /* El siguiente foreach va a servir para concatenar en la consulta las columnas donde se insertarán
        los datos, esas columnas vienen en la variable $datos, el cual debe ser un array multidimensional, y
        cada array asociativo debe tener tres partes: campo_nombre, campo_marcador, campo_valor, con sus valores asignados */
        foreach($datos as $clave){
            if($c >= 1){$query .= ",";}
            $query .= $clave['campo_nombre'];
            $c++;
        }

        $query .= ") VALUES (";

        /* Este foreach concatena en la consulta los marcadores de valor, para tener una consulta segura */
        $c = 0;
        foreach($datos as $clave){
            if($c >= 1){$query .= ",";}
            $query .= $clave['campo_marcador'];
            $c++;
        }

        $query .= ")";

        $sql = $this->conectar()->prepare($query);

        /* Este foreach asigna los valores a cada marcador de la consulta */
        foreach($datos as $clave){
            $sql -> bindParam($clave['campo_marcador'], $clave['campo_valor']);
        }

        $sql -> execute();

        return $sql;
    }

    //SENTENCIA PREPARADA UPDATE

    protected function actualizarDatos($tabla, $datos, $condicion){
        $query = "UPDATE $tabla SET ";

        $c = 0;

        foreach ($datos as $clave){
            if($c>=1){ $query.=","; }
            $query.=$clave["campo_nombre"]."=".$clave["campo_marcador"];
            $c++;
        }

        $query.=" WHERE ".$condicion["condicion_campo"]."=".$condicion["condicion_marcador"];

        $sql = $this->conectar()->prepare($query);

        foreach($datos as $clave){
            $sql -> bindParam($clave['campo_marcador'], $clave['campo_valor']);
        }

        $sql->bindParam($condicion["condicion_marcador"],$condicion["condicion_valor"]);

        $sql -> execute();
        
        return $sql;
    }

    //ELIMINAR REGISTROS
    protected function eliminarRegistro($tabla,$campo,$id){
        $sql=$this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
        $sql->bindParam(":id",$id);
        $sql->execute();
        
        return $sql;
    }
}