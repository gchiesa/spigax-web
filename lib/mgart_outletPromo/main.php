<?
require_once('../config.php');
require_once('module_config.php');
require_once('../classes/class.search.php');
require_once('../classes/class.outlet.php');
require_once('../classes/class.promop10.php');


if(!isset($_POST['job'])) {

   $smarty->assign('im_codArt', $_SESSION['im_codArt']);
   $smarty->assign('ir_codArtA', $_SESSION['ir_codArtA']);
   $smarty->assign('ir_codArtB', $_SESSION['ir_codArtB']);

   $smarty->display('mgart_outletPromo/main.tpl');
   die();
    
}

/* salvo le variabili di sessione */
$_SESSION['im_codArt'] = $_POST['im_codArt'];
$_SESSION['ir_codArtA'] = $_POST['ir_codArtA'];
$_SESSION['ir_codArtB'] = $_POST['ir_codArtB'];

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
    $tblData = $S->fromMysqlByCodArt($_POST['im_codArt'], $errors);
    
    if(count($errors)) {
      
      $smarty->assign('errors', $errors);
      $template = 'main.tpl';
      break;
    }
    
    $smarty->assign('datatable', $tblData);
    $template =  'searchResult.tpl';
    break;


    
case 'ir':
    $tblData = $S->fromMysqlByRange($_POST['ir_codArtA'], $_POST['ir_codArtB'], $errors);

    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
    

    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;

    
case 'tr':
    $tblData = $S->fromMysqlByEANFile(F734_FILE, $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;
        
    }
        
    $smarty->assign('datatable', $tblData);
    $template = 'searchResult.tpl';
    break;
    
    
case 'up':
    if(SPIGAXDBG) error_log("dump POST : ".print_r($_POST, true)."\n"); 
    
    $P10 = new PromoP10($dbArt, $dbPrz);
    $P10->setDval($_POST['dataVal']); 
    
    $result = $P10->loadAndCheckData($_POST['P10'], $errors);
    
    if(!$result || count($errors)) {
        
        $smarty->assign('errors', $errors);
        $template = 'main.tpl';
        break;

    }        
    
    if(SPIGAXDBG) error_log('dump p10 : '.print_r($P10->p10, true));
    
    $P10->update($errors);
    $tblData = $S->tblDataByCodArts(array_keys($_POST['P10']), $errors);
    
    if(count($errors)) {
        
        $smarty->assign('errors', $errors);
        break;

    }        
    
    $smarty->assign('datatable', $tblData);
    
    $template = 'logPromo.tpl';
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

$smarty->display('mgart_outletPromo/'.$template);

?>
