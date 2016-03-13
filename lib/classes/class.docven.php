<?
/**
 * DOCVEN 
 *   oggetto per la gestione dei documenti di vendita e statistiche del venduto
 */
require_once('../datatypes/mgart.php');
require_once('../datatypes/mgmov.php');
require_once('../datatypes/cgtbl.php');
require_once('../datatypes/mgprz.php');


class DocVen {
    
    var $dbArt;
    var $dbMov;
    var $dbTbl;
    var $cauVen;
    var $cauReso;
    var $gmMondi; 
    
    
    
    
    /**
     * costruccore della classe
     * verifica che esista la tabella outlet_docven
     */
    function DocVen(&$dbArt, &$dbMov, &$dbTbl)
    {
        $this->dbArt = $dbArt;
        $this->dbMov = $dbMov;
        $this->dbTbl = $dbTbl;
        
        $this->checkInstall();
        
    }
     
    
    
        
    /**
     * carica nella struttura dell'oggetto l'elenco dei Mondi dei gruppi 
     * merceologici 
     */
    function loadMondi()
    {
        $TBL = new CGTBL();
        
        $result = mfcdb_start($this->dbTbl, 0, sprintf("%-4s", CGTBL_MONDI_ASSOC), MFC_ISGTEQ);
        
        if(!$result) {
            
            return false;
            
        }
        
        $data = mfcdb_curr($this->dbTbl);
        
        $TBL->load($data);
        
        $len = strlen(CGTBL_MONDI_ASSOC); 
        while(strncmp($TBL->type, CGTBL_MONDI_ASSOC, $len) <= 0) {
            
            $data = mfcdb_next($this->dbTbl);
            $TBL->load($data);
            
        }

        $this->gmMondi = $TBL->getGmMondiAssoc();

        return true;
    }
    
    
        
        
    /**
     * carica nella struttura le causali di vendita e reso vs. e da cliente 
     */
    function loadCauVR()
    {
        $TBL = new CGTBL();
        
        $result = mfcdb_start($this->dbTbl, 0, sprintf("%-4s", CGTBL_CAUSALI_MAGAZZINO), MFC_ISGTEQ);
        
        if(SPIGAXDBG) error_log("__FUNCTION__  : result $result \n");
        if(!$result) {
            
            return false;
            
        }
        
        $data = mfcdb_curr($this->dbTbl);
        
        $TBL->load($data);
        
        $len = strlen(CGTBL_CAUSALI_MAGAZZINO); 
        while(strncmp($TBL->type, CGTBL_CAUSALI_MAGAZZINO, $len) <= 0) {
            
            if(SPIGAXDBG) error_log("leggo causale ".$TBL->type.'-'.$TBL->codAA.'-'.$TBL->codBBB."\n");
            $data = mfcdb_next($this->dbTbl);
            
            if(!$data) break;
            
            $TBL->load($data);
            
        }
        
        $this->cauVen = $TBL->CM_getCauClienteVendita();
        $this->cauReso = $TBL->CM_getCauClienteReso();
        
        return true;
    }
    
    
    
    
    /**
     * restituisce la data iniziale da cui è necessario eseguire la lettura dei 
     * movimenti di magazzino per riempire il database mysql con i documenti 
     * di vendita
     */
    function getStartTimeToReadMovMag()
    {
        $q = mysql_query("SELECT MIN(docven_date) as date FROM outlet");
        
        $row = mysql_fetch_assoc($q); 
        
        return ($row['date']); 
    }
    
    
    
    
    /**
     * aggiorna il database mysql con i documenti di vendita per ogni articolo
     * inserito in outlet 
     */
    function updateDocVen()
    {
        $today = date("Y-m-d", time());
        $timeToday = strtotime($today.' 12:00:00');
        
        if(SPIGAXDBG) error_log("carico causali...\n");
        if(!$this->loadCauVR()) {
            
            return false;
        
        }
        
        if(SPIGAXDBG) error_log("causali vend ".print_r($this->cauVen, true)." \n causali reso ".print_r($this->cauReso, true)."\n");
        /* carico le associazioni mondi gruppi */
        $this->loadMondi(); 
        
        /* per ogni articolo in outlet effettuo la lettura movimenti */        
        $q = mysql_query("SELECT codart, docven_date FROM outlet ");
        
        while($row = mysql_fetch_assoc($q)) {
            
            $result = $this->updateDocVenByCodArt($row['codart'], $row['docven_date']);
            
            if($result) {
                
                $q1 = mysql_query("UPDATE outlet SET docven_date = '".$timeToday."' WHERE codart = '".$row['codart']."' ");
                
            }
            
        }
        
        return true;
    }
    
    
    
        
    /**
     * aggiorna i documenti di vendita sul database mysql interrogando i 
     * movimenti di magazzino 
     * @param string $codArt codice articolo per il quale leggere e aggiornare
     *                       i documenti di vendita
     * @param string $docven_date data di inizio lettura (formato unixtime)
     */
    function updateDocVenByCodArt($codArt, $docven_date)
    {
        $timeStart = date("Ymd", $docven_date);
        $timeStop = date("Ymd", time());
        
        if(SPIGAXDBG) error_log("analisi articolo $codArt partendo dalla data $timeStart\n");
        
        /* elimino dal db outlet_docven i movimenti del timeStart in modo che non ci siano doppioni con il nuovo inserimento */
        $q = mysql_query("DELETE FROM outlet_docven WHERE codart = '".$codArt."' AND doc_data >= '".$docven_date."' "); 
        
        $MOV = new MGMOV();
        
        if(SPIGAXDBG) error_log("key di partenza ".sprintf("000000000%-13s%-8s%-8s", $codArt, $timeStart, $timeStart)."\n");
        $result = mfcdb_start($this->dbMov, 0, sprintf("000000000%-13s%-8s%-8s", $codArt, $timeStart, $timeStart), MFC_ISGTEQ);
        
        if(!$result) {
            
            return false;
            
        }
        
        $data = mfcdb_curr($this->dbMov);
        
        $MOV->load($data);

        $k = 0;
        
        while(strncmp($MOV->codArt, sprintf("%-13s", $codArt), 13) <= 0) {
            
            if(SPIGAXDBG) error_log("analizzo movimento n. ".$MOV->progr." ( ".$MOV->codArt."-".$MOV->datReg."-".$MOV->docData."-".$MOV->docNum."-".$MOV->docAlpha." causale ".$MOV->codCau." \n");
            if($k++ > 10) break;
            if(in_array($MOV->codCau, $this->cauVen)) {
                
                $type = 'V';
                
            } else if(in_array($MOV->codCau, $this->cauReso)) {
                
                $type = 'R';
                
            } else {
                
                $data = mfcdb_next($this->dbMov);
                $MOV->load($data);
                
                continue;
            }

            if(SPIGAXDBG) error_log("tento inserimento di movimento n. ".$MOV->progr." ( ".$MOV->codArt."-".$MOV->datReg."-".$MOV->docData."-".$MOV->docNum."-".$MOV->docAlpha." causale ".$MOV->codCau." \n");
            /* leggo i dati gruppo e sottogruppo dell'articolo */
            $data = mfcdb_fetch($this->dbArt, sprintf("%-13s", $MOV->codArt), 0);
            
            $ART = new MGART();
            $ART->load($data); 
            
            $mondo = $this->gmMondi[$ART->gm_gr]; 
            $ric = MGPRZ::getRicarico($ART->getUltCA(), round($MOV->val / $MOV->qta, 2));
            $ric = ($ART->getUltCA() > 0)?$ric:DOCVEN_DEFAULT_RIC;      // se non  è calcolabile il ricarico lo setto a ricarico di default
            
            if(SPIGAXDBG) error_log("ult ca : $ART->ultCA , ric $ric \n");
            $valV = ($type=='V')?$MOV->val:'0';
            $valR = ($type=='R')?$MOV->val:'0';
            
            $sql = "INSERT INTO outlet_docven (type, doc_data, doc_num, doc_alpha, codcau, codcli, qt, val, val_v, val_r, ric, mondo, gruppo, sottogruppo, codfor, codart) VALUES ( ".
                   "'" . $type . "', ".
                   "'" . MGMOV::fromDateToTime($MOV->docData) . "', ".
                   "'" . $MOV->docNum . "', ".
                   "'" . $MOV->docAlpha . "', ".
                   "'" . $MOV->codCau . "', ".
                   "'" . $MOV->cfCod . "', ".
                   "'" . $MOV->qta . "', ".
                   "'" . $MOV->val . "', ".
                   "'" . $valV . "', ".
                   "'" . $valR . "', ".
                   "'" . $ric . "', ".
                   "'" . $mondo . "', ".
                   "'" . $ART->gm_gr . "', ".
                   "'" . $ART->gm_sgr . "', ".
                   "'" . $ART->lastFor . "', ".
                   "'" . $MOV->codArt . 
                   " ' ) ";
                   
            $q = mysql_query($sql);
            
            $data = mfcdb_next($this->dbMov);
            $MOV->load($data);
        }            
    
        return true;
    }
    
    
    

