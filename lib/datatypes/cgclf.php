<?
/**
 * DATATYPES 
 * cgclf
 *   oggetto per la gestione della tabella clienti/fornitori
 */

require_once('datatype.php');

class CGCLF extends Datatype {
    
    var $rawData;
    
    var $type;
    var $codFor;
    var $ragSoc;
    var $artPfx;
    
    
    /**
    * carica il record e riempie l'oggetto 
    */
    function load($data)
    {
        $this->rawData = $data;

        if($data == NULL) return;
        
        $this->type = substr($data , 0, 1);
        $this->codFor = substr($data, 1, 6);
        $this->ragSoc = substr($data, 7, 40);
        
    }
    
    
    
    
    /**
    * restituisce la Ragione Sociale
    */
    function getRagSoc()
    {
        if($this->rawData == NULL) return 'n/a';
        
        return $this->ragSoc;
    }
    
    
    
    
    /**
    * restituisce il codice cliente 
    */
    function getCod()
    {
        if($this->rawData == NULL) return 'n/a';

        return $this->codFor;
    }

    
    
    
}
?>
