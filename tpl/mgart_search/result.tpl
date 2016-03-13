{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
function getDetails(codArt)
{
    var url = 'details.php?codArt=' + codArt;
    
    jsspigaxpopup = window.open(url, 'spigaxpopup', 'height=480,width=640,alwaysRaised=yes');
    top.jsspigaxpopup.focus();
}
</script>
{/literal}

</head>
<body onload="onLoadFunctions();" onfocus="checkPopup();">

{include file="std.frameup.tpl" }

<div id="content">
<h2>Archivio Articoli - Consultazione Articoli</h2>

<p>
<tt>
<table class="confReport">
<tr>
    <td></td>
    <td class="th">Solo Art. Esistenti</td>
    <td class="th">Solo Art. in Outlet</td>
    <td class="th">Solo Art. Movimentati</td>
    <td class="th">Vis. Prezzi Acquisto</td>
    <td class="th">Solo Art. Giornalino</td>
</tr>
<tr>
    <td width="50%">
        Filtro codice.......:&nbsp;{$codArt}<br />
        Filtro descrizione..:&nbsp;{$descriz}<br />
        <br />
        Tempo mysql.......: &nbsp;{$tmysql|string_format:"%.2f"} secs<br />
        Tempo mfc.........: &nbsp;{$tmfc|string_format:"%.2f"} secs<br />
        Righe analizzate..: &nbsp;{$trows} 
    </td>
    <td align="center">{$flEsist}</td>
    <td align="center">{$flOutlet}</td>
    <td align="center">{$flMovim}</td>
    <td align="center">{$flP8}</td>
    <td align="center">{$flGiorn}</td>
</tr>
</table>
</tt>
</p>

<h3>Risultati della ricerca</h3>

<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px" row_onclick="getDetails('\$codArt');"}
    {column id="codArt" align="left" name="Codice Articolo" }
    {column id="descriz" align="left" name="Descrizione" }
    {column id="esist"  align="center" name="Esistenza" }
    {column id="pVen" align="right" output_printf="€ %8.2f" name="P.Vendita"}
    {column id="flGiorn" align="center" name="Giornalino Attivo"}
    {column id="flP10" align="center" name="Prezzo 10 Attivo" }
    {column id="pAcq" align="right" output_printf="€ %8.2f" name="P.Acquisto" }
    {column id="pRic" align="right" output_printf="%4.2f %%" name="Perc.Ricarico" }
    {column id="ultAcq" align="right" name="Ultimo Acquisto" }
{/datatable}
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />

{include file="std.footer.tpl" }

