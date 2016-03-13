<?
require_once('../datatypes/mgart.php');
require_once('../datatypes/mgcon.php');
require_once('../datatypes/mgprz.php');
require_once('../datatypes/mgcaa.php');
require_once('../datatypes/cgclf.php');
require_once('class.outlet.php');


class Search {

    var $codArt;
    var $descriz;
    var $flEsist;
    var $flMovim;
    var $flP8;
    var $flGiorn;
    
    var $DB;
    
    var $tmysql;
    var $tmfc;
    var $trows;
    
    
    
    function Search($aPost)
    {
        $this->codArt = $aPost['codArt'];
        $this->descriz = $aPost['descriz'];
        $this->flEsist = (isset($aPost['flEsist']))?true:false;
        $this->flMovim = (isset($aPost['flMovim']))?true:false;
        $this->flP8 = (isset($aPost['flP8']))?true:false;
        $this->flGiorn = (isset($aPost['flGiorn']))?true:false;
        $this->flOutlet = (isset($aPost['flOutlet']))?true:false;
        
    }
    
    
    
    
    function getData($dbArt, $dbCon, $dbPrz)
    {
        
        $this->dbArt = $dbArt;
        $this->dbCon = $dbCon;
        $this->dbPrz = $dbPrz;
        
        $con = new MGCON();
        $prz = new MGPRZ();
        $art = new MGART();
        
        $row = array();
        $retArray = array();
        
        /* verifico se è una fulltext o normale */
        if(strlen(trim($this->descriz))) {
            
            $sql = $this->buildSql($descriz);
            
            $tstart = microtime(true);
            $q = mysql_query($sql);
            $tstop = microtime(true);
            
            $this->tmysql = ($tstop - $tstart);
            
            $tstart  = microtime(true);
            while($row = mysql_fetch_assoc($q)) {
                
                $data = mfcdb_fetch($this->dbCon, sprintf("000%-13s000000", $row['codart']), 0);
                
                $con->load($data);
                
                $esist = $con->getEsistenza();                                              // ESISTENZA
                
                $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $row['codart']), 0);
                
                $prz->load($data);
                
                $pVen = $prz->getP();                                                      // PREZZO P1
                
                if($prz->haveAssoc()) {
                    
                    $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s%-2s", $row['codart'], $prz->haveAssoc()), 0);
                    
                    $prz->load($data);
                    
                    if($prz->isActive()) {
                        
                        $pVen = $prz->getP();
                        $flGiorn = ($prz->numP() == '09')?true:false;                        // PREZZO 9
                        $flP10 = ($prz->numP() == '10')?true:false;                         // PREZZO 10
                            
                    }
                    
                }
                
                if($this->flP8) {                                                           // PREZZO P8
                    
                    $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s08", $row['codart']), 0);
                
                    $prz->load($data);
                    
                    $pAcq = $prz->getP();
                    
                    if($prz->haveAssoc()) {
                        
                        $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s%-2s", $row['codart'], $prz->haveAssoc()), 0);
                
                        $prz->load($data);
                        
                        if($prz->isActive()) {
                            
                            $pAcq = $prz->getP();
                            
                        }
                        
                    }
                    
                } // if($this->flP8) 
                
                $data = mfcdb_fetch($dbArt, sprintf("%-13s", $row['codart']), 0);
                
                $art->load($data);
                
                $ultAcq = $art->getUltAcq();                                                // ULTIMO ACQUISTO
                
                $descriz = $art->getDescriz();                                              // DESCRIZIONE
                
                /* riempio l'array finale */
                $retArray[] = array('codArt' => $row['codart'], 
                                    'descriz' => $descriz,
                                    'esist' => $esist,
                                    'pAcq' => $pAcq,
                                    'ultAcq' => $ultAcq,
                                    'pVen' => $pVen,
                                    'pRic' => (MGPRZ::getRicarico($pAcq, $pVen)),
                                    'flGiorn' => $flGiorn,
                                    'flP10' => $flP10);
                                
                $flGiorn = $flP10 = false;

                
            } //  while($row = sql_fetch_array($q, SQLITE_ASSOC))
            
