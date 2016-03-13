<?
ini_set('memory_limit', '256M');
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);


define('DOCVEN_DEFAULT_RIC', '60');

session_start();

$smarty->assign('select_checkType', array('tot'=> 'Solo Totali Rapido', 'mgs' => 'Per Mondi/Gruppi/Sottogruppi', 'det' => 'Dettagliata c/ricarichi'));
$smarty->assign('defaultDateA', '01/'.date('m/Y', time()));

?>
