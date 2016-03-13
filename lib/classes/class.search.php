<?
/**
 * CLASS SEARCH 
 *   gestisce la ricerca in vari moduli e restituisce una datatable cosi' composta
 *   
 *   $tblData   ['codArt']  => codice articolo
 *              ['descriz'] => descrizione
 *              ['esist']   => esistenza
 *              ['pVen']    => prezzo di vendita list 01
 *              ['pPromo']  => prezzo promo
 *              ['pPromoDval'] => data di scadenza prezzo promo
 *              
 */
require_once('../datatypes/mgart.php');
require_once('../datatypes/mgcon.php');
require_once('../datatypes/mgcaa.php');
require_once('../datatypes/mgprz.php');
require_once('class.outlet.php');
require_once('class.scf.php');

define('SEARCH_HAVE_SCF', 1);

class Search {
    
    var $im_codArt;
    
    var $dbArt;
    var $dbCon;
    var $dbCaa;
    var $dbPrz;
    
    var $Outlet;
    
    
    
   
    /**
     * costruttore dell'oggetto
     */
    function __construct(&$dbArt, &$dbCon, &$dbCaa, &$dbPrz) 
    {
        $this->dbArt = $dbArt; 
        $this->dbCon = $dbCon;
        $this->dbCaa = $dbCaa;
        $this->dbPrz = $dbPrz;
      
        $this->Outlet = new Outlet($this->dbArt, $this->dbCon, $this->dbCaa, $this->dbPrz);

    }
    
    
    
    
    /**
     * effettua la ricerca codici per codice articolo
     * restituisce la datatable (vd. inizio file)
     */
    function byCodArt($codArt, &$errors)
    {
      $tblData = array();
      $CAA = new MGCAA();
      
      $tblData[] = $result = $this->_getDataSingle($codArt, $errors);
      
      if($result == false) {       // tento la ricerca per barcode 
          
          if(SPIGAXDBG) error_log("tento la ricerca per bar code \n");
          $data = mfcdb_fetch($this->dbCaa, sprintf("%-25sE000000", $codArt), 0);
          
          if($data != false) {
              
              $CAA->load($data);
              $tblData = array();
              array_pop($errors); 
              $tblData[] = $this->_getDataSingle($CAA->getCodArt(), $errors);
          }
      }
              
      if(count($errors)) {
          
          return false;
      
      }
      
      return $tblData;
    }
    
    
    
    
    /**
     * effettua la ricerca utilizzando outlet come sorgente 
     * restituisce una datatable 
     */
    function fromMysqlByCodArt($codArt, &$errors)
    {
        $tblData = array();
        $CAA = new MGCAA();
        
        $codArts = $this->Outlet->filterInOutlet($codArt);
        
        if(!$codArts) {     // tento la ricerca per barcode 
            
            $data = mfcdb_fetch($this->dbCaa, sprintf("%-25sE000000", $codArt), 0);
            
            if($data != false) {
                
                $CAA->load($data);
                $codArt = $CAA->getCodArt();
                $codArts = $this->Outlet->filterInOutlet($codArt);
                
            }
            
        }
            
        if(!count($codArts)) {
            
            $errors[] = 'articolo non esistente in outlet';
            return false;
            
        }
        
        $tblData[] = $this->_getDataSingle($codArt, $errors);
        
        if(count($errors)) {
          
          return false;
        
        }
        
        return $tblData;
    }
    
    
    
    
    /** 
     * effettua la ricerca per range articoli.
     * restituisce una datatable e riempie l'array $errors
     */
    function byRange($codArtA, $codArtB, &$errors)
    {
        $codArtA = trim($codArtA); 
        $codArtB = trim($codArtB);
        
        $ART = new MGART();
        
        $result = mfcdb_start($this->dbArt, 0, $codArtA, MFC_ISGTEQ);
        
        if(!$result) {
            
            $errors[] = 'impossibile inizializzare la ricerca';
            return false;
        
        }
        
        $tblData = array();
        
        $data = mfcdb_curr($this->dbArt);
        $ART->load($data);
        
        
        $keylen = strlen($codArtB);
        $k = 0;
        while(strncmp($ART->codArt, $codArtB, $keylen) <= 0) {
            
            if($k++ > SITE_MAX_LOOP) break;
            
            $tblData[] = $this->_getDataSingle($ART->codArt, $errors);
            
            if(count($errors)) {
                
                return false;
                
            }
            
            /* passo alla key successiva */
            $data = mfcdb_next($this->dbArt);
            $ART->load($data);
            
        }
        
        return $tblData;
    }
    
  
    
        
    /**
     * effettua la ricerca per range utilizzanto outlet mysql come sorgente dati
     * restituisce una datatable
     */
    function fromMysqlByRange($codArtA, $codArtB, &$errors)
    {
        $codArtA = trim($codArtA); 
        $codArtB = trim($codArtB);
        
        $codArts = $this->Outlet->filterInOutlet($codArtA, $codArtB);
        
        if(!count($codArts)) {

            $errors[] = 'Nessun record trovato negli intervalli '.$codArtA.' - '.$codArtB;
            return false;
            
        }            
            
        $tblData = array();
        
        foreach($codArts as $row) {
            
            $tblData[] = $this->_getDataSingle($row['codArt'], $errors);
            
            if(count($errors)) {
                
                return false;
                
            }

        }            
        
        return $tblData;
    }
    
    
    
    
    /**
     * filtra una datatabale eliminando gli elementi con esistenza pari a zero
     * restituisce una nuova datatable
     */
    function filterEsist($data)
    {
        $tblData = array();
        
        foreach($data as $row) {
            
            if($row['esist'] == 0) continue;
            
            $tblData[] = $row;
            
        }
        
        return $tblData;
    }
    
    
    
        
    /** 
     * effettua la ricerca utilizzando come sorgente un file tipo terminalino
     * restituisce una datatable
     */
    function byEANFile($file, &$errors)
    {
        $CAA = new MGCAA();
        $tblData = array();
        
        if(!file_exists($file)) {
            
            $errors[] = 'file terminalino inesistente';
            return false;
        }
        
        $aData = file($file);
        
        foreach($aData as $row) {
            
            list($date, $hour, $EAN, $qt) = explode('|', $row);
            
            $result = mfcdb_start($this->dbCaa, 0, sprintf("%-25sE000000", substr($EAN, 0, 12)), MFC_ISEQUAL);
            $data = mfcdb_curr($this->dbCaa);
            
            /* se non trovo l'ean accodo nella tabella un array vuoto */
            if($data == false) {
                
                $tblData[] = array('codArt'=>'', 'descriz'=> 'EAN : '.$EAN.' - *** EAN NON TROVATO ***');
                continue;
                
            }
            
            $CAA->load($data);
            
            $tblData[] = $this->_getDataSingle($CAA->getCodArt(), $errors);
            
            if(count($errors)) {
                
                return false;
            }
            
        }
        
        return $tblData;
    }

    
    
    
    /**
     * effettua la ricerca utilizzando come sorgente il file terminalino e
     * incrociandolo con outlet mysql 
     * restituisce una datatable
     */
    function fromMysqlByEANFile($file, &$errors)
    {
        $CAA = new MGCAA();
        $tblData = array();
        
        if(!file_exists($file)) {
            
            $errors[] = 'file terminalino inesistente';
            return false;
        }
        
        $aData = file($file);
        
        foreach($aData as $row) {
            
            list($date, $hour, $EAN, $qt) = explode('|', $row);
            
            $result = mfcdb_start($this->dbCaa, 0, sprintf("%-25sE000000", substr($EAN, 0, 12)), MFC_ISEQUAL);
            $data = mfcdb_curr($this->dbCaa);
            
            /* se non trovo l'ean accodo nella tabella un array vuoto */
            if($data == false) {
                
                $tblData[] = array('codArt'=>'', 'descriz'=> 'EAN : '.$EAN.' - *** EAN NON TROVATO ***');
                continue;
                
            }
            
            $CAA->load($data);
            
            /* se non è in outlet proseguo */
            if(!$this->Outlet->filterInOutlet($CAA->getCodArt())) {
                
                continue;
                
            }
            
            $tblData[] = $this->_getDataSingle($CAA->getCodArt(), $errors);
            
            if(count($errors)) {
                
                return false;
            }
            
        }
        
        return $tblData;
    }




