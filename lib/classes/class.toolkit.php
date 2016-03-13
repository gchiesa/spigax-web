<?
/**
 * CLASS TOOLKIT 
 *   funzioni statiche varie per l'utilizzo all'interno delle classi
 */

Class Toolkit {

    


    /**
     * converta una data dal formato gg/mm/aaaa al formato SpigaX aaaammgg
     */
    static function dateToSpigaXDate($date)
    {
        $gg = substr($date, 0, 2);
        $mm = substr($date, 3, 2); 
        $aaaa = substr($date, 6, 4); 
        
        if(!checkdate($mm, $gg, $aaaa)) {
            
            if(SPIGAXDBG) error_log(__CLASS__." : data non corretta $gg / $mm / $aaaa ");
            return false;
            
        }
        
        return $aaaa.$mm.$gg;
    }
    
        
        
    
}

?>
