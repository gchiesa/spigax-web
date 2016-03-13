<?
/**
 * BCODE 
 *   oggetto base per la gestione della stampa bar code
 */ 
require_once('../datatypes/mgart.php');
require_once('../datatypes/mgcaa.php');
require_once('../datatypes/mgprz.php');


Class BCode {

    var $dbArt;
    var $dbCaa;
    var $dbPrz;
    var $forceP1;
    
    


    /**
     * costruttore principale della classe 
     */
    function __construct(&$dbArt, &$dbCaa, &$dbPrz)
    {
        $this->dbArt = $dbArt;
        $this->dbCaa = $dbCaa;
        $this->dbPrz = $dbPrz;
    }
    
    
    
        
    /**
     * crea e restituisce un array semplice di codici articoli a partire da una
     * lista associativa del tipo :
     *
     * array_start[<codice_articolo>] = <quantita>
     *
     * e restituisce un array del tipo 
     * 
     * array[] = <codice_articolo> 
     * 
     * con codice articolo ripetuto n volte in base alla quantità specificata.
     */
    function createListFromAssoc($assoc)
    {
        $list = array();
        
        foreach($assoc as $key=>$value) {
            
            if($value == 0) continue; 
            
            if($value == 1) {
                
                $list[] = $key;
                continue;
                
            }
            
            for($k=0;$k<$value;$k++) $list[] = $key;
        }
        
        return $list;
    }
    
    
    
    
    /**
     * restituisce i dati binari di stampa della stampa barcode a partire dalla
     * lista codici articoli
     *
     * @param array $list array codici articoli 
     */
    function getBCodeDataFromList($list)
    {
        $ART = new MGART();
        $CAA = new MGCAA();
        $PRZ = new MGPRZ();
        
        $retArray = array();
        
        foreach($list as $codArt) {
            
            // prendo la descrizione 
            $data = mfcdb_fetch($this->dbArt, sprintf("%-13s", $codArt), 0);
            
            if($data == false) continue;
            
            $ART->load($data); 
            
            $descrizione1 = substr($ART->getDescriz(), 0, 30); 
            $descrizione2 = substr($ART->getDescriz(), 30); 
            
            // prendo il codice a barre
            $result = mfcdb_start($this->dbCaa, 2, sprintf("%-13sE", $codArt), MFC_ISGTEQ);
            
            if(!$result) continue;
            
            $data = mfcdb_curr($this->dbCaa);
            
            if($data == false) continue;
            
            $CAA->load($data); 
            
            $bcode = $CAA->getCode();
            
            // prendo il prezzo 
            $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s01", $codArt), 0);
            
            if($data == false) continue;
            
            $PRZ->load($data); 
            
            $prezzo = $PRZ->getP();
            
            if(!$this->forceP1) {
                
                if($PRZ->haveAssoc()) {
                    
                    $data = mfcdb_fetch($this->dbPrz, sprintf("000%-13s%-02s", $codArt, $PRZ->haveAssoc()), 0);
                    
                    $PRZ->load($data); 
                    
                    if($PRZ->isActive()) {
                        
                        $prezzo = $PRZ->getP();
                        
                    }
                    
                }
                
            }
            
            $retArray[] = array(  'descrizione1'  => $descrizione1,
                                'descrizione2'  => $descrizione2, 
                                'prezzoE'       => 'E '.number_format(trim($prezzo), 2, ',', '.'),
                                'prezzoL'       => 'Lire '.number_format(round($prezzo * 1936.27), 0, ',', '.'),
                                'codart'        => $codArt,
                                'barcode'       => $bcode.$this->ean13CheckDigit($bcode));
            
        } // foreach
        
        return $retArray;
    }
            
            
            
            
    /** 
     * restituisce l' EAN13 check digit in base al codice articolo passato
     */
    function ean13CheckDigit($digits) {
        
        //first change digits to a string so that we can access individual numbers
        $digits =(string)$digits;
        
        // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
        
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        
        // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
        
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        
        // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
        $next_ten = (ceil($total_sum/10))*10;
        $check_digit = $next_ten - $total_sum;
        
        return $check_digit;
    }
            
                
}
?>
