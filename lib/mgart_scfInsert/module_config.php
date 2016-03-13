<?
ini_set('memory_limit', '256M');
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();

/*
 * preparo l'array dei metri testate e ganci
 */
for($k=1; $k<=14; $k++) {
	
	$selectScfMt[$k] = 'Metro '.$k;
}

$selectScfMt['T1'] = 'Testata 1';
$selectScfMt['T2'] = 'Testata 2';
$selectScfMt['T3'] = 'Testata 3';
$selectScfMt['T4'] = 'Testata 4';

$selectScfMt['G1'] = 'Griglia 1';
$selectScfMt['G2'] = 'Griglia 2';
$selectScfMt['G3'] = 'Griglia 3';
$selectScfMt['G4'] = 'Griglia 4';
$selectScfMt['G5'] = 'Griglia 5';
$selectScfMt['G6'] = 'Griglia 6';
$selectScfMt['G7'] = 'Griglia 7';
$selectScfMt['G8'] = 'Griglia 8';

$smarty->assign('selectScfMt', $selectScfMt);

?>
