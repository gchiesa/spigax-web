<?
/**
 * DATATYPES
 * cgtbl
 *   oggetto per la gestione delle tabelle spigax
 */

require_once('datatype.php');

define('CGTBL_CAUSALI_MAGAZZINO',   'CM');
define('CGTBL_MONDI',               'SUGR');
define('CGTBL_MONDI_ASSOC',         'COSG');
define('CGTBL_GRUPPI_MERCEOLOGICI', 'GM');


class CGTBL extends Datatype {
    
    var $rawData; 
    
    var $type;
    var $codAA;
    var $codBBB;
    var $descriz;
    var $data; 
    
    var $cgTbl; 
    
    
    
    /**
    * costruttore della classe 
    */
    function __construct()
    {
        $this->cgTbl = array();
    }
    
    
    
    
    /**
    * carica il record e riempie la struttura dati 
    */
    function load($data)
    {
        if($data == false) {
            
            $this->rawData = false;
            return false;
            
        }
        
        $this->type = trim(substr($data, 0, 4)); 
        $this->codAA = trim(substr($data, 4, 2));
        $this->codBBB = trim(substr($data, 6, 3)); 
        $this->descriz = trim(substr($data, 20, 20));
    
        switch($this->type) { 
        
        case CGTBL_CAUSALI_MAGAZZINO:
            
            $this->data['operatVen'] = substr($data, 71, 1);
            $this->data['operatEsist'] = substr($data, 105, 1);
            break;
            
        default:
            break;
        }
        
        $this->data['type'] = $this->type;
        $this->data['codAA'] = $this->codAA;
        $this->data['codBBB'] = $this->codBBB;
        $this->data['descriz'] = $this->descriz;
        
        $this->cgTbl[$this->type.$this->codAA.$this->codBBB] = $this->data;
        
    }
    
    
    

    /**
    * CAUSALI MAGAZZINO : restituisce un array contenente le causali di vendita
    * al cliente
    */
    function CM_getCauClienteVendita()
    {
        $retArray = array();
        
        foreach($this->cgTbl as $row) {
            
            if($row['type'] != CGTBL_CAUSALI_MAGAZZINO) continue;
            
            if($row['operatVen'] == '+') $retArray[] = $row['codAA'].$row['codBBB'];
            
        }
        
        return $retArray;
    }
    
                

    
    /**
    * CAUSALI MAGAZZINO : restituisce un array contenente le causali di reso 
    * da cliente
    */
    function CM_getCauClienteReso()
    {
        $retArray = array();
        
        foreach($this->cgTbl as $row) {
            
            if($row['type'] != CGTBL_CAUSALI_MAGAZZINO) continue;
            
            if($row['operatVen'] == '-') $retArray[] = $row['codAA'].$row['codBBB'];
            
        }
        
        return $retArray;
    }
    
    
    
    
    /**
    * GRUPPI MERCEOLOGIGI
    * restituisce un array associativo tra gruppi e mondi del tipo 
    * array[<gruppo>] = <mondo>
    */
    function getGmMondiAssoc()
    {
        $retArray = array();
        
        foreach($this->cgTbl as $row) {
        
            if($row['type'] != CGTBL_MONDI_ASSOC) continue;
            
            $retArray[$row['codAA']] = substr($row['descriz'], 0, 2);
            
        }
        
        return $retArray;
    }
        
}
?>
