<?
ini_set('memory_limit', '256M');
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

define('BCODEPRINT_CAUSALE_EVASIONE', '37');

session_start();

$bcLayouts = array();
$bcodeTmp = explode(',', BCODE_LAYOUT_FILES);

foreach($bcodeTmp as $row) {
    
    list($fDesc, $fFile) = explode('|', trim($row));
    $bcLayouts[$fFile] = $fDesc;
    
}

$smarty->assign('select_checkType', $bcLayouts);
$smarty->assign('defBCodePrinter', BCODE_PRINTER);
$smarty->assign('functions', array('mysql_outlet' => 'Articoli Outlet', 'db' => 'Spiga X III') ); 

?>
