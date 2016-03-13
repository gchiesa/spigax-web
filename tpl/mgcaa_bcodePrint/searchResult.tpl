{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
// data fine validità p10
var rules = new Array(); 

</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Stampa Bar Code - Selezione Articoli</h2>

<form id="f_editor" name="editor" method="POST" action="main.php?function={$function}">
<input type="hidden" name="job" value="1" />
<tt>Stampante BCode...:</tt><input type="text" name="bcPrinter" value="{$defBCodePrinter}" disabled/><br />
<tt>Tipo di layout....:</tt><select name="bcLayout" >{html_options options=$select_checkType selected=$checkType}</select><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Forza stampa P1...:</a><input type="checkbox" name="bcP1" /><br />
<br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php?function={$function}';" /><br />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px" row_onclick=""}
    {column id="qt" align="center" name="Quantità Etichette" default="1"}
    {column id="codArt" align="left" name="Codice Articolo" }
    {column id="descriz" align="left" name="Descrizione" }
    {column id="esist"  align="center" name="Esistenza" }
    {column id="pVen" align="right" output_printf="€ %8.2f" name="Prezzo P1"}
    {column id="pPromo" align="right" output_printf="€ %8.2f" name="Prezzo P10"}
    {column id="pPromoDval" align="center" name="Validità"}
    {column id="EAN" align="left" name="Codice EAN" }
{/datatable}
<input type="button" value=" Invia Stampa " onclick="$('f_editor').action += '&method=pr';$('f_editor').submit();" /><br />
<br /><br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php&function={$function}';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{literal}
<script type="text/javascript">
convertCellToInputText('datatable_0', 0, 'bcQt', 1, 'size="10" maxlength="10" style="text-align:right;"');
</script>
{/literal}

{include file="std.footer.tpl" }

