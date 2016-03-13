<?
/**
 * DATATYPES 
 * mgprz
 *   oggetto per la gestione della tabella prezzi
 */
 
require_once('datatype.php');

class MGPRZ extends Datatype {
    
    var $rawData;
    
    var $codMag;
    var $codArt;
    var $nPrz;
    var $prz;
    var $sco1;
    var $dVal;
    var $sco2;
    var $lAss;
    
    
    
    
    /**
     * carico e riempio la struttura dati 
     */
    function load($data)
    {
        $this->rawData = $data;
        
        if($data == NULL) return ;
        
        $this->codMag = substr($data, 0, 3);
        $this->codArt = substr($data, 3, 13);
        $this->nPrz = substr($data, 16, 2);
        $this->prz = $this->fromComp3(substr($data, 18, 8), 8, 5);
        $this->sco1 = $this->fromComp3(substr($data, 26, 4), 4, 2);
        $this->dVal = $this->fromComp3(substr($data, 30, 5), 5, 0);
        $this->sco2 = $this->fromComp3(substr($data, 35, 4), 4, 2);
        $this->lAss = substr($data, 39, 2);
        
    }
    
    
    
    
    /** 
     * restituisco il prezzo 
     */
    function getP()
    {
        if($this->rawData == NULL) return 'n/a';
        
        $p = $this->prz; 
        
        if($this->sco1 != 0 ) 
            $p = $p - round(($p / 100 * $this->sco1), 2);
        
        if($this->sco2 != 0)
            $p = $p - round(($p / 100 * $this->sco2), 2);
        
        return $p;
    }
    
    
    
    
    /**
     * restituisco il valore del listino associato se presente e valido 
     * in caso contrario restituisce false.
     */
    function haveAssoc()
    {
        if($this->rawData == NULL) return false;
        
        if($this->lAss == '00') 
            return false;
        
        $aaaa = substr($this->dVal, 0, 4);
        $mm = substr($this->dVal, 4, 2);
        $gg = substr($this->dVal, 6, 2);
        
        if(!checkdate($mm, $gg, $aaaa))
            return false;
        
        $today = date("Ymd", time());
        
        if($today >= $this->dVal)
            return sprintf("%-02s", $this->lAss);
        else 
            return false;
    }
    
    
    
    
    /**
     * restituisce true|false se il prezzo in vigore è attivo, ovvero la sua data
     * di validità non è scaduta
     */
    function isActive()
    {
        if($this->rawData == NULL) return false;
        
        $today = date("Ymd", time());
        
        if($today < $this->dVal)
            return true;
        
        return false;
    }
    
    
    
    
    /**
     * restituisce il numero listino
     */
    function numP()
    {
        if($this->rawData == NULL) return '00';
        
        return sprintf("%-02s", $this->nPrz);
    }
        
    
    
    
    /**
     * funzione statica che calcola il ricarico tra 2 prezzi e restituisce la 
     * percentuale positiva o negativa
     */
    static function getRicarico($pStart, $pStop)
    {
        if($pStart == 0 || $pStop == 0) return 'n/a';
        
        $negative = false;
        $diff = $pStop - $pStart;
        
        if($diff < 0) {
            
            $negative = true;
            $diff = $diff * (-1);
            
        }
        
        $ric = round($diff * 100 / $pStart, 2);
        
        return ($negative)?($ric * -1):($ric);
    }
        
    
    
    
    /**
     * restituisce la data di validità del prezzo nel formato gg/mm/aaaa
     */
    function getDval() 
    {
        if($this->rawData == NULL) return false;
        
        $aaaa = substr($this->dVal, 0, 4);
        $mm = substr($this->dVal, 4, 2);
        $gg = substr($this->dVal, 6, 2);
        
        if(!checkdate($mm, $gg, $aaaa))
            return false;
        
        return ($gg.'/'.$mm.'/'.$aaaa);
    }
        

    
    
    
    /** 
     * imposta la data di validità di un prezzo 
     * @param string $data data di validità nel formato gg/mm/aaaa
     */
    function setDval($data)
    {
        // 00/00/0000
        $gg = substr($data, 0, 2);
        $mm = substr($data, 3, 2);
        $aaaa = substr($data, 6, 4); 
        
        if(!checkdate($mm, $gg, $aaaa)) {
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." : data non corretta $gg / $mm / $aaaa ");
            return false;
            
        }            

        $this->dVal = $aaaa.$mm.$gg;
    }
    
    
    
    
    /**
     * azzera la data di validità
     */
    function deleteDval()
    {
        $this->dVal = '00000000';
    }
    
    
    
    
    /** 
     * azzera il listino associato
     */
    function removeAssoc()
    {
        $this->lAss = '00';
    }
    
    
    
    
    /**
     * restituisce il record binario composto dalla pacchettizzazione del 
     * oggetto
     */
    function packRecord()
    {
        $data = sprintf("%-3s%-13s%-2s%-8s%-4s%-5s%-4s%-02s%-4s%-2s", 
                            $this->codMag, 
                            $this->codArt, 
                            $this->nPrz, 
                            $this->toComp3($this->prz, MFC_COMP3_TYPE_F, 8, 5), 
                            $this->toComp3($this->sco1, MFC_COMP3_TYPE_C, 4, 2), 
                            $this->toComp3($this->dVal, MFC_COMP3_TYPE_F, 5, 0), 
                            $this->toComp3($this->sco2, MFC_COMP3_TYPE_C, 4, 2), 
                            $this->lAss, 
                            '    ', 
                            '  ');
        
        return $data;
    }

}
?>
