{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();
rules[0] = 'codArtA|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ.';
rules[1] = 'codArtB|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZz';
rules[2] = 'dateA|date|Data Immessa non valida';
rules[3] = 'dateB|date|Data Immessa non valida';

yav.addHelp('codArtA', 'Inserire il codice iniziale');
yav.addHelp('codArtB', 'Inserire il codice finale');
yav.addHelp('dateA', 'Inserire la data iniziale ( tipo gg/mm/aaaa )');
yav.addHelp('dateB', 'Inserire la data finale ( tipo gg/mm/aaaa )');

function activateCodArt(id)
{
    if(id.checked) {
        
        $('f_editor').codArtA.disabled = true;
        $('f_editor').codArtB.disabled = true;
    
    } else {
        
        $('f_editor').codArtA.disabled = false;
        $('f_editor').codArtB.disabled = false;
        
    }
       
}




function activateDate(id)
{
    if(id.checked) {
        
        $('f_editor').dateA.disabled = true;
        $('f_editor').dateB.disabled = true;
    
    } else {
        
        $('f_editor').dateA.disabled = false;
        $('f_editor').dateB.disabled = false;
        
    }
}    
</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>OUTLET - Statistiche di Vendita</h2>

{if isset($errors)}
<div class="errordiv">
{foreach item=row from=$errors}
- {$row}
{/foreach}
</div>
{/if}

<form id="f_editor" name="editor" method="POST" action="main.php">
<input type="hidden" name="job" value="1" />

<tt>Tipo di report..:</tt>&nbsp;<select name="statsType">{html_options options=$select_checkType selected=$statsType}</select><br />
<input type="checkbox" name="codArtAll" onclick="activateCodArt(this);" /><a onclick="linkToCheckbox(this, 'PREV');" class="checkbox_link">Tutti gli articoli</a>&nbsp;&nbsp;<input type="checkbox" name="dateAll" onclick="activateDate(this);" /><a onclick="linkToCheckbox(this, 'PREV');" class="checkbox_link">Qualsiasi data</a><br />
<tt>Da codice articolo...:</tt>&nbsp;<input type="text" name="codArtA" value="{$codArtA}" /><span id="errorsDiv_codArtA"></span><br />
<tt>A codice articolo....:</tt>&nbsp;<input type="text" name="codArtB" value="{$codArtB}" /><span id="errorsDiv_codArtB"></span><br />
<tt>Da data.........:</tt>&nbsp;<input type="text" id="dateA" name="dateA" value="{if isset($dateA)}{$dateA}{else}{$defaultDateA}{/if}" /><span id="errorsDiv_dateA"></span><br />
<tt>A data..........:</tt>&nbsp;<input type="text" id="dateB" name="dateB" value="{$dateB}" /><span id="errorsDiv_dateB"></span><br />
{literal}
<script type="text/javascript">
var dpck_dateA = new DatePicker({
        relative    : 'dateA',
        language    : 'it', 
        afterClose : yav.performCheck('editor', rules, 'inline')
});
dpck_dateA.setDateFormat([ "dd", "mm", "yyyy" ], "/");

var dpck_dateB = new DatePicker({
        relative    : 'dateB',
        language    : 'it', 
        afterClose : yav.performCheck('editor', rules, 'inline')
});
dpck_dateB.setDateFormat([ "dd", "mm", "yyyy" ], "/");

</script>
{/literal}
<br />
<input type="button" value=" Crea Report " onclick="if(yav.performCheck('editor', rules, 'inline')) $('f_editor').submit();" />
<br /><br />
<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

