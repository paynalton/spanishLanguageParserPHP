<?php
global $db,$player,$room, $parser;
$db=new mysqli("localhost","root","","parser");
include "parser/parser.php";
session_start();

$parser=new parser();
$player=new pc(array_key_exists("id", $_SESSION)?$_SESSION["id"]:null);
$room=$player->getRoom();

if(array_key_exists("comando", $_POST)){
    $parser->parse($_POST["comando"]);
}

$p=array(
    "#TITULO#"=>$room->title,
    "#DESCRIPCION#"=>$room->descripcion
);

$visor=  file_get_contents("visor.html");
echo str_replace(array_keys($p),$p,$visor);

class npc{
    
}

class pc{
    private $_id;
    public function pc($id){
        if($id){
            $this->_id=$id;
            $this->updatelcon();
        }
        else {
            $this->create();
        }
    }
    public function create(){
        global $db;
        $query="insert into pc(sid) values('".($db->real_escape_string(session_id()))."')";
        $db->query($query);
        $this->_id=$db->insert_id;
        $_SESSION["id"]=$this->_id;
    }
    public function updatelcon(){
        global $db;
        $q="update pc set lastcon=NOW() where id='".$db->real_escape_string($this->_id)."'";
        $db->query($q);
    }
    public function getRoom(){
        global $db;
        $q="select room from pc where  id='".$db->real_escape_string($this->_id)."'";
        if($r=$db->query($q)){
            if($r->num_rows&&$i=$r->fetch_assoc()){
                return new habitacion($i["room"]);
            }
        }
        return new habitacion(0);
    }
}

class habitacion{
    private $_id;
    public $title="Una habitación desconocida";
    public $descripcion="Estás en un lugar extraño, cuatro paredes y un techo de un color que no alcanzas a reconocer. Este es el limbo, de donde todo viene y a donde todo va, no existe salida, no existe nada salvo la soledad eterna.";
    public function habitacion($id){
        $this->_id=$id;
    }
}


    
    