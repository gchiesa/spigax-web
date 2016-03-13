{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();

// Inserimento Singolo - codArt 
rules[0] = 'im_codArt|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

// Inserimento Range
rules[1] = 'ir_codArtA|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZz.';
rules[2] = 'ir_codArtB|mask|0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZz.';
// Terminalino 

yav.addHelp('im_codArt', 'Inserire il codice articolo');
yav.addHelp('ir_codArtA', 'Inserire il codice articolo iniziale');
yav.addHelp('ir_codArtB', 'Inserire il codice articolo finale');
yav.addHelp('ie_data', 'Inserire la data di evasione ordine ( gg/mm/aaaa )');
yav.addHelp('ie_doc', 'Inserire il documento di evasione (es. 1234/A)');
</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>{$pageTitle}</h2>

{if isset($errors)}
<div class="errordiv">
<ul>
{foreach item=row from=$errors}
<li>{$row}</li>
{/foreach}
</ul>
</div>
{/if}

<form id="f_editor" name="editor" method="POST" action="main.php?function={$function}">
<input type="hidden" name="job" value="1" />

{* ----- SEZIONE OUTLET ----- *}
{if $function == 'mysql_outlet'}
<div class="div_separator">&nbsp;Selezione articolo singolo</div>
<tt>Codice EAN/Articolo...:&nbsp;<input type="text" name="im_codArt" value="{$im_codArt}" maxlength="13" /></tt>&nbsp;<input type="button" value=" Conferma " onclick="$('f_editor').action += '&method=immysql';$('f_editor').submit();" /><span id="errorsDiv_im_codArt"></span>

<div class="div_separator">&nbsp;Selezione range articoli</div>
<tt>Da codice articolo...:&nbsp;<input type="text" name="ir_codArtA" value="{$ir_codArtA}" maxlength="13" /></tt><span id="errorsDiv_ir_codArtA"></span><br />
<tt>A codice articolo....:&nbsp;<input type="text" name="ir_codArtB" value="{$ir_codArtB}" maxlength="13" /></tt>&nbsp;<input type="button" value=" Conferma " onclick="$('f_editor').action += '&method=irmysql';$('f_editor').submit();" /><span id="errorsDiv_ir_codArtB"></span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Solo articoli esist..:</a>&nbsp;<input type="checkbox" name="ir_esist" {if isset($ir_esist)}{$ir_esist}{else}checked{/if} />

<div class="div_separator">&nbsp;Selezione su archivio terminalino</div>
<input type="button" value=" Carica dati " onclick="$('f_editor').action += '&method=trmysql';$('f_editor').submit();"/>
{/if}
{* ----- SEZIONE OUTLET STOP ----- *}

{* ----- SEZIONE SPIGA X ----- *}
{if $function == 'db'}
<div class="div_separator">&nbsp;Selezione articolo singolo</div>
<tt>Codice EAN/Articolo...:&nbsp;<input type="text" name="im_codArt" value="{$im_codArt}" maxlength="13" /></tt>&nbsp;<input type="button" value=" Conferma " onclick="$('f_editor').action += '&method=imdb';$('f_editor').submit();" /><span id="errorsDiv_im_codArt"></span>

<div class="div_separator">&nbsp;Selezione range articoli</div>
<tt>Da codice articolo...:&nbsp;<input type="text" name="ir_codArtA" value="{$ir_codArtA}" maxlength="13" /></tt><span id="errorsDiv_ir_codArtA"></span><br />
<tt>A codice articolo....:&nbsp;<input type="text" name="ir_codArtB" value="{$ir_codArtB}" maxlength="13" /></tt>&nbsp;<input type="button" value=" Conferma " onclick="$('f_editor').action += '&method=irdb';$('f_editor').submit();" /><span id="errorsDiv_ir_codArtB"></span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Solo articoli esist..:</a>&nbsp;<input type="checkbox" name="ir_esist" {if isset($ir_esist)}{$ir_esist}{else}checked{/if} />

<div class="div_separator">&nbsp;Selezione da evasione ordine</div>
<tt>Data Evasione.....:</tt>&nbsp;<input type="text" id="ie_data" name="ie_data" value="{$ie_data}" maxlength="10" /><span id="errorsDiv_ie_data"></span><br />
<tt>Numero documento..:</tt>&nbsp;<input type="text" name="ie_doc" value="{$ie_doc}" maxlength="10" />&nbsp;<input type="button" value=" Conferma " onclick="$('f_editor').action += '&method=iedb';$('f_editor').submit();" /><span id="errorsDiv_ie_doc"></span>
<div class="div_separator">&nbsp;Selezione su archivio terminalino</div>
<input type="button" value=" Carica dati " onclick="$('f_editor').action += '&method=trdb';$('f_editor').submit();"/>

{literal}
<script type="text/javascript">
rules[3] = 'ie_data|date|Data inserita non valida';

var datePicker_ie_data = new DatePicker({
        relative    : 'ie_data',
        language    : 'it', 
        disablePastDate : false,
        disableFutureDate : true, 
        afterClose : yav.performCheck('editor', rules, 'inline')
});
datePicker_ie_data.setDateFormat([ "dd", "mm", "yyyy" ], "/");
</script>
{/literal}
{/if}
{* ----- SEZIONE SPIGA X STOP ----- *}

<br /><br />
<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

