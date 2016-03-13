{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();
rules[0] = 'codA|required|inserire il codice iniziale';
rules[1] = 'codB|custom|checkOrder()';
rules[2] = 'checkType|required|selezionare il tipo di controllo';
rules[3] = 'codB|required|inserire il codice finale';
rules[4] = 'codB|1|and|3';

yav.addHelp('codA', 'Inserire il codice iniziale');
yav.addHelp('codB', 'Inserire il codice finale');
yav.addHelp('checkType', 'Selezionare il tipo di controllo');

function checkOrder()
{
    F = document.forms['editor'];
    if(F.codA.value > F.codB.value ) {
        return 'Ordine incongruente';
    }
    return null;
}
</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Codici Alternativi - Ricerca Incongruenze</h2>

<form name="editor" method="POST" action="main.php">
<input type="hidden" name="job" value="1" />
<p>
<tt>Da Codice........:&nbsp;</tt><input type="text" name="codA" value="{$codA}" maxlength="13" /><span id="errorsDiv_codA"></span><br />
<tt>A Codice.........:&nbsp;</tt><input type="text" name="codB" value="{$codB}" maxlength="13" /><span id="errorsDiv_codB"></span><br />
<tt>Tipo controllo...:&nbsp;</tt>
<select name="checkType" size=3>
{html_options options=$select_checkType selected=$checkType}
</select>
<span id="errorsDiv_checkType"></span><br />
</p>
<input type="submit" value=" Conferma " onclick="return yav.performCheck('editor', rules, 'classic');" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

