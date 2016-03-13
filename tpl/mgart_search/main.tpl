{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();
rules[0] = 'codArt|required|Occorre specificare il codice articolo';
rules[1] = 'descriz|custom|checkDescr()';
rules[2] = 'codArt|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ*';
rules[3] = 'codArt|0|and|2';
rules[4] = 'descriz|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ,.';
rules[5] = 'descriz|4|and|1';

yav.addHelp('codArt', 'Inserire parte del codice articolo (* per ricerche full-text)');
yav.addHelp('descriz', 'Inserire parole chiave separate da virgola es: TRAP oppure MENS,NOCE');
yav.addHelp('flOutlet', 'Selezionare per visualizzare solo articoli Outlet');
yav.addHelp('flEsist', 'Selezionare per visualizzare solo articoli esistenti');
yav.addHelp('flMovim', 'Selezionare per visualizzare solo articoli movimentati');
yav.addHelp('flP8', 'Selezionare per visualizzare prezzo 8 e percentuale ricarico');
yav.addHelp('flGiorn', 'Selezionare per visualizzare solo articoli giornalino');

function checkDescr()
{
    F = document.forms['editor'];
    
    F.descriz.value = F.descriz.value.strip();
    F.codArt.value = F.codArt.value.strip();
    
    if(F.codArt.value == '*' && F.descriz.value.blank() ) {
        
        return 'Occorre specificare un criterio nella descrizione per una ricerca full text';
    
    }

    if(F.descriz.value == ',') {
        
        return 'Criterio non valido';
        
    }
    
    return null;
}

function resetForm()
{
    F = document.forms['editor'];
    
    F.descriz.value = "";
    F.codArt.value = "";
    F.flEsist.checked = true;
    F.flMovim.checked = true;
    F.flP8.checked = false;
    F.flGiorn.checked = false;
}
    
</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Archivio Articoli - Consultazione Articoli</h2>

<form name="editor" method="POST" action="main.php">
<input type="hidden" name="job" value="1" />
<table>
<tr>
    <td height="60" width="300" valign="top">
        <tt>Codice Articolo..:&nbsp;</tt><input type="text" name="codArt" value="{$codArt}" maxlength="13" size="13" /><br />
        <span id="errorsDiv_codArt"></span>
    </td>
    <td height="60" valign="top">
        <tt>Descrizione...:&nbsp;</tt><input type="text" name="descriz" value="{$descriz}" maxlength="30" size="30" /><br />
        <span id="errorsDiv_descriz"></span>
    </td>
</tr>
</table>
<p>

{if $outletExists == true}
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Solo OUTLET............:&nbsp;</a><input type="checkbox" name="flOutlet" {if isset($flOutlet)}{$flOutlet}{else}checked{/if} /><span id="errorsDiv_flOutlet"></span><br />
{/if}
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Articoli esistenti.....:&nbsp;</a><input type="checkbox" name="flEsist" {if isset($flEsist)}{$flEsist}{else}checked{/if} /><span id="errorsDiv_flEsist"></span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Articoli movimentati...:&nbsp;</a><input type="checkbox" name="flMovim" {if isset($flMovim)}{$flMovim}{else}checked{/if} /><span id="errorsDiv_flMovim"></span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Visualizza P8 c/ricar..:&nbsp;</a><input type="checkbox" name="flP8" {$flP8} /><span id="errorsDiv_flP8"></span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Solo art.giornalino....:&nbsp;</a><input type="checkbox" name="flGiorn" {$flGiorn} /><span id="errorsDiv_flGiorn"></span><br />
</p>
<input type="submit" value=" Avvia Ricerca " onclick="return yav.performCheck('editor', rules, 'classic');" />&nbsp;<input type="button" value=" Pulisci " onclick="resetForm();" /> &nbsp;&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

