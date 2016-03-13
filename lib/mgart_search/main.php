<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.search.fulltext.php');
require_once('../classes/class.outlet.php');

if(!isset($_POST['job'])) {

    $smarty->assign('codArt', $_SESSION['codArt']);
    $smarty->assign('descriz', $_SESSION['descriz']);
    $smarty->assign('flEsist', $_SESSION['flEsist']);
    $smarty->assign('flMovim', $_SESSION['flMovim']);
    $smarty->assign('flP8', $_SESSION['flP8']);
    $smarty->assign('flGiorn', $_SESSION['flGiorn']);
    $smarty->assign('flOutlet', $_SESSION['flOutlet']);
    $smarty->assign('outletExists', Outlet::exists() );

    $smarty->display('mgart_search/main.tpl');
    die();
    
}

/* salvo i dati per mantenere la sessione */
$_SESSION['codArt'] = $_POST['codArt'];
$_SESSION['descriz'] = $_POST['descriz'];
$_SESSION['flEsist'] = ($_POST['flEsist'])?'checked':'';
$_SESSION['flMovim'] = ($_POST['flMovim'])?'checked':'';
$_SESSION['flP8'] = ($_POST['flP8'])?'checked':'';
$_SESSION['flGiorn'] = ($_POST['flGiorn'])?'checked':'';
$_SESSION['flOutlet'] = ($_POST['flOutlet'])?'checked':'';


$search = new Search($_POST);

/* apro i database spiga */
$dbArt = mfcdb_open($tables['mgart']); 
$dbCon = mfcdb_open($tables['mgcon']);
$dbPrz = mfcdb_open($tables['mgprz']);

$search->setDB($dbArt, $dbCon, $dbPrz);
$resultData =  $search->performSearch();

mfcdb_close($dbArt);
mfcdb_close($dbCon);
mfcdb_close($dbPrz);

$smarty->assign('datatable', $resultData);
$smarty->assign('codArt', $search->codArt);
$smarty->assign('descriz', $search->descriz);
$smarty->assign('flEsist', ($_POST['flEsist'])?'Si':'No');
$smarty->assign('flMovim', ($_POST['flMovim'])?'Si':'No');
$smarty->assign('flP8', ($_POST['flP8'])?'Si':'No');
$smarty->assign('flGiorn', ($_POST['flGiorn'])?'Si':'No');
$smarty->assign('flOutlet', ($_POST['flOutlet'])?'Si':'No');
$smarty->assign('tmysql', $search->tmysql);
$smarty->assign('tmfc', $search->tmfc);
$smarty->assign('trows', $search->trows);

$smarty->display('mgart_search/result.tpl');
?>
