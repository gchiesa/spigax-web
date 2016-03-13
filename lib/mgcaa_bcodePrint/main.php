<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.search.php');
require_once('../classes/class.search.evasione.php');
require_once('../classes/class.outlet.php');
require_once('../classes/class.bcode.allegro.php');

// se non è impostato il tipo di sorgente day (mysql | db ) 
if(!isset($_GET['function'])) {

    $smarty->assign('im_codArt', $_SESSION['im_codArt']);
    $smarty->assign('ir_codArtA', $_SESSION['ir_codArtA']);
    $smarty->assign('ir_codArtB', $_SESSION['ir_codArtB']);
    $smarty->assign('ir_esist', $_SESSION['ir_esist']);
    
    $smarty->display('mgcaa_bcodePrint/main.tpl');
    die();
    
}

// se è impostata la sorgente ma non il flag job 
if(!isset($_POST['job'])) {
    
    switch($_GET['function']) {
        
    case 'mysql_outlet':
        
        $smarty->assign('pageTitle', 'Stampa Bar Code : Frontalini Outlet');
        break;
        
    case 'db':
        
        $smarty->assign('pageTitle', 'Stampa Bar Code : Frontalini Spiga X');
        break;
        
    default:
        break;
    }
       
    $smarty->assign('function', $_GET['function']);
    $smarty->assign('im_codArt', $_SESSION['im_codArt']);
    $smarty->assign('ir_codArtA', $_SESSION['ir_codArtA']);
    $smarty->assign('ir_codArtB', $_SESSION['ir_codArtB']);
    $smarty->assign('ir_esist', $_SESSION['ir_esist']);
    $smarty->assign('ie_data', $_SESSION['ie_data']);
    $smarty->assign('ie_doc', $_SESSION['ie_doc']);
    
    $smarty->display('mgcaa_bcodePrint/select.tpl');
    die();

}



/* salvo le variabili di sessione */
$_SESSION['im_codArt'] = $_POST['im_codArt'];
$_SESSION['ir_codArtA'] = $_POST['ir_codArtA'];
$_SESSION['ir_codArtB'] = $_POST['ir_codArtB'];
$_SESSION['ir_esist'] = (isset($_POST['ir_esist']))?'checked':'';
$_SESSION['ie_data'] = $_POST['ie_data'];
$_SESSION['ie_doc'] = $_POST['ie_doc'];

/* apertura database */
$dbArt = mfcdb_open($tables['mgart']); 
$dbCon = mfcdb_open($tables['mgcon']);
$dbCaa = mfcdb_open($tables['mgcaa']);
$dbPrz = mfcdb_open($tables['mgprz']);
$dbMov = mfcdb_open($tables['mgmov']); 


$Search = new Search($dbArt, $dbCon, $dbCaa, $dbPrz);
$Outlet = new Outlet($dbArt, $dbCaa, $dbCon, $dbPrz);

$errors = array();

switch ($_GET['method'] )  
{
case 'immysql':
    $tblData = $Search->fromMysqlByCodArt($_POST['im_codArt'], $errors);
    
    if(count($errors)) {
      
      $smarty->assign('errors', $errors);
      $template = 'main.tpl';
      break;
    }
    
    $smarty->assign('datatable', $tblData);
    $template =  'searchResult.tpl';
    break;


case 'imdb':
    
    $tblData = $Search->byCodArt($_POST['im_codArt'], $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'select.tpl';
        break;
        
    }
    
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'irmysql':
    $tblData = $Search->fromMysqlByRange($_POST['ir_codArtA'], $_POST['ir_codArtB'], $errors);

    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
    
    if(isset($_POST['ir_esist'])) {
        
        $tblData = $Search->filterEsist($tblData);
        
    }

    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;

    
case 'irdb':
    $tblData = $Search->byRange($_POST['ir_codArtA'], $_POST['ir_codArtB'], $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'select.tpl';
        break;
    
    }
    
    if(isset($_POST['ir_esist'])) {
        
        $tblData = $Search->filtersist($tblData);
        
    }
    
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'trmysql':
    $tblData = $Search->fromMysqlByEANFile(F734_FILE, $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
        
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'trdb':
    $tblData = $Search->byEANFile(F734_FILE, $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'select.tpl';
        break;
        
    }
    
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'iedb':
    $Search = new SearchEvasione($dbArt, $dbCon, $dbCaa, $dbPrz, $dbMov);
    $Search->setCausaleEvasione(BCODEPRINT_CAUSALE_EVASIONE);
    $tblData = $Search->byEvasione($_POST['ie_data'], $_POST['ie_doc'], $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'select.tpl';
        break;
        
    }
    
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    

case 'pr':
    $BCode = new BCodeAllegro($dbArt, $dbCaa, $dbPrz, $_POST['bcLayout'], TMPDIR);

    if(SPIGAXDBG) error_log(print_r($_POST, true));
    
    $BCode->forceP1 = (isset($_POST['bcP1']))?true:false;
    $bcData = $BCode->createListFromAssoc($_POST['bcQt']);
    
    if(SPIGAXDBG) error_log(print_r($bcData, true));
    
    $bcData = $BCode->getBCodeDataFromList($bcData);
    
    if(SPIGAXDBG) error_log(print_r($bcData, true));
    
    $bcData = $BCode->getEtiMulti($bcData);
    $fTmp = TMPDIR.'/bcode_'.session_id();
    file_put_contents($fTmp, $bcData);
    
    $result = system(BCODE_CMD.' '.$fTmp, $resultCode);
    
    if(!$resultCode) {
        
        $errors[] = 'Stampa inviata : '.$result;
        
    } else {
        
        $errors[] = 'Si è verificato un errore: cod. '.$resultCode.' - '.$result;
        
    }
    
    $smarty->assign('errors', $errors); 
    $template = 'main.tpl';
    break;
    
    
default:
   $template = 'main.tpl';
   break;
}

/* chiudo i db spiga */
mfcdb_close($dbArt);
mfcdb_close($dbCon);
mfcdb_close($dbCaa);
mfcdb_close($dbPrz);
mfcdb_close($dbMov);



$smarty->assign('im_codArt', $_SESSION['im_codArt']);
$smarty->assign('ir_codArtA', $_SESSION['ir_codArtA']);
$smarty->assign('ir_codArtB', $_SESSION['ir_codArtB']);
$smarty->assign('ir_esist', $_SESSION['ir_esist']);
$smarty->assign('ie_data', $_SESSION['ie_data']);
$smarty->assign('ie_doc', $_SESSION['ie_doc']);
$smarty->assign('function', $_GET['function']);

$smarty->display('mgcaa_bcodePrint/'.$template);

?>
