<?
ini_set('max_execution_time', 60*60);
ini_set('memory_limit', '256M');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();
$smarty->assign('select_checkType', array('E'=> 'Incongruenze EAN', 'Z' => 'Incongruenze tipo Z', 'M' => 'Incongruenze tipo M'));
?>
