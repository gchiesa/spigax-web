<?
require_once('../datatypes/mgprz.php');


class PromoP10 {

    var $PRZ;
    var $p10;
    var $p10dVal;
    
    var $dbArt;
    var $dbPrz;
    
    
    
    function PromoP10(&$dbArt, &$dbPrz)
    {
        $this->PRZ = new MGPRZ();
        $this->p10 = array();
        $this->dbArt = $dbArt;
        $this->dbPrz = $dbPrz;
        
    }
    
    
    
    
    function loadAndCheckData($prezzi, &$errors)
    {
        $PRZ = new MGPRZ();
        
        foreach($prezzi as $key => $value) {
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - analizzo chiave $key value $value \n");
            
            $value = str_replace(',', '.', $value);
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - converto prezzo, nuovo value $value");
            
            if(!is_numeric($value)) {    // check 0 - numeric
                
                $errors[] = 'ERRORE: articolo '.$key.' con P10 pari a '.$value.' ha un valore P10 errato';
                return false;
                
            }
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - check prezzo zero su ".(round($value, 2)*100));
            
            if((round($value, 2)*100) == 0) {         // check 1 - prezzo diverzo da zero
                
                $errors[] = 'ERRORE: articolo '.$key.' con P10 pari a '.$value.' ';
                return false;
                
            }
            
            $value = round($value, 2);  // forzo l'arrotondamento a 2 cifre
            
            $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $key), 0);
            
            if(!$data) {                // se non esiste il P1
                
                $errors[] = 'ERRORE: articolo '.$key.' non ha prezzo 01 ';
                return false;
                
            }
            
            $PRZ->load($data); 
            
            $pRic = MGPRZ::getRicarico($PRZ->getP(), $value);
            
            if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." - check ricarico su $pRic");
            
            if($pRic > 99 || $pRic < -99 ) {     // se ricarica troppo elevata
                
                $errors[] = 'ERRORE: discostamento P1 -> P10 su articolo '.$key.' pari a '.$pRic.' % ( P1: '.$PRZ->getP().' , P10: '.$value.' )';
                return false;
                
            }
            
            $this->p10[$key] = $value;
        }
        
        return true;
    }

    
    
    
    function update(&$errors)
    {
        $today = date("d/m/Y", time() - (60*60*24) ); // le promozioni vengono calcolate sulla base del giorno precedente
        
        foreach($this->p10 as $key => $value) {
            
            $P1 = new MGPRZ();
            $P10 = new MGPRZ();
            
            $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $key), 0);
            
            $P1->load($data);
            
            /* riempio i campi del P10 */
            $P10->codMag = $P1->codMag; 
            $P10->codArt = $P1->codArt;
            $P10->nPrz = '10';
            $P10->prz = $value;
            $P10->sco1 = 0;
            $P10->setDval($this->p10dVal);
            $P10->sco2 = 0;
            $P10->lAss = 0;
            
            $data = $P10->packRecord();
            
            if(mfcdb_exists($this->dbPrz, sprintf("000%-13s10", $P10->codArt), 0)) {
                
                // rimpiazzo il record vecchio col nuovo 
                $result = mfcdb_replace($this->dbPrz, sprintf("000%-13s10", $P10->codArt), $data);
                
                if($result == false) {
                    
                    $errors[] = 'errore durante fase aggiornamento p10 '.$P10->codArt.' ('.mfcdb_error().')';
                    continue;
                    
                }
                
            } else {
                
                // aggiungo nuovo record p10 
                $result = mfcdb_insert($this->dbPrz, $data);
                
                if($result == false) {
                    
                    $errors[] = 'errore durante inserimento nuovo p10 '.$P10->codArt.' ('.mfcdb_error().')';
                    continue;
                    
                }
                
            }
            
            // aggiorno il P1 
            $P1->lAss = '10';
            $P1->setDval($today); 
            
            $data = $P1->packRecord();
            
            $result = mfcdb_replace($this->dbPrz, sprintf("000%-13s01", $P1->codArt), $data);
            
            if($result == false) {
                
                $errors[] = 'errore durante aggiornamento p1 '.$P1->codArt.' ('.mfcdb_error().')';
                continue;
                
            }
            
        }
     
        return true;
    }
    
    
    
    
    function setDval($data)
    {
        $this->p10dVal = $data;
    }        
            
            
    
    
        
    
}
?>
