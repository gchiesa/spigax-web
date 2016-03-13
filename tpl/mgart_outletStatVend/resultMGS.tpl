{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
</script>
{/literal}

</head>
<body onload="">

{include file="std.frameup.tpl" }


<div id="content">
<h2>OUTLET - Statistiche di Vendita Mondi Gruppi Sottogruppi</h2>

<tt>Statistiche di vendita <br />
{if $dateAll != 'checked'}dalla data {$dateA} alla data {$dateB}<br />{/if}
{if $codArtAll != 'checked'}range articoli : da {$codArtA} a {$codArtB}<br />{/if}
</tt>

<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />
<div class="artikel_liste_div" style="height:300px;">
<table class="artikel_liste" width="100%">
<thead><tr class="heading"><td>Mondo</td><td>Gruppo</td><td>Sottogruppo</td><td>Venduto</td><td>Reso da Clienti</td><td>Venduto Effettivo</td><td>Perc.Ricarico Medio</td></tr></thead>
<tbody>

{foreach item=row from=$tblData}
<tr>
<td>{if $row.mondo != NULL}<span style="font-size:0.8em">{$row.mondo}</span> {$row.mondo_d}{/if}</td>
<td>{if $row.gruppo != NULL}<span style="font-size:0.8em">{$row.gruppo}</span> {$row.gruppo_d}{/if}</td>
<td>{if $row.sottogruppo != NULL}<span style="font-size:0.8em">{$row.sottogruppo}</span> {$row.sottogruppo_d}{/if}</td>
<td align="right">&euro;&nbsp;{$row.venduto|string_format:"%.2f"}</td>
<td align="right">&euro;&nbsp;{$row.reso|string_format:"%.2f"}</td>
<td align="right">&euro;&nbsp;{$row.saldo|string_format:"%.2f"}</td>
<td align="center">{$row.ric|string_format:"%.2f"} %</td>
</tr>
{/foreach}

</tbody>
</table>
</div>

<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

