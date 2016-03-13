<?
/**
 * BCODE
 *   oggetto per la stampa barcode su stampanti allegro
 */
require_once('class.bcode.php');


Class BCodeAllegro extends BCode {

    var $fileEti;
    var $dirTmp;
    var $data;
    var $flTwinEti; 
    
    
    
    
    /**
     * costruttore della classe 
     */
    function BCodeAllegro(&$dbArt, &$dbCaa, &$dbPrz, $fileEti, $dirTmp)
    {
        parent::__construct($dbArt, $dbCaa, $dbPrz);
        $this->flTwinEti = false;
        $this->fileEti = $fileEti;
        $this->dirTmp = $dirTmp;
        $this->load();
    }
        
    
    
    
    /**
     * carica il tracciato specificato in fase di istanza (file tipo eti)
     */
    function load()
    {
        if(!file_exists($this->fileEti)) {
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." : file etichetta non trovato");
            return false;
            
        }
        
        $data = file($this->fileEti);
        
        if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__.' - '.print_r($data, true));
        
        foreach($data as $row) {
            
            $row = trim($row);
            $index = substr($row, 0, 2);
            if(is_numeric($index[0]) && is_numeric($index[1]) ) {
                
                $this->data[$index] = substr($row, 2);
                
                // se index > 10 allora è un file a doppia etichetta
                if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - verifico l'indice $index per twin eti");
                $tmpTwinEti = round($index);
                if($tmpTwinEti > 10 && $tmpTwinEti < 20) {
                	
                	if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - etichetta twin su tracciato $this->fileEti, index = $index");
                	$this->flTwinEti = true;
                	
                } 
                
            }
        } // end foreach
    }
    
    
    
    
    /**
    * restituisce l'etichetta singola pronta 
    * 
    * NOTE: $aData è un array associativo con 
    * ['descrizione1']
    * ['descrizione2']
    * ['prezzoE']
    * ['prezzoL']
    * ['barcode']
    * ['codart']
    * 
    * @param array $aData file etichetta 
    * @param array $aData2 file etichetta 2 per tracciati a 2 etichette per stampa
    */    
    function getEtiSingle($aData, $aData2 = null) 
    {
        if($this->data == false) return false;
        
        $retData = array();
        $retData[] = "\x0d\x0a";
        $retData[] = $this->data['00']."\x0a";
        
        $mapFields = array( '06' => 'barcode',
                            '02' => 'descrizione1', 
                            '03' => 'descrizione2', 
                            '04' => 'codart', 
                            '05' => 'prezzoE', 
                            '07' => 'prezzoL'); 
        
        $mappedFields = array_keys($mapFields);
        
        foreach($mapFields as $key=>$value) {
            
            $retData[] = $this->data[$key].$aData[$value]."\x0d\x0a";
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - aggiungo ".bin2hex($this->data[$key].$aData[$value]."\x0d\x0a"));            
            
        }
        
        /*
         * se è un tracciato a 2 etichette per pista processo anche l'array $aData2
         */
        if($this->flTwinEti) {
        	
	        foreach($mapFields as $key=>$value) {
	            
	            $retData[] = $this->data[$key+10].$aData2[$value]."\x0d\x0a";
	            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - aggiungo Pista2 ".bin2hex($this->data[$key+10].$aData[$value]."\x0d\x0a"));            
	            
	        }
        	
        }
        
        // chiusura tracciato
        if($this->data['99'] == 'E') {
            
            $retData[] = $this->data['99']."\x0a";
            
        }        
        
        return implode('', $retData); 
    }
    
    
    
    
    /**
     * restituisce i dati binari della stampa di etichette multiple 
     * @param arrat $aMulti array contenente i dati dell'articolo di ogni 
     *                      elemento dell'etichetta
     */
    function getEtiMulti($aMulti)
    {
        if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - eti data: ".print_r($this->data, true));
        if($this->data == false) return false;
        
        $retData = '';
        
        $k = 0;
        for ($k=0; $k < count($aMulti); $k++) {
        	
        	$aData = $aMulti[$k];	// etichetta
        	
        	/*
        	 * se è un etichetta a 2 piste allora leggo anche l'etichetta successiva altrimenti 
        	 * la setto a null
        	 */
        	if($this->flTwinEti) $aData2 = $aMulti[++$k];
        	else $aData2 = null;

        	$retData .= $this->getEtiSingle($aData, $aData2);
        	
        }
        
        return $retData;
    }
    
    
    

}
?>
