<?
/**
 * DATATYPES
 * Datatype
 *   classe parent contenente le primitive utilizzate da tutti gli oggetti 
 *   delle tabelle spigax
 */
 
class Datatype {
    

    
    
    /**
    * converte un valore da formato COMP-3
    * @param string $data dati di origine
    * @param int $length lunghezza in byte dei dati di origine
    * @param int $decimals numero decimali
    */
    function fromComp3($data, $length, $decimals) 
    {
        $value = mfcdb_comp3decode($data, $length);
        
        if($value == 0) return 0;
        
        $moltiplier = pow(10, $decimals);

        return round(($value / $moltiplier), $decimals);
    }
    
    
    
    
    /**
     * converte un dato in formato COMP-3
     * @param $data dato sorgente 
     * @param $type tipo di comp3 (MFC_COMP3_TYPE_C/D/F)
     * @param $length lunghezza in byte finale 
     * @param $decimals numero decimali del valore iniziale 
     */
    function toComp3($data, $type, $length, $decimals)
    {
        if($decimals) {
            $moltiplier = pow(10, $decimals);
            $data = $data * $moltiplier;
        }
        
        return (mfcdb_comp3encode($data, $type, $length));
    }
    
    
    
    
    /**
     * converte una data del tipo AAAAMMGG come da spigax in formato unixtime
     */
    static function fromDateToTime($date)
    {
        $aaaa = substr($date, 0, 4); 
        $mm = substr($date, 4, 2);
        $gg = substr($date, 6, 2); 
        
        $retTime = strtotime($aaaa.'-'.$mm.'-'.$gg.' 12:00:00');
        
        return $retTime;
    }
}

?>
