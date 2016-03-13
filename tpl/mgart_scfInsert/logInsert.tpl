{include file="std.header.tpl" }

{literal}
<script type="text/javascript">

</script>
{/literal}

</head>
<body onload="">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Gestione Scaffali - Stampa Articoli Inseriti</h2>

{if isset($errors)}
<div class="errordiv">
{foreach item=row from=$errors}
- {$row}<br />
{/foreach}
</div>
{/if}

<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" /><br />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px"}
    {column id="codArt" align="left" name="Codice Articolo" }
    {column id="descriz" align="left" name="Descrizione" }
    {column id="esist"  align="center" name="Esistenza" }
    {column id="pVen" align="right" output_printf="€ %8.2f" name="Prezzo P1"}
    {column id="pPromo" align="right" output_printf="€ %8.2f" name="Prezzo P10"}
    {column id="pPromoDval" align="center" name="Validità"}
    {column id="EAN" align="left" name="Codice EAN" }
    {column id="scfDescriz" align="center" name="Scaffale" truncate="10"}
    {column id="scfMt" align="center" name="Posiz."}    
{/datatable}
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

