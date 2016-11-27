<?php
require_once './PeopleDB.php'; //Incluyo el archivo 'PeopleDB.php'

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PeopleAPI
 *
 * @author sebas
 */
class PeopleAPI {    
    
     /**
     * Respuesta al cliente
     * @param int $code Codigo de respuesta HTTP
     * @param String $status indica el estado de la respuesta puede ser "success" o "error"
     * @param String $message Descripcion de lo ocurrido
     */
     function response($code=200, $status="", $message="") {
        http_response_code($code);
        if( !empty($status) && !empty($message) ){
            $response = array("status" => $status ,"message"=>$message);  
            echo json_encode($response,JSON_PRETTY_PRINT);    
        }            
     }   
    
     /**
     * función que segun el valor de "action" e "id":
     *  - mostrara una array con todos los registros de personas
     *  - mostrara un solo registro 
     *  - mostrara un array vacio
     */
        function getPeoples(){
            if($_GET['action']=='peoples'){         
                $db = new PeopleDB();
                if(isset($_GET['id'])){//muestra 1 solo registro si es que existiera ID                 
                    $response = $db->getPeople($_GET['id']);                
                    echo json_encode($response,JSON_PRETTY_PRINT);
                }else{ //muestra todos los registros                   
                    $response = $db->getPeoples();              
                    echo json_encode($response,JSON_PRETTY_PRINT);
                }
            }else{
                $this->response(400);
            }       
        }
    
    /**
    * metodo para guardar un nuevo registro de persona en la base de datos
    */
    function savePeople(){
        if($_GET['action']=='peoples'){   
            //Decodifica un string de JSON
            $obj = json_decode( file_get_contents('php://input') );   
            $objArr = (array)$obj;
            if (empty($objArr)){
               $this->response(422,"error","Nada que añadir. Chequear Json");                           
            }else if(isset($obj->name)){
               $people = new PeopleDB();     
               $people->insert( $obj->name );
               $this->response(200,"success","Nuevo dato añadido");                             
            }else{
                $this->response(422,"error","The property is not defined");
            }
        } else{               
            $this->response(400);
        }  
    }
    
    /**
    * Actualiza un recurso
    */
    function updatePeople() {
        if( isset($_GET['action']) && isset($_GET['id']) ){
            if($_GET['action']=='peoples'){
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){                        
                    $this->response(422,"error","Nada que añadir. Chequear Json");                        
                }else if(isset($obj->name)){
                    $db = new PeopleDB();
                    $db->update($_GET['id'], $obj->name);
                    $this->response(200,"success","Dato actualizado");                             
                }else{
                    $this->response(422,"error","La propiedad no esta definida");                        
                }     
                exit;
           }
        }
        $this->response(400);
    }
    
    /**
     * elimina una persona
     */
    function deletePeople(){
        if( isset($_GET['action']) && isset($_GET['id']) ){
            if($_GET['action']=='peoples'){                   
                $db = new PeopleDB();
                $db->delete($_GET['id']);
                $this->response(204);                   
                exit;
            }
        }
        $this->response(400);
    }
        
    public function API(){
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getPeoples();
            //echo 'GET';
            break;     
        case 'POST'://inserta
            $this->savePeople();
            //echo 'POST';
            break;                
        case 'PUT'://actualiza
            $this->updatePeople();
            //echo 'PUT';
            break;      
        case 'DELETE'://elimina
            $this->deletePeople();
            //echo 'DELETE';
            break;
        default://metodo NO soportado
            echo 'METODO NO SOPORTADO';
            break;
        }
    }
    
    
    
    
}//end class

