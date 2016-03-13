<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.search.php');
require_once('../classes/class.scf.php');
require_once('../classes/class.scf.insert.php');


/*
 * verifico l'installazione delle tabelle scf
 */
SCF::checkInstall();

if(!isset($_POST['job'])) {

   $smarty->assign('im_codArt', $_SESSION['im_codArt']);

   $smarty->display('mgart_scfInsert/main.tpl');
   die();
    
}

/* salvo le variabili di sessione */
$_SESSION['im_codArt'] = $_POST['im_codArt'];

/* apertura database */
$dbArt = mfcdb_open($tables['mgart']); 
$dbCon = mfcdb_open($tables['mgcon']);
$dbCaa = mfcdb_open($tables['mgcaa']);
$dbPrz = mfcdb_open($tables['mgprz']);


$S = new Search($dbArt, $dbCon, $dbCaa, $dbPrz);

$errors = array();

switch ($_GET['method'] )  
{
case 'im':
    $tblData = $S->byCodArt($_POST['im_codArt'], $errors);
    $tblData = $S->tblDataExtended($tblData, $errors, SEARCH_HAVE_SCF);
    
    if(count($errors)) {
      
      $smarty->assign('errors', $errors);
      $template = 'main.tpl';
      break;
    }
    
    $smarty->assign('datatable', $tblData);
    $template =  'searchResult.tpl';
    break;


    
case 'tr':
    $tblData = $S->byEANFile(F734_FILE, $errors);
    $tblData = $S->tblDataExtended($tblData, $errors, SEARCH_HAVE_SCF);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
        
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'up':
	
	if(SPIGAXDBG) error_log(__FILE__.':'." : post ".print_r($_POST, true));
	
	// istanzio la classe inserimento
	$SCFInsert = new SCFInsert();
	$SCFInsert->update($_POST, $errors);
	
	$tblData = $S->tblDataByCodarts($_POST['inScf'], $errors);
	$tblData = $S->tblDataExtended($tblData, $errors, SEARCH_HAVE_SCF);
	
	if(count($errors)) {
		
		$smarty->assign('errors', $errors);
		$template = 'main.tpl';
		break;
		
	}

	$smarty->assign('datatable', $tblData);
	$template = 'logInsert.tpl';	
	
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



$smarty->assign('im_codArt', $_SESSION['im_codArt']);
$smarty->assign('selectScfSector', SCF::getScfAssoc());
$smarty->display('mgart_scfInsert/'.$template);

?>