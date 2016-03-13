<?
/**
 * DATATYPES 
 * mgcon
 *   oggetto per la gestione della tabella contabile articoli 
 */
require_once('datatype.php');

class MGCON extends Datatype {

    var $rawData; 
    
    var $codArt; 
    
    var $riord;
    var $scoMin;
    var $scoMax;
    var $duv;
    var $qtGI;     /* giacenza iniziale */
    var $valGI;    /* valore giacenza iniziale */
    var $qtFP;     /* fabbisogno produzione */
    var $qtDP;     /* da produrre */
    var $qtIL;     /* in lavorazione */
    var $qtIP;     /* impegno per produzione */
    var $qtIM;     /* impegnato */
    var $valIM;    /* valore impegnato */
    var $qtOR;     /* ordinato */
    var $valORD;   /* valore ordinato */
    var $qtCF;     /* carichi fornitore */
    var $qtCI;     /* carichi interni */
    var $qtAC;     /* altri carichi */
    var $valTC;    /* valore carichi */
    var $qtSC;     /* scarichi clienti */
    var $qtSI;     /* scarichi interni */
    var $qtAS;     /* altri scarichi */
   
   
   
    
    /** 
    * carica e riempie la struttura dati 
    */
    function load($data)
    {
        if($data == false) {
            
            $this->rawData = false;
            return;
        }
       
        $this->rawData = $data;
        
        $this->codArt = substr($data, 3, 13);
        // $this->riord = mfcdb_comp3decode(substr($data, 66, 4), 4);
        // $this->scoMin = mfcdb_comp3decode(substr($data, 70, 4), 4);
        // $this->scoMax = mfcdb_comp3decode(substr($data, 74, 4), 4);
        // $this->duv = mfcdb_comp3decode(substr($data, 78, 5), 5);
        $this->qtGI = $this->fromComp3(substr($data, 83, 8), 8, 5);
        // $this->valGI = round(mfcdb_comp3decode(substr($data, 91, 8), 8) / 100, 2);
        $this->qtFP = $this->fromComp3(substr($data, 99, 8), 8, 5); 
        $this->qtDP = $this->fromComp3(substr($data, 107, 8), 8, 5);
        $this->qtIL = $this->fromComp3(substr($data, 115, 8), 8, 5);
        $this->qtIP = $this->fromComp3(substr($data, 123, 8), 8, 5);
        $this->qtIM = $this->fromComp3(substr($data, 131, 8), 8, 5);
        // $this->valIM = round(mfcdb_comp3decode(substr($data, 139, 8), 8) / 100, 2);
        $this->qtOR = $this->fromComp3(substr($data, 147, 8), 8, 5);
        // $this->valORD = round(mfcdb_comp3decode(substr($data, 155, 8), 8) / 100, 2);
        $this->qtCF = $this->fromComp3(substr($data, 163, 8), 8, 5);
        $this->qtCI = $this->fromComp3(substr($data, 171, 8), 8, 5);
        $this->qtAC = $this->fromComp3(substr($data, 179, 8), 8, 5);
        $this->valTC = $this->fromComp3(substr($data, 187, 8), 8, 2);
        $this->qtSC = $this->fromComp3(substr($data, 195, 8), 8, 5);
        $this->qtSI = $this->fromComp3(substr($data, 203, 8), 8, 5);
        $this->qtAS = $this->fromComp3(substr($data, 211, 8), 8, 5);
        
    }
    
    
    
    
    /**
    * restituisce la valorizzazione dell'esistenza 
    */
    function getEsistenza()
    {
        if($this->rawData == false) return 'n/a';
        
        $test = round($this->qtGI + $this->qtCF + $this->qtCI + $this->qtAC - $this->qtSC - $this->qtSI - $this->qtAS);
        
        if($test > 100 || $test < -100) {
           
           $tmp = $this->rawData;
           $this->rawData = '';
           if(SPIGAXDBG) error_log('ATTENZIONE: valore non congruo, dump classe: '.print_r($this, true)."\n\n");
           $this->rawData = $tmp;
        }
        
        return round( $this->qtGI + $this->qtCF + $this->qtCI + $this->qtAC - $this->qtSC - $this->qtSI - $this->qtAS );
    }

}
   
?>
