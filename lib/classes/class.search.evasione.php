<?
/**
 * CLASS SEARCH.EVASIONE
 *   oggetto per la ricerca tramite evasione
 */
 
require_once('class.search.php');
require_once('class.toolkit.php');
require_once('../datatypes/mgprz.php');
require_once('../datatypes/mgmov.php');


Class SearchEvasione extends Search {
    // -- TODO --
    
    var $dbMov;
    var $cauEvasione;
    
    
    
    /**
     * costruttore dell'oggetto
     */
    function __construct(&$dbArt, &$dbCon, &$dbCaa, &$dbPrz, &$dbMov)
    {
        parent::__construct($dbArt, $dbCon, $dbCaa, $dbPrz);
        $this->dbMov = $dbMov;
        
    }
    
    
    
    
    /**
     * imposta la causale da considerare evasione 
     */
    function setCausaleEvasione($codice)
    {
        $this->cauEvasione = $codice; 
    }
    
    
    
    
    /**
     * effettua la ricerca basandosi su numero documento e data documento di 
     * evasione. 
     * restituisce una datatable
     */
    function byEvasione($docData, $docNum, &$errors)
    {
        $MOV = new MGMOV();
        
        $docData = trim($docData);
        $docNum = trim($docNum);
        
        $docData = Toolkit::dateToSpigaXDate($docData);
        list($docNum, $docAlpha) = explode('/', $docNum);
        
        if(!is_numeric($docNum)) {
            
            $errors[] = 'numero documento non valido ('.$docNum.')';
            return false;
            
        }
        
        // inizio la scansione del db e compilo la lista di codici articoli 
        $aCodArts = array(); 
        
        if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." : chiave di ricerca |". sprintf("%-8s%1s%06s%03s", $docData, $docAlpha, $docNum, $this->cauEvasione) ."|\n");
        
        $result = mfcdb_start($this->dbMov, 1, sprintf("%-8s%1s%06s%03s", $docData, $docAlpha, $docNum, $this->cauEvasione), MFC_ISGTEQ);
        
        if(!$result) {
            
            $errors[] = 'impossibile effettuare start';
            return false;
            
        }
        
        $data = mfcdb_curr($this->dbMov);
        
        $MOV->load($data);
        
        $k = 0;
        $key = sprintf("%-8s%1s%06s%03s", $docData, $docAlpha, $docNum, $this->cauEvasione);
        $keylen = strlen($key);
        
        while(strncmp($MOV->docData.$MOV->docAlpha.$MOV->docNum.$MOV->codCau, $key, $keylen) <= 0) {
            
            if($k++ > SITE_MAX_LOOP) break;
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." : inserirsco articolo : $MOV->codArt\n");
            
            $aCodArts[] = $MOV->codArt;
        
            $data = mfcdb_next($this->dbMov);
            $MOV->load($data);
            
        }
        
        return $this->tblDataByCodArts($aCodArts, $errors);
    }

}

?>
