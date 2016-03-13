<?
require_once('../config.php');
require_once('module_config.php');
require_once('../datatypes/mgart.php');


if(!isset($_POST['job'])) {
    
    $smarty->display('mgart_createFulltext/main.tpl');
    die();
    
}

/* apro tabella articoli */
$dbArt = mfcdb_open($tables['mgart']);


/* elimino il vecchio db fulltext */
$q = mysql_query("DROP TABLE IF EXISTS ft_codarts") or die("impossibile eliminare la tabella - ".mysql_error());


/* creo le tabelle */
$q = mysql_query("CREATE TABLE `ft_codarts` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `codart` VARCHAR(13) NOT NULL, `descriz` VARCHAR(60) NOT NULL) ENGINE = MyISAM;"); 
$q = mysql_query("ALTER TABLE `ft_codarts` ADD INDEX (`codart`)");
$q = mysql_query("ALTER TABLE `ft_codarts` ADD FULLTEXT (`descriz`)"); 


$ART = new MGART(); 
$errors = array();
$result = mfcdb_start($dbArt, 0, 'A..', MFC_ISGTEQ);

if($result == FALSE) {
    
    $smarty->assign('errmsg', 'impossibile fare start della tabella mgart, errore: '.mfcdb_error());
    $smarty->display('mgart_createFulltext/error.tpl');
    die();

}

$data = mfcdb_curr($dbArt);

$ART->load($data); 

$timeStartFulltext = microtime(true);

/* --- STEP 1 --- COMPILAZIONE DIZIONARIO --- */

$aSearch = array("\x27", "\x22", "\x60", "\x40", "\x5c", "\x25", "\x2e");
$aReplace = array(' ', ' ', ' ', ' ', ' ', ' ', ' ');

$k = 0;
$key = trim($ART->codArt);

while($key[0] <= 'Z') {

    $key = str_replace($aSearch, $aReplace, trim($ART->codArt));
    $descriz = $ART->getDescriz();
    
    $q = mysql_query("INSERT IGNORE INTO ft_codarts (codart, descriz) VALUES('".$key."', '".$descriz."') ");
    
    $data = mfcdb_next($dbArt);
    
    if($data == false && substr(mfcdb_error(), 0, 3)=='107' ) {
        
        if(SPIGAXDBG) error_log("__FILE__ : tento la lettura con isstart del record successivo a ".$ART->codArt." \n");
        $result = mfcdb_start($dbArt, 0, $ART->codArt, MFC_ISGREAT);
        
        if($result == false) {
            
            $errors[] = 'impossibile effettuare mfcdb_start su record GREAT -> '.$ART->codArt;
            break;
            
        }
        
        $data = mfcdb_curr($dbArt);
    }
    
    if($data == false) {
        
        $errors[] = 'impossibile leggere il record successivo a '.$ART->codArt.', motivo : '.mfcdb_error();
        break;
        
    }
    
    $ART->load($data); 

}

$timeStopFulltext = microtime(true);
$timeElapsedFulltext = $timeStopFulltext - $timeStartFulltext; 

/* prelevo il totale degli articoli */
$q = mysql_query("SELECT COUNT(id) AS recs FROM ft_codarts");
$row = mysql_fetch_assoc($q); 

$totRows = $row['recs']; 

mfcdb_close($dbArt);

if(count($errors)) $smarty->assign('errors', $errors);
$smarty->assign('rows', $totRows);
$smarty->assign('timeElapsedFulltext', $timeElapsedFulltext);

$smarty->display('mgart_createFulltext/result.tpl');

?>
