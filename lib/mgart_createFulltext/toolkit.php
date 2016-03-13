<?
function createTransactionCodArts(&$ftCodArts) 
{
    global $dbTmp; 
    
    sqlite_exec($dbTmp, "BEGIN TRANSACTION");

    $k = 0;
    $end = count($ftCodArts); 

    if(SPIGAXDBG) error_log("inizio transazione codarts, flush di $end record...\n");

    for($k = 0; $k < $end; $k++) {
    
        $q = sqlite_query($dbTmp, "INSERT OR IGNORE INTO ft_codarts ( id, codart ) VALUES ('".$ftCodArts[$k]['id']."', '".$ftCodArts[$k]['codart']."' ) ");
    
    }
    sqlite_exec($dbTmp, "COMMIT");

    if(SPIGAXDBG) error_log("transazione codarts terminata.\n");
    
}




function createTransactionAssoc(&$ftAssoc)
{
    global $dbTmp;

    sqlite_exec($dbTmp, "BEGIN TRANSACTION");
    
    $k = 0;
    $end = count($ftAssoc); 

    if(SPIGAXDBG) error_log("inizio transazione assoc, flush di $end record...\n");
    
    for($k = 0; $k < $end; $k++) {
    
        $q = sqlite_query($dbTmp, "INSERT INTO ft_fulltext (id_codart, id_word) VALUES('".$ftAssoc[$k]['id_codart']."', '".$ftAssoc[$k]['id_word']."' ) ");
    }

    sqlite_exec($dbTmp, "COMMIT");

    if(SPIGAXDBG) error_log("transazione assoc terminata.\n");

}
?>
