<?php


class PerfilModel
{
    private $database;
    private $resultado;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerUsuario($id){
        $sql="SELECT * FROM usuario WHERE usuario.id=$id";
        $resultado=$this->database->query($sql);
        return $resultado;
    }

    public function obtenerReservasAcreditadas($id){
        $sql='SELECT * FROM reservavuelo JOIN vuelo ON vuelo.idVuelo=reservavuelo.idVuelo WHERE reservavuelo.idUsuario='.$id.' AND Acreditada=true';
        $resultado=$this->database->query($sql);
        return $resultado;
    }
    public function obtenerReservasNoAcreditadas($id){
        $sql='SELECT * FROM reservavuelo JOIN vuelo ON vuelo.idVuelo=reservavuelo.idVuelo WHERE reservavuelo.idUsuario='.$id.' AND Acreditada=false and enEspera=false';
        $resultado=$this->database->query($sql);
        return $resultado;
    }
    public function obtenerReservasEnEspera($id){
        $sql='SELECT * FROM reservavuelo JOIN vuelo ON vuelo.idVuelo=reservavuelo.idVuelo WHERE reservavuelo.idUsuario='.$id.' AND Acreditada=false and enEspera=true';
        $resultado=$this->database->query($sql);
        return $resultado;
    }

    public function acreditarPago($id){
        $sql='UPDATE `reservavuelo` SET `Acreditada` = 1 WHERE `reservavuelo`.`id` = '.$id;
        $this->database->insert($sql);
    }
    public function darDeBajaReserva($idReserva,$cabina,$aeronave,$asiento,$idVuelo){

        $sql="delete from `reservavuelo` WHERE `id` = '$idReserva'";
        $this->database->insert($sql);
        $sql="update aeronave set $cabina=$cabina+1 where id='$aeronave'";
        $this->database->insert($sql);
        $sql="update aeronave set capacidad=capacidad+1 where id='$aeronave'";
        $this->database->insert($sql);
        $this->consultarListaDeEspera($aeronave,$cabina,$asiento,$idVuelo);
    }
    public function darDeBajaReservaEnEspera($idReserva){

        $sql="delete from `reservavuelo` WHERE `id` = '$idReserva'";
        $this->database->insert($sql);
    }
    public function consultarListaDeEspera($aeronave,$cabina,$asiento,$idVuelo){
        $sql="select * from reservaVuelo where enEspera=true and idVuelo='$idVuelo'";
        if($reservaEnEspera=$this->database->query($sql)){
            $sql="update aeronave set $cabina=$cabina-1 where id='$aeronave'";
            $this->database->insert($sql);
            $sql="update aeronave set capacidad=capacidad-1 where id='$aeronave'";
            $this->database->insert($sql);
            foreach ($reservaEnEspera as $reserva) {
                $idReserva = $reserva['id'];
                $sql = "update reservavuelo set cabina='$cabina' where id='$idReserva'";
                $this->database->insert($sql);
                $sql = "update reservavuelo set asiento='$asiento'where id='$idReserva'";
                $this->database->insert($sql);
                $sql = "update reservavuelo set enEspera=false where id='$idReserva'";
                $this->database->insert($sql);
                break;
            }
        }
    }

    public function CargaDatosDeComprobante($id){
        
        $PDFPrinter = new PDFPrinter();
        $sql = "select * from `vuelo` join `aeronave` as a on vuelo.idAeronave=a.id 
        join `origen` as o on vuelo.origen=o.id 
        join `destinos`  as d on vuelo.destino=d.id 
        join `reservavuelo` as r on vuelo.idVuelo=r.idVuelo 
        where r.idUsuario='$id'";
        $data=$this->database->query($sql);

        /**  FALTA CODIGO QR  */
        $html = "<h1>Boarding Pass</h1><br> 
        <p>Vuelo:<br> COD: ".$data[0]["codAlfanumerico"] .", Nombre: ".$data[0]["nombreVuelo"].
        "<br> Cabina tipo:".$data[0]["cabina"]." , Asiento numero: ".$data[0]["asiento"].
        "<br> Origen: ".$data[0]["origen"].
        ", Destino: ".$data[0]["destino"].", duracion: ".
       $data[0]["duracion"]." horas <br>
       Valor Acreditado: $".$data[0]["precio"];
        return $PDFPrinter->generarOutput($html);
    }
}