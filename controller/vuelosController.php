<?php


class vuelosController
{
    private $model;
    private $printer;
    private $sesion;


    public function __construct($model, $printer, $sesion){
        $this->model = $model;
        $this->printer = $printer;
        $this->sesion = $sesion;
    }

    public function show(){
        $data=$this->sesion->obtenerPermisos();
        if($data["sesion"]){
            $data["vuelos"]=$this->model->obtenerVuelos();
            echo $this->printer->render( "view/vuelosCliente.html", $data);
        }else{
            header("Location: /home");
        }
    }
    public function vuelosDisponibles(){

        $data=$this->sesion->obtenerPermisos();
        $data["vuelos"]=$this->model->obtenerVuelos();
        if($data["sesion"]==false && $data["admin"]==false){
            $data["error"]=true;
            echo $this->printer->render("view/vuelosCliente.html", $data);
        }else {
            $idVuelo=$_POST["idVuelo"];
            $data['vuelo']=$this->model->obtenerVuelosPorId($idVuelo);
            echo $this->printer->render("view/reservaVuelo.html", $data);

        }
    }
    public function procesarReserva(){
        $idUsuario=$_SESSION["id"];
        $idVuelo=$_POST["idVuelo"];
        $asiento=$_POST["asiento"];
        $cabina=$_POST["cabina"];
        $data['vuelo']=$this->model->obtenerVuelosPorId($idVuelo);

        if(!$this->model->procesarReserva($idVuelo,$idUsuario,$asiento,$cabina)){
            $data["error"]=true;
            echo $this->printer->render("view/reservaVuelo.html", $data);
        }else {
            echo "RESERVASTE EL PASAJE";
            // aca poner lo que se haga con el pdf .
            $html = "<h2>Reserva</h2>
                     <h3>Vuelo: $idVuelo</h3>
                     <h3>Asiento: $asiento</h3>
                     <h3>Cabina: $cabina</h3>";
            $this->pdf->render($html, "archivardo.pdf", 0);
        }
    }
}
