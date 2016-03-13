<?
require_once('../config.php');
require_once('module_config.php');
require_once('../datatypes/mgart.php');
require_once('../datatypes/mgcaa.php');
require_once('../datatypes/mgcon.php');

if(!isset($_POST['job'])) {
    
    $smarty->assign('codA', $_SESSION['codA']);
    $smarty->assign('codB', $_SESSION['codB']);
    $smarty->display('mgcaa_checkEZM/main.tpl');
    die();
    
}

/* salvo i dati di sessione */
$_SESSION['codA'] = $_POST['codA'];
$_SESSION['codB'] = $_POST['codB'];

/* apro i database */
if(!file_exists($tables['mgcaa'].'.idx')) {
    
    echo "impossibile trovare ".$tables['mgcaa'].'.idx';
}
        
    
$dbCaa = mfcdb_open($tables['mgcaa']);
$dbArt = mfcdb_open($tables['mgart']);
$dbCon = mfcdb_open($tables['mgcon']);

if(!$dbCaa || !$dbArt || !$dbCon) {
    
    $smarty->assign('errmsg', 'Impossibile aprire le tabelle dei database<br />'.mfcdb_error());
    $smarty->display('mgcaa_checkEZM/error.tpl');
    die();
    
}

$art = new MGART();
$caa = new MGCAA();
$con = new MGCON();

$result = mfcdb_start($dbArt, 0, $_POST['codA'], MFC_ISGTEQ);

if($result == FALSE) {
    
    $smarty->assign('errmsg', 'impossibile fare start della tabella mgart, errore: '.mfcdb_error());
    $smarty->display('mgcaa_checkEZM/error.tpl');
    die();

}

$data = mfcdb_curr($dbArt);
$art->load($data);

$k = 0;
$tmpTbl = array();
while($art->codArt < $_POST['codB']) {
    
    if($_POST['checkType'] == 'E') 
        $key = sprintf("%-25sE000000", $art->codArt);
    else if($_POST['checkType'] == 'M')
        $key = sprintf("%-25sM000000", $art->codArt);
    else if($_POST['checkType'] == 'Z')        
        $key = sprintf("%-25sZ000000", $art->codArt);

    $data = @mfcdb_fetch($dbCaa, $key, 0);
    
    if($data != FALSE) { 
    
        $caa->load($data);
                
        /* prelevo l'esistenza del primo codice */
        $key = sprintf("000%-13s000000", $art->codArt);
        $data = mfcdb_fetch($dbCon, $key, 0);
        
        $con->load($data);
        $esistA = $con->getEsistenza();
        
        /* prelevo l'esistenza del codice collegato */
        $key = sprintf("000%-13s000000", $caa->codArt);
        $data = mfcdb_fetch($dbCon, $key, 0);
        
        $con->load($data);
        $esistB = $con->getEsistenza();
        
        /* riempio la tabella finale */
        $tmpTbl[] = array('CodArt' => $art->codArt, 'Descriz1' => $art->descriz, 'Esist1' =>$esistA, 'CodArt2' => $caa->codArt, 'Descriz2' =>'', 'Esist2' =>$esistB);
        
    }
    
    $data = mfcdb_next($dbArt);
    $art->load($data);
    $k++;
}
mfcdb_close($dbCaa);
mfcdb_close($dbCon);

/* prelevo le seconde descrizioni */
$tbl = array();
foreach($tmpTbl as $row) {
    $key = sprintf("%-13s", $row['CodArt2']);
    $data = mfcdb_fetch($dbArt, $key, 0);
    $row['Descriz2'] = substr($data, 13, 30);
    $tbl[] = $row;
}
unset($tmpTbl);


mfcdb_close($dbArt);

$smarty->assign('rows', $k);
$smarty->assign('codA', $_POST['codA']);
$smarty->assign('codB', $_POST['codB']);
$smarty->assign('checkType', $_POST['checkType']);
$smarty->assign('tbl', $tbl);
$smarty->display('mgcaa_checkEZM/result.tpl');
?>