    /**
     * preleva i dati per riempire i vari campi per un elemento passato tramite
     * @param string $codArt codice articolo
     * @param array &$errors array errori da riempire se necessario
     * restituisce un array elemento di datatable
     */
    function _getDataSingle($codArt, &$errors)
    {
        $codArt = trim($codArt); 
              
        $ART = new MGART();
        $CON = new MGCON();
        $CAA = new MGCAA();
        $PRZ = new MGPRZ();
        
        $data = mfcdb_fetch($this->dbArt, sprintf("%-13s", $codArt), 0);
        
        if($data == false) {
            
            $errors[] = 'Record non trovato in magazzino articoli';
            return false;
            
        }
        
        $ART->load($data);
        
        /* leggo i dati contabili */
        $data = mfcdb_fetch($this->dbCon, sprintf("000%-13s000000", $codArt), 0);
        $CON->load($data);
        
        /* leggo il primo codice EAN */
        $result = mfcdb_start($this->dbCaa, 2, sprintf("%-13sE", $codArt), MFC_ISGTEQ);
        
        if($result) {
            
            $data = mfcdb_curr($this->dbCaa); 
            
            $CAA->load($data);
            
        }  
        
        /* leggo il prezzo 1 */
        $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $codArt), 0);
        
        $PRZ->load($data);
        $pVen = $PRZ->getP();
        $pPromo = 'n/a';
        $pPromoDval = '';
        
        if($PRZ->haveAssoc()) {
            
            $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s%-2s", $codArt, $PRZ->haveAssoc()), 0);
            
            $PRZ->load($data);
            
            if($PRZ->isActive()) {
                
                $pPromo = $PRZ->getP();
                $pPromoDval = $PRZ->getDval();
                
            }
            
        }
        
        
        $tblData = array(       'codArt'    => $codArt, 
                                'descriz'   => $ART->getDescriz(),
                                'esist'     => $CON->getEsistenza(),
                                'EAN'       => $CAA->getCode(),
                                'pVen'      => $pVen,
                                'pPromo'    => $pPromo,
                                'pPromoDval'=> $pPromoDval
                         );
        
        return $tblData;
    }
    
    
    
    
    /**
     * crea una datatable basandosi su una lista di codici articoli definiti in 
     * @param array $aCodArts array codici articoli
     * @param array &$errors array da riempire in caso di errori
     * restituisce una datatable 
     */
    function tblDataByCodArts($aCodArts, &$errors)
    {
        $tblData = array();
        
        foreach($aCodArts as $codArt) {
            
            $tblData[] = $this->_getDataSingle($codArt, $errors);
            
        }
        
        return $tblData;
    }
    
    
    
    /**
     * aggiunge ulteriori informazioni alla tblData canonica secondo $type
     * @param int $type tipo di valori da associare 
     * 					SEARCH_HAVE_SCF = aggiunge i dati di scaffale
     * 
     * @return array $retArray nuova tblData
     */
    function tblDataExtended($tblData, &$errors, $type = NULL)
    {
    	$retArray = array();
    	
    	foreach($tblData as $row) {
    		
			if($type & SEARCH_HAVE_SCF) { 		// se richiesta l'aggiunta del dato identificativo scaffale
    			
    			$scfData = SCF::getScfDescMt($row['codArt']);
    			$row['scfDescriz'] = $scfData['descriz'];
    			$row['scfMtId'] = $scfData['mt'];
    			$row['scfMt'] = $scfData['mtDesc'];
    			
    		}
    		
    		$retArray[] = $row;
    	}
    	
    	return $retArray;
    }
    
    
    
    
}
?>
