{include file="std.header.tpl" }

</head>
<body onload="">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Archivio Articoli - Dettagli Articolo</h2>

<div style="height:200px;">

<div style="float:right;">
<tt>
{datatable data=$caaTbl sortable=0 cycle=1 height="180" mouseover=1}
{column id="type" align="center" name="Tipo Codice"}
{column id="code" align="left" name="Codice Alternativo"}
{/datatable}
</tt>
</div>

<p>
<tt>
Codice Art..: {$tblData.codArt}<br />
Descrizione.: {$tblData.descriz}<br />
<br />
Fornitore..: {$tblData.forCod}&nbsp;&nbsp;{$tblData.forDescriz}<br />
<br />
</tt>
</p>

</div>

<center>
<div style="border-style:solid;border-width:1px;boder-color:#a0a0a0;font-size:2.0em;padding:10px;">
<table width="100%">
<tr>
    <td><tt>EAN:&nbsp;{$ean}</tt></td> 
    <td>Prezzo Vendita: &nbsp;&nbsp; € {$tblData.pVen|string_format:"%5.2f"}</td>
</tr>
</table>
</div>
</center>

<input type="button" value=" Esci " onclick="self.close();" />


</div>
{include file="std.footer.tpl" }

