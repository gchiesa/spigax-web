<?
/**
 * DATATYPES
 * mgcaa
 *   oggetto per la gestione dei codici alternativi
 */
require_once('datatype.php');

class MGCAA extends Datatype {

    var $rawData; 
    
    var $codArt;
    var $type;
    var $code;
    
    
    
    
    /**
     * carica e riempie la struttura dati 
     */
    function load($data) 
    {
        $this->rawData = $data;
        
        if($data == NULL) return;
        
        $this->codArt = substr($data, 64, 13);
        $this->code = substr($data, 0, 25);
        $this->type = substr($data, 25, 1);
        
    }
    
    
    
    
    /** 
     * restituisce il codice alternativo
     */
    function getCode()
    {
       if($this->rawData == NULL) {
          
          return 'n/a';
          
       }
       
       return trim($this->code);
    }
    
    
    
    
    /**
     * restituisce il tipo di codice alternativo 
     * es. E|Z|M|F etc
     */
    function getType()
    {
       if($this->rawData == NULL) {
          
          return 'n/a';
          
       }

       return $this->type;
    }
    
    
    
    
    /**
     * restituisce il codice articolo a cui è associato il codice alternativo
     */
    function getCodArt()
    {
        if($this->rawData == NULL) {
            
            return 'n/a';
            
        }
        
        return $this->codArt;
    }
            
                
}    
?>
