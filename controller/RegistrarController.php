<?php
class RegistrarController{


    private $registrarModel;
    private $printer;
    private $sesion;

    public function __construct($registrarModel, $printer, $sesion){
        $this->registrarModel = $registrarModel;
        $this->printer = $printer;
        $this->sesion = $sesion;
    }

    public function show(){

        $data=$this->sesion->obtenerPermisos();

        if (!$data["sesion"]){
            $data["error"]=false;
            echo $this->printer->render( "view/registrar.html", $data);}
        else{
            header("Location: /home");
        }
    }

    public function registrarUsuario(){
        $data["usuario"] = $_POST["usuario"];
        $data["password"] =  $_POST["password"];
        $data["email"] =  $_POST["email"];
        $data["rol"] = "cliente";
        $data["validacion"]=md5(time());
        if($this->registrarModel->registrarUsuario($data["usuario"],$data["password"],$data["rol"], $data["email"], $data["validacion"])){
            $this->mostrarValidacion( $data["validacion"], $data["email"]);

        }else{
            $data["error"]=true;
            echo $this->printer->render( "view/registrar.html", $data);
        }


    }

    public function mostrarValidacion($validacion, $email){
        $data=$this->sesion->obtenerPermisos();

        $data["validacion"] = $validacion;
        $data["email"]=$email;

        echo $this->printer->render( "view/validacion.html", $data);
    }

    public function validarCuenta(){
        $data["validacion"] = $_GET["validacion"];
        $data["email"]=$_GET["email"];

        $this->registrarModel->validarUsuario( $data["validacion"], $data["email"]);
        header("Location: /home");
        die();
    }




}