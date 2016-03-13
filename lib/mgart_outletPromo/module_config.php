<?
ini_set('memory_limit', '256M');
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();

$k = 0;
$option_perc = array();
for($k = 0; $k <= 90; $k += 5) {
    
    $option_perc[$k] = $k.' %';
    
}


$smarty->assign('select_checkType', $option_perc);
$smarty->assign('lastOfYear', '31/12/'.date("Y", time())); 
?>
