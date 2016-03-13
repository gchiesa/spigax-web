<?
/**
 * DATATYPE 
 * mgart
 *   oggetto per la gestione della struttura dati articolo.
 */
require_once('datatype.php');
 
class MGART extends Datatype {
    
    var $rawData;
    
    var $codArt;
    var $descriz;
    var $ultAcq;
    var $ultCA;
    var $lastFor;
    var $gm_gr;
    var $gm_sgr;
    
    
    
    
    /**
     * carica il record e riempie la struttura dati 
     */
    function load($data)
    {
        $this->rawData = $data;
        
        if($data == NULL) return;
        
        $this->codArt = substr($data, 0, 13);
        $this->descriz = substr($data, 13, 30);
        $this->descriz2 = substr($data, 43, 30);
        
        $this->gm_gr = substr($data, 89, 2);
        $this->gm_sgr = substr($data, 91, 3);
        
        $this->lastFor = $this->fromComp3(substr($data, 133, 4), 4, 0);
        $this->ultAcq = $this->fromComp3(substr($data, 318, 5), 5, 0);
        $this->ultCA = $this->fromComp3(substr($data, 323, 8), 8, 5);
    }
    
    
    
    
    /**
     * restituisce la data dell'ultimo acquisto in formato gg/mm/aaaa oppure
     * n/a se non disponibile
     */
    function getUltAcq()
    {
        if($this->rawData == NULL) return 'n/a';
        
        $aaaa = substr($this->ultAcq, 0, 4);
        $mm = substr($this->ultAcq, 4, 2);
        $gg = substr($this->ultAcq, 6, 2);
        
        if(!checkdate($mm, $gg, $aaaa))
            return false;
        
        return $gg.'/'.$mm.'/'.$aaaa;
    }
    
    
    
    
    /** 
     * restituisce la descrizione dell'articolo
     */
    function getDescriz()
    {
        if($this->rawData == NULL) return 'n/a';
        
        return $this->descriz.' '.$this->descriz2;
    }
    
    
    
    
    /** 
     * restituisce il codice dell'ultimo fornitore per l'articolo
     */
    function getLastFor()
    {
        if($this->rawData == NULL) return 'n/a';
        
        return ($this->lastFor!=0)?$this->lastFor:NULL;
    }
    
    
    
    
    /** 
     * restituisce il valore dell'ultimo costo d'acquisto dell'articolo
     */
    function getUltCA()
    {
        if($this->rawData == NULL) return 0; 
        
        return round($this->ultCA, 2);
    }
    
    
}

?>