    /**
     * converte la data in formato unixtime 
     * @param string $data data da convertire in formato gg/mm/aaaa
     */
    function dateToTime($data) // formato dd/mm/yyyy
    {
        $dd = substr($data, 0, 2); 
        $mm = substr($data, 3, 2); 
        $yyyy = substr($data, 6, 4);
        
        if(!checkdate($mm, $dd, $yyyy)) {
            
            return false;
            
        }
        
        return strtotime($yyyy.'-'.$mm.'-'.$dd.' 12:00:00');
    }
    
    
    
    
    /**
     * crea le statistiche di tipo semplice
     *
     * | venduto | reso | venduto effettivo | perc.ricarico medio |
     *
     * @param array $aPost array contenente le informazioni per costruire la 
     *                     query quali:
     *                      ['codArtA'] articolo/maschera iniziale
     *                      ['codArtB'] articolo/maschera finale
     *                      ['dateA'] data iniziale 
     *                      ['dateB'] data finale
     *                      ['codArtAll'] flag true|false -> tutti gli articoli
     *                      ['dateAll'] flag true|false -> qualsiasi data 
     *
     */
    function getStatsSimple($aPost)
    {
        if(SPIGAXDBG) error_log("array ".print_r($aPost, true));
        $codArtA = $aPost['codArtA'];
        $codArtB = $aPost['codArtB'];
        $dateA = $aPost['dateA'];
        $dateB = $aPost['dateB'];
        $flCodArtAll = $aPost['codArtAll'];
        $flDateAll = $aPost['dateAll'];
        
        $whereClause = array();
        
        if(!$flCodArtAll) {
            
            $whereClause[] = " codart >= '".$codArtA."' AND codart <= '".$codArtB."' ";
            
        }
        
        if(!$flDateAll) {
            
            $whereClause[] = " doc_data >= '".$this->dateToTime($dateA)."' AND doc_data <= '".$this->dateToTime($dateB)."' ";
            
        }
        
        // eseguo la query 
        $sql = "SELECT SUM(val_v) AS venduto, SUM(val_r) AS reso, AVG(ric) as ric FROM outlet_docven ";
        
        if(count($whereClause)) {
            
            $sql .= " WHERE ".implode('AND', $whereClause);
            
        }
        
        if(SPIGAXDBG) error_log("query : $sql\n");
        
        $q = mysql_query($sql);
        
        if(!$q) return false; // errore
        
        $row = mysql_fetch_assoc($q); 
        
        $retArray = array();
        
        $retArray['venduto'] = $row['venduto'];
        $retArray['reso'] = $row['reso'];
        $retArray['ric'] = $row['ric'];
        $retArray['saldo'] = round($retArray['venduto'] - $retArray['reso'], 2);
        
        return $retArray;
    }
    
        
    
    
    function getStatsByMGS($aPost)    
    {
        if(SPIGAXDBG) error_log("array ".print_r($aPost, true));
        $codArtA = $aPost['codArtA'];
        $codArtB = $aPost['codArtB'];
        $dateA = $aPost['dateA'];
        $dateB = $aPost['dateB'];
        $flCodArtAll = $aPost['codArtAll'];
        $flDateAll = $aPost['dateAll'];
    
        $whereClause = array();
        
        if(!$flCodArtAll) {
            
            $whereClause[] = " codart >= '".$codArtA."' AND codart <= '".$codArtB."' ";
            
        }
    
        if(!$flDateAll) {
            
            $whereClause[] = " doc_data >= '".$this->dateToTime($dateA)."' AND doc_data <= '".$this->dateToTime($dateB)."' ";
            
        }
        
        /* eseguo la query per i gruppi merceologici */
        $sql = "SELECT mondo, gruppo, sottogruppo, COUNT(id) AS elementi, SUM(val_v) AS venduto, SUM(val_r) AS reso, ROUND(AVG(ric), 2) AS ric FROM outlet_docven ";
        
        if(count($whereClause)) {
            
            $sql .= " WHERE ".implode(' AND ', $whereClause);
            
        }
        
        // GROUPING BY 
        $sql .= " GROUP BY mondo, gruppo, sottogruppo WITH ROLLUP ";
        
        if(SPIGAXDBG) error_log("query: $sql\n");
        
        $q = mysql_query($sql);

        if(!$q || !mysql_num_rows($q) ) return false; // errore o zero righe
        
        // preparo l'array finale 
        // per ogni riga carico i mondi gruppi e sottogruppi
        
        $retArray = array();
        
        while($row = mysql_fetch_assoc($q)) {
            
            if(is_numeric($row['mondo'])) $row['mondo_d'] = $this->getGM($row['mondo'], NULL, NULL);
            
            if(is_numeric($row['gruppo'])) $row['gruppo_d'] = $this->getGM(NULL, $row['gruppo'], NULL);
            
            if(is_numeric($row['sottogruppo'])) $row['sottogruppo_d'] = $this->getGM(NULL, NULL, $row['gruppo'].$row['sottogruppo']);
            
            $row['saldo'] = round($row['venduto'] - $row['reso'], 2);
            
            $retArray[] = $row; 
            
        }
        
        return $retArray;
    }
            
            
        
        
    /**
     * restituisce il mondo, gruppo o sottogruppo merceologico in base ai 
     * parametri passati. 
     * se rischiesto un solo valore restituisce una variabile singola altrimenti
     * restituisce un array
     */           
    function getGM($mondo = NULL, $gruppo = NULL, $sottogruppo = NULL) 
    {
        $retArray = array();
        $TBL = new CGTBL();
        
        // preparo se presente il valore di mondo
        if($mondo != NULL) {
            
            $data = mfcdb_fetch($this->dbTbl, sprintf("%-4s%-2s%-3s%-11s", CGTBL_MONDI, $mondo, '', ''), 0);
            
            if($data != false) {
                
                $TBL->load($data);
                
                $retArray[] = $TBL->descriz;
                
            }
            
        }
        
        // preparo se richiesto il valore di gruppo
        if($gruppo != NULL) {
            
            $data = mfcdb_fetch($this->dbTbl, sprintf("%-4s%-2s%-3s%-11s", CGTBL_GRUPPI_MERCEOLOGICI, $gruppo, '', ''), 0);
            
            if($data != false) {
                
                $TBL->load($data);
                
                $retArray[] = $TBL->descriz;
            
            }
            
        }
        
        // preparo se richiesto il valore di sottogruppo
        if($sottogruppo != NULL) {
            
            if(SPIGAXDBG) error_log("cerco sottogruppo con key |".sprintf("%-4s%-2s%-3s%-11s", CGTBL_GRUPPI_MERCEOLOGICI, $gruppo, $sottogruppo, '')."|\n");
            
            $data = mfcdb_fetch($this->dbTbl, sprintf("%-4s%-5s%-11s", CGTBL_GRUPPI_MERCEOLOGICI, $sottogruppo, ''), 0);
            
            if($data != false) {
                
                $TBL->load($data);
                
                $retArray[] = $TBL->descriz;
                
            }
            
        }
        
        
        // restituisco un array se più di un elemento altrimenti una variabile
        if(count($retArray) > 1) {
            
            return $retArray;
            
        } 
        
        return array_pop($retArray); 
    }
        
        
            
            
            
        
        