            $tstop = microtime(true);
            
            $this->tmfc = $tstop - $tstart;
            
        } else { // if(strlen(trim($this->descriz)))
            
            /* query non full text quindi utilizzo la ricerca sequenziale sull'archivio mgcon */
            if(SPIGAXDBG) error_log("DEBUG: codArt ".$this->codArt."\n");
            $result = mfcdb_start($dbCon, 0, sprintf("000%-13s", $this->codArt), MFC_ISGTEQ);
            
            if($result == FALSE) {
                
                if(SPIGAXDBG) error_log("DEBUG : ".mfcdb_error());
                return NULL;
                
            }
            
            
            $data = mfcdb_curr($dbCon);
            
            $con->load($data);
            
            $k = 0;
            $tmysql = 'n/a';
            
            $tstart = microtime(true);
            $keylen = strlen($this->codArt); /* salvo la lunghezza stringa per evitare continue call nel while */
            while(strncmp($con->codArt, $this->codArt, $keylen) == 0) {
                
                if($k++ > SITE_MAX_LOOP) break;
                $row['codart'] = $con->codArt;
                
                if(SPIGAXDBG) error_log("DEBUG: analisi codice ".$row['codart']."\n");
                $esist = $con->getEsistenza();                                              // ESISTENZA
                
                $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $row['codart']), 0);
                
                $prz->load($data);
                
                $pVen = $prz->getP();                                                      // PREZZO P1
                
                if($prz->haveAssoc()) {
                    
                    $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s%-2s", $row['codart'], $prz->haveAssoc()), 0);
                    
                    $prz->load($data);
                    
                    if($prz->isActive()) {
                        
                        $pVen = $prz->getP();
                        $flGiorn = ($prz->numP() == '09')?true:false;                        // PREZZO 9
                        $flP10 = ($prz->numP() == '10')?true:false;                         // PREZZO 10
                            
                    }
                    
                }
                
                if($this->flP8) {                                                           // PREZZO P8
                    
                    $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s08", $row['codart']), 0);
                
                    $prz->load($data);
                    
                    $pAcq = $prz->getP();
                    
                    if($prz->haveAssoc()) {
                        
                        $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s%-2s", $row['codart'], $prz->haveAssoc()), 0);
                
                        $prz->load($data);
                        
                        if($prz->isActive()) {
                            
                            $pAcq = $prz->getP();
                            
                        }
                        
                    }
                    
                } // if($this->flP8) 
                
                $data = mfcdb_fetch($dbArt, sprintf("%-13s", $row['codart']), 0);
                
                $art->load($data);
                
                $ultAcq = $art->getUltAcq();                                                // ULTIMO ACQUISTO
                
                $descriz = $art->getDescriz();                                              // DESCRIZIONE
                
                /* riempio l'array finale */
                $retArray[] = array('codArt' => $row['codart'], 
                                    'descriz' => $descriz,
                                    'esist' => $esist,
                                    'pAcq' => $pAcq, 
                                    'ultAcq' => $ultAcq,
                                    'pVen' => $pVen,
                                    'pRic' => (MGPRZ::getRicarico($pAcq, $pVen)),
                                    'flGiorn' => $flGiorn,
                                    'flP10' => $flP10);
                
                
                $data = mfcdb_next($dbCon);
                $con->load($data);
                
                $flGiorn = $flP10 = false;
                if(SPIGAXDBG) error_log(print_r($retArray, true));
            
                
            } // while($this->codart >= $con->codArt)
            
            $tstop = microtime(true);
            
            $this->tmfc = $tstop - $tstart;
            
            $this->trows = $k;
            
        } // else 

        return $retArray;
    }




    function applyFilters($rArray)
    {
        $nArray = array();
        
        foreach($rArray as $row) {
            
            if($row['esist'] == 0 && $this->flEsist) 
                continue;
            
            if($row['ultAcq'] == false && $this->flMovim)
                continue;
            
            if($this->flGiorn && !$row['flGiorn'])
                continue;
            
            if(!$this->flP8) 
                $row['pAcq'] = 'n/a';
            
            $row['flGiorn'] = ($row['flGiorn'])?'S':'N';
            $row['flP10'] = ($row['flP10'])?'S':'N';
            
            $nArray[] = $row;
        
        }
        
        return $nArray;
    }




    function buildSql()
    {
        $aWhere = explode(',', $this->descriz);
        
        $wSql = array();
        
        foreach($aWhere as $wClause) {
            
            if(strlen(trim($wClause))) {
                
                $wSql[] = '+'.$wClause.'*';
            }
            
        }
        
        $sql = "SELECT ft_codarts.codart FROM ft_codarts ";
        /* outlet */
        if($this->flOutlet && Outlet::exists()) {
            
            $sql .= Outlet::getInnerJoinOn('ft_codarts.codart'); 
            
        }
        
        $sql .= " WHERE MATCH (ft_codarts.descriz) AGAINST ('".
                implode(' ', $wSql).
                "' IN BOOLEAN MODE) ";
               
        
        if($this->codArt != '*') {
           
           $sql .= " AND ft_codarts.codart LIKE '".$this->codArt."%' ";
                   
        }
        
        $sql .= " ORDER BY ft_codarts.codart ";
        
        if(SPIGAXDBG) error_log("query richiesta $sql"); 
        return $sql;
    }




    function setDB($dbArt, $dbCon, $dbPrz)
    {
        $this->dbArt = $dbArt;
        $this->dbCon = $dbCon;
        $this->dbPrz = $dbPrz;
    }
    
    
    
    
    function performSearch()
    {
        return $this->applyFilters($this->getData($this->dbArt, $this->dbCon, $this->dbPrz));
        
    }
    
    
    
    
    static function getDetails($dbArt, $dbCaa, $dbClf, $dbPrz, $codArt)
    {
        $art = new MGART();
        $caa = new MGCAA();
        $prz = new MGPRZ();
        $clf = new CGCLF();
        
        $data = mfcdb_fetch($dbArt, sprintf("%-13s", $codArt));                             // dati articolo
        
        $art->load($data);
        
        $descriz = $art->getDescriz();

        $data = mfcdb_fetch($dbClf, sprintf("F%06s", $art->getLastFor()));                        // dati fornitore
        
        $clf->load($data);

        $forCod = $clf->getCod();
        $forDescriz = $clf->getRagSoc();
        
        $data = mfcdb_fetch($dbPrz, sprintf("000%-13s01", $codArt));                        // dati prezzo
        
        $prz->load($data);
        
        $pVen = $prz->getP();
        
        if($prz->haveAssoc()) {
            
            $data = mfcdb_fetch($dbPrz, sprintf("000%-13s%-2s", $codArt, $prz->haveAssoc()), 0);
            
            $prz->load($data);
            
            if($prz->isActive()) {
                
                $pVen = $prz->getP();
                    
            }
            
        }
        
        $caaTbl = array();                                                                  // codici alternativi
        $result = mfcdb_start($dbCaa, 2, $codArt, MFC_ISGTEQ);
        
        if($result) {

            $data = mfcdb_curr($dbCaa);
            if(SPIGAXDBG) error_log("DEBUG result : $result, data caa : ".$data);
            $caa->load($data);
            
            while(strncmp($caa->codArt, $codArt, strlen($codArt))==0) {
                if(SPIGAXDBG) error_log("DEBUG: codartzz: ".$codArt."zz codart: $caa->codArt, caa-type $caa->type, caa $caa->code");
                $caaTbl[] = array('type'=> $caa->type, 'code'=> $caa->code);
                
                $data = mfcdb_next($dbCaa);
                
                $caa->load($data);
                
            }
        
        }
        
        $tblData = array(   'codArt' => $codArt, 
                            'descriz' => $descriz, 
                            'forCod' => $forCod, 
                            'forDescriz' => $forDescriz, 
                            'pVen' => $pVen, 
                            'caaTbl' => $caaTbl
                            );
        
        return $tblData;
    }
        
    
} 


?>
