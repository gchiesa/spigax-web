{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
</script>
{/literal}

</head>
<body onload="">

{include file="std.frameup.tpl" }


<div id="content">
<h2>OUTLET - Statistiche di Vendita</h2>

<table class="artikel_liste" width="100%">
<thead><tr class="heading"><td></td><td>Venduto</td><td>Reso da Clienti</td><td>Venduto Effettivo</td><td>Perc.Ricarico Medio</td></tr></thead>
<tbody>
<tr>
    <td>
        Statistiche vendita<br />
        {if $dateAll != 'checked'}dalla data {$dateA} alla data {$dateB}<br />{/if}
        {if $codArtAll != 'checked'}range articoli : da {$codArtA} a {$codArtB}<br />{/if}
    </td>
    <td align="right">
        &euro;&nbsp;{$tblData.venduto|string_format:"%.2f"}
    </td>
    <td align="right">
        &euro;&nbsp;{$tblData.reso|string_format:"%.2f"}
    </td>
    <td align="right">
        &euro;&nbsp;{$tblData.saldo|string_format:"%.2f"}
    </td>
    <td align="right">
        {$tblData.ric|string_format:"%.2f"} %
    </td>
</tr>
</tbody>
</table>

<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

