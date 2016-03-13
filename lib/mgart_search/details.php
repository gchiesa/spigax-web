<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.search.fulltext.php');


/* apro i database spiga */
$dbArt = mfcdb_open($tables['mgart']); 
$dbCaa = mfcdb_open($tables['mgcaa']);
$dbPrz = mfcdb_open($tables['mgprz']);
$dbClf = mfcdb_open($tables['cgclf']);

$tblData = Search::getDetails($dbArt, $dbCaa, $dbClf, $dbPrz, $_GET['codArt']);

mfcdb_close($dbArt);
mfcdb_close($dbCaa);
mfcdb_close($dbPrz);
mfcdb_close($dbClf);

/* cerco il primo ean disponbile */
foreach($tblData['caaTbl'] as $row) {
    
    if($row['type'] == 'E' && $row['code'][0] != '0') {
        
        $smarty->assign('ean', $row['code']);
        break;
    
    }

}
        
$smarty->assign('tblData', $tblData);
$smarty->assign('caaTbl', $tblData['caaTbl']);
$smarty->display('mgart_search/details.tpl');


?>
