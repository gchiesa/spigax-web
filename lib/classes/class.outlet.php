<?

class Outlet {

    var $dbArt;
    var $dbCaa;
    var $dbCon;
    var $dbPrz;
    
    var $defaultDocVenDate;
    
    
    
    function Outlet(&$dbArt, &$dbCaa, &$dbCon, &$dbPrz)
    {
        if(!$this->initializeOutlet()) {
            
            return false;
            
        }
        
        $this->dbArt = $dbArt;
        $this->dbCaa = $dbCaa;
        $this->dbCon = $dbCon;
        $this->dbPrz = $dbPrz;
    }
    
    
    
    
    function initializeOutlet()
    {
        $q = mysql_query("SELECT COUNT(codart) FROM outlet");
        
        if($q) {
            
            return true;
        
        }
        
        /* creo la tabella */
        $sql = "
        CREATE TABLE IF NOT EXISTS `outlet` (
          `id` bigint(20) unsigned NOT NULL auto_increment,
          `codart` varchar(13) NOT NULL,
          `date` bigint(20) NOT NULL,
          `docven_date` bigint(20) NOT NULL,
          PRIMARY KEY  (`id`),
          KEY `codart` (`codart`),
          KEY `docven_date` (`docven_date`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;
        ";

        $q = mysql_query($sql);
        
        return true;
    }
    
    
    
    
    function insert($codArts)
    {
        $now = time();
        $docven_date = ($this->defaultDocVenDate)?$this->defaultDocVenDate:strtotime(date("Y-m-d", $now).' 12:00:00');
        
        foreach($codArts as $codArt) {
            
            if(!strlen(trim($codArt))) continue; 
            
            $q = mysql_query("SELECT * FROM outlet WHERE codart = '".$codArt."' ");
            
            if(!mysql_num_rows($q)) {
                
                $q = mysql_query("INSERT IGNORE INTO outlet (codart, date, docven_date) VALUES ('".$codArt."', '".$now."', '".$docven_date."' ) ");
                
            }
            
        }
        
        // -- TODO -- createLog --//
    }
                
        
    
        
    function delete($codArts, $unlinkP10 = false, &$errors)
    {
        $PRZ = new MGPRZ();
        
        foreach($codArts as $codArt) {
            
            $q = mysql_query("DELETE FROM outlet WHERE codart = '".$codArt."' ");
            
            if(!$unlinkP10) continue;   // se nn richiesto non elimino i p10
            
            $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $codArt), 0);
            
            if($data == false) continue;
            
            $PRZ->load($data); 
            
            if(SPIGAXDBG) error_log("prezzo associato : ".$PRZ->haveAssoc()."\n");
            
            // se ha associato il p10 azzero la data di validità 
            if($PRZ->haveAssoc() == '10') {
                
                $PRZ->deleteDval();
                $PRZ->removeAssoc();
                $data = $PRZ->packRecord();
                
                $result = mfcdb_replace($this->dbPrz, sprintf("000%-13s01", $codArt), $data);
                
                if($result == false) {
                    
                    $errors[] = 'impossibile aggiornare articolo '.$codArt.' errore : '.mfcdb_error().' ';
                    
                }
                
            }
            
        }
    }
    
    
                
        
    function filterInOutlet($codArtA, $codArtB = NULL) 
    {
        $tblData = array();
        
        /* ricerca singola */
        if($codArtB == NULL) {
            
            $q = mysql_query("SELECT * FROM outlet WHERE codart = '".$codArtA."' LIMIT 1 ");
            
            if(!mysql_num_rows($q)) {
                
                $tblData[] = array('codArt' => $codArtA, 'exixts' => false);
                return false;
            
            }
            
            $row = mysql_fetch_assoc($q);
            
            $tblData[] = array('codArt' => $codArt, 'exists' => true, 'date' => $row['date']);

        } else {
            
            $q = mysql_query("SELECT * FROM outlet WHERE codart >= '".$codArtA."' AND codart <= '".$codArtB."' ORDER BY codart ");
            
            if(!mysql_num_rows($q)) {
                
                return false;
                
            }
            
            while($row = mysql_fetch_assoc($q)) {
                
                $tblData[] = array('codArt' => $row['codart'], 'exists' => true, 'date' => $row['date']);
                
            }
            
        }
        
        return $tblData;
    }
    
    
    
    
    static function exists() 
    {
        $q = mysql_query("SELECT COUNT(id) AS id FROM outlet");
        
        if(!$q) {
            
            return false;
            
        }
        
        $row = mysql_fetch_assoc($q); 
        
        if(!$row['id']) {
            
            return false;
            
        }
        
        return true;
    }
    
    
    
    static function getInnerJoinOn($field) 
    {
        return " INNER JOIN outlet ON outlet.codart = ".$field." ";
        
    }
        
    
    
    
    function setDocVenDate($date)   // $date format gg/mm/aaaa 
    {
        $gg = substr($date, 0, 2);
        $mm = substr($date, 3, 2); 
        $aaaa = substr($date, 6, 4); 
        
        if(checkdate($mm, $gg, $aaaa)) {
            
            $this->defaultDocVenDate = strtotime($aaaa.'-'.$mm.'-'.$gg.' 12:00:00');
            
        } else {
            
            $this->defaultDocVenDate = false;
            
        }
    }
        
        
}
?>