    /**
     * verifica se la tabella outlet_docven è installata correttamente 
     */
    function checkInstall()
    {
        $q = mysql_query("SELECT COUND(id) FROM outlet_docven"); 
        
        if(!$q) {
            
            $this->initialize();
            return true;
        }
        
        return true;
    }
    
    
    
        
    /**
     * installa la tabella outlet_docven
     */
    function initialize()
    {
        
        $sql  = "
        CREATE TABLE IF NOT EXISTS `outlet_docven` (
          `id` bigint(20) unsigned NOT NULL auto_increment,
          `type` varchar(1) NOT NULL,
          `doc_data` bigint(20) NOT NULL,
          `doc_num` int(11) NOT NULL,
          `doc_alpha` varchar(1) NOT NULL,
          `codcau` varchar(3) NOT NULL,
          `codcli` int(11) NOT NULL,
          `qt` varchar(32) NOT NULL,
          `val` varchar(32) NOT NULL,
          `mondo` varchar(3) NOT NULL,
          `gruppo` varchar(3) NOT NULL,
          `sottogruppo` varchar(3) NOT NULL,
          `codfor` varchar(6) NOT NULL,
          `codart` varchar(13) NOT NULL,
          PRIMARY KEY  (`id`),
          FOREIGN KEY `codart` (`codart`) REFERENCES outlet(codart)
            ON DELETE CASCADE
            ON UPDATE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ";

        $sql2 = "
        ALTER TABLE `outlet_docven`
        ADD CONSTRAINT outlet_docven_ibfk_1 FOREIGN KEY (codart) REFERENCES outlet (codart) ON DELETE CASCADE ON UPDATE NO ACTION;
        ";
      
        $q = mysql_query($sql);
        $q = mysql_query($sql2);
    }
    
    
    
    
    
}

?>
