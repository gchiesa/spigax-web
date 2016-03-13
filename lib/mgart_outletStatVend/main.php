<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.docven.php');

if(!isset($_POST['job'])) {

    $smarty->assign('codArtA', $_SESSION['codArtA']);
    $smarty->assign('codArtB', $_SESSION['codArtB']);
    $smarty->assign('dateA', $_SESSION['dateA']);
    $smarty->assign('dateB', $_SESSION['dateB']); 
    $smarty->assign('statsType', $_SESSION['statsType']);
   
    $smarty->display('mgart_outletStatVend/main.tpl');
    die();
    
}

/* salvo le variabili di sessione */
$_SESSION['codArtA'] = $_POST['codArtA'];
$_SESSION['codArtB'] = $_POST['codArtB'];
$_SESSION['dateA'] = $_POST['dateA'];
$_SESSION['dateB'] = $_POST['dateB'];
$_SESSION['dateAll'] = ($_POST['dateAll'])?'checked':'';
$_SESSION['codArtAll'] = ($_POST['codArtAll'])?'checked':'';
$_SESSION['statsType'] = $_POST['statsType'];


/* apertura database */
$dbArt = mfcdb_open($tables['mgart']); 
$dbMov = mfcdb_open($tables['mgmov']);
$dbTbl = mfcdb_open($tables['cgtbl']); 

$docVen = new DocVen($dbArt, $dbMov, $dbTbl); 
$docVen->updateDocVen(); 

switch($_POST['statsType'])
{
case 'tot':
    
    $tblData = $docVen->getStatsSimple($_POST);
    $smarty->assign('tblData', $tblData);
    
    $template = 'resultSimple.tpl';
    break;
    
case 'mgs':
    
    $tblData = $docVen->getStatsByMGS($_POST);
    $smarty->assign('tblData', $tblData);
    
    $template = 'resultMGS.tpl';
    break;
    
default:
    $template = 'main.tpl';
    break;
}

mfcdb_close($dbArt);
mfcdb_close($dbMov);
mfcdb_close($dbTbl);

$smarty->assign('statsType', $_SESSION['statsType']);
$smarty->assign('codArtA', $_SESSION['codArtA']);
$smarty->assign('codArtB', $_SESSION['codArtB']);
$smarty->assign('dateA', $_SESSION['dateA']);
$smarty->assign('dateB', $_SESSION['dateB']); 
$smarty->assign('dateAll', $_SESSION['dateAll']);
$smarty->assign('codArtAll', $_SESSION['codArtAll']);

$smarty->display('mgart_outletStatVend/'.$template);
?>
