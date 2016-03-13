<?
/**
 * DATATYPES 
 * mgmov 
 *   oggetto per la gestione della tabella movimenti di magazzino
 */ 
require_once('datatype.php');

class MGMOV extends Datatype {

    var $rawData; 
    
    var $codMag;
    var $codArt;
    var $datReg;
    var $docData;
    var $docAlpha;
    var $docNum;
    var $progr;
    var $codCau;
    var $cfType;
    var $cfCod;
    var $qta;
    var $val;
    
    
    
    
    /**
     * carica e riempie la struttura dati
     */
    function load($data)
    {
        if($data == false) {
            
            $this->rawData = false;
            return false;    
        } 

        $this->codMag = substr($data, 0, 3); 
        $this->codArt = substr($data, 9, 13); 
        $this->datReg = substr($data, 22, 8);
        $this->docData = substr($data, 30, 8);
        $this->docAlpha = substr($data, 38, 1); 
        $this->docNum = substr($data, 39, 6); 
        $this->progr = substr($data, 45, 5);
        $this->codCau = substr($data, 65, 3);
        $this->cfType = substr($data, 70, 1); 
        $this->cfCod = $this->fromComp3(substr($data, 71, 4), 4, 0);
        $this->qta = $this->fromComp3(substr($data, 86, 6), 6, 3); 
        $this->val = $this->fromComp3(substr($data, 92, 8), 8, 2); 
    }
    
}
?>
