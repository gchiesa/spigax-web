<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.search.php');
require_once('../classes/class.outlet.php');


if(!isset($_POST['job'])) {

   $smarty->assign('im_codArt', $_SESSION['im_codArt']);
   $smarty->assign('ir_codArtA', $_SESSION['ir_codArtA']);
   $smarty->assign('ir_codArtB', $_SESSION['ir_codArtB']);
   $smarty->assign('ir_esist', $_SESSION['ir_esist']);

   $smarty->display('mgart_outletInsert/main.tpl');
   die();
    
}

/* salvo le variabili di sessione */
$_SESSION['im_codArt'] = $_POST['im_codArt'];
$_SESSION['ir_codArtA'] = $_POST['ir_codArtA'];
$_SESSION['ir_codArtB'] = $_POST['ir_codArtB'];
$_SESSION['ir_esist'] = (isset($_POST['ir_esist']))?'checked':'';

/* apertura database */
$dbArt = mfcdb_open($tables['mgart']); 
$dbCon = mfcdb_open($tables['mgcon']);
$dbCaa = mfcdb_open($tables['mgcaa']);
$dbPrz = mfcdb_open($tables['mgprz']);


$S = new Search($dbArt, $dbCon, $dbCaa, $dbPrz);
$Outlet = new Outlet($dbArt, $dbCaa, $dbCon, $dbPrz);


$errors = array();

switch ($_GET['method'] )  
{
case 'im':
    $tblData = $S->byCodArt($_POST['im_codArt'], $errors);
    
    if(count($errors)) {
      
      $smarty->assign('errors', $errors);
      $template = 'main.tpl';
      break;
    }
    
    $smarty->assign('datatable', $tblData);
    $template =  'searchResult.tpl';
    break;


    
case 'ir':
    $tblData = $S->byRange($_POST['ir_codArtA'], $_POST['ir_codArtB'], $errors);

    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
    
    if(isset($_POST['ir_esist'])) {
        
        $tblData = $S->filterEsist($tblData);
        
    }

    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;

    
case 'tr':
    $tblData = $S->byEANFile(F734_FILE, $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
        
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'up':
    $Outlet->setDocVenDate($_POST['docvenDate']);
    $Outlet->insert($_POST['inOutlet']);
    $tblData = $S->tblDataByCodArts($_POST['inOutlet'], $errors);    
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        
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
$smarty->assign('ir_codArtA', $_SESSION['ir_codArtA']);
$smarty->assign('ir_codArtB', $_SESSION['ir_codArtB']);
$smarty->assign('ir_esist', $_SESSION['ir_esist']);

$smarty->display('mgart_outletInsert/'.$template);

?>
