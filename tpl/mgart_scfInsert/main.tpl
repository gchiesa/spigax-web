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
    
</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Gestione Scaffali - Inserimento Articoli</h2>

{if isset($errors)}
<div class="errordiv">
{foreach item=row from=$errors}
- {$row}
{/foreach}
</div>
{/if}

<form id="f_editor" name="editor" method="POST" action="main.php">
<input type="hidden" name="job" value="1" />

<div class="div_separator">&nbsp;Inserimento Singolo</div>
<tt>Codice EAN/Articolo...:&nbsp;<input type="text" name="im_codArt" value="{$im_codArt}" maxlength="13" /></tt>&nbsp;<input type="button" value=" Conferma " onclick="$('f_editor').action='main.php?method=im';$('f_editor').submit();" /><span id="errorsDiv_im_codArt"></span>

<div class="div_separator">&nbsp;Inserimento Terminalino</div>
<input type="button" value=" Carica dati " onclick="$('f_editor').action='main.php?method=tr';$('f_editor').submit();"/>

<br /><br />
<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

