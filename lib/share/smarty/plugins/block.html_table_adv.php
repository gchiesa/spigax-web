<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_table_adv} function plugin
 *
 * Type:     block<br>
 * Name:     html_table_adv<br>
 * Date:     Feb 17, 2003<br>
 * Purpose:  make an html table from an array of arrays of data with
 *           instant customized cell layout<br>
 * Input:<br>
 *         - loop = array to loop through
 *         - cols = number of columns
 *         - rows = number of rows
 *         - table_attr = table attributes
 *         - tr_attr = table row attributes (arrays are cycled)
 *         - td_attr = table cell attributes (arrays are cycled)
 *         - trailpad = value to pad trailing cells with
 *         - vdir = vertical direction (default: "down", means top-to-bottom)
 *         - hdir = horizontal direction (default: "right", means left-to-right)
 *         - inner = inner loop (default "cols": print $loop line by line,
 *                   $loop will be printed column by column otherwise)
 *         - ldelim = the left delimiter for finding variables in content
 *                    (default: "[[")
 *         - rdelim = the right delimiter for finding variables in content
 *                    (default: "]]")
 *
 *
 * Examples:
 * <pre>
 * PHP: $data = array(
 *        array('name'=>'Marc', 'city'=>'Miami'),
 *        array('name'=>'Someone', 'city'=>'Somewhere')
 *      );
 * TPL: {html_table_adv loop=$data}Hello [[name]], you're from [[city]].{/html_table_adv}
 * Output: a table with two cells modified with each person's name and city as seen in the TPL
 *         code
 * </pre>
 * @author   Modified by Marc Melvin <marc@meltedmediainc.com>
 * @version  1.0
 * @link http://smarty.php.net/manual/en/language.function.html.table.php {html_table}
 *          (Smarty online manual)
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_block_html_table_adv($params, $content, &$smarty)
{
    $table_attr = 'border="1"';
    $tr_attr = '';
    $td_attr = '';
    $cols = 3;
    $rows = 3;
    $trailpad = '&nbsp;';
    $vdir = 'down';
    $hdir = 'right';
    $inner = 'cols';
    $ldelim = '[[';
    $rdelim = ']]';

    if (!isset($params['loop'])) {
        $smarty->trigger_error("html_table_adv: missing 'loop' parameter");
        return;
    }

    foreach ($params as $_key=>$_value) {
        switch ($_key) {
            case 'loop':
                $$_key = (array)$_value;
                break;

            case 'cols':
            case 'rows':
                $$_key = (int)$_value;
                break;

            case 'table_attr':
            case 'trailpad':
            case 'hdir':
            case 'vdir':
            case 'inner':
            case 'ldelim':
            case 'rdelim':
                $$_key = (string)$_value;
                break;

            case 'tr_attr':
            case 'td_attr':
                $$_key = $_value;
                break;
        }
    }

    $loop_count = count($loop);
    if (empty($params['rows'])) {
        /* no rows specified */
        $rows = ceil($loop_count/$cols);
    } elseif (empty($params['cols'])) {
        if (!empty($params['rows'])) {
            /* no cols specified, but rows */
            $cols = ceil($loop_count/$rows);
        }
    }

    $output = "<table $table_attr>\n";

    for ($r=0; $r<$rows; $r++) {
        $output .= "<tr" . smarty_function_html_table_cycle('tr', $tr_attr, $r) . ">\n";
        $rx =  ($vdir == 'down') ? $r*$cols : ($rows-1-$r)*$cols;

        for ($c=0; $c<$cols; $c++) {
            $x =  ($hdir == 'right') ? $rx+$c : $rx+$cols-1-$c;
            if ($inner!='cols') {
                /* shuffle x to loop over rows*/
                $x = floor($x/$cols) + ($x%$cols)*$rows;
            }

            if ($x<$loop_count) {
                $new_content = $content;
                foreach ($loop[$x] as $k=>$v) {
                    $new_content = str_replace($ldelim.$k.$rdelim, $v, $new_content);
                }
                $output .= "<td" . smarty_function_html_table_cycle('td', $td_attr, $c) . ">" . $new_content . "</td>\n";
            } else {
                $output .= "<td" . smarty_function_html_table_cycle('td', $td_attr, $c) . ">$trailpad</td>\n";
            }
        }
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    
    return $output;
}

function smarty_function_html_table_cycle($name, $var, $no) {
    if(!is_array($var)) {
        $ret = $var;
    } else {
        $ret = $var[$no % count($var)];
    }
    
    return ($ret) ? ' '.$ret : '';
}


/* vim: set expandtab: */
?>
