{include file="std.header.tpl" }
</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }

<div id="content">
<h2>Codici Alternativi - Ricerca Incongruenze</h2>


<h3>Risultati della ricerca</h3>
<p><tt>Ricerca <br />
Da..: {$codA}<br />
A...: {$codB}<br />
Controllo su codici..: {$checkType}<br />
Righe analizzate.....: {$rows}</tt></p>

<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />
<tt>
{datatable data=$tbl sortable=0 cycle=1 width="100%" mouseover=1}
    {column id="CodArt" align="left" name="Codice Articolo"}
    {column id="Descriz1" align="left" name="Descrizione"}
    {column id="Esist1"  align="center" name="Esistenza"}
    {column id="CodArt2" align="left"  name="Articolo Richiamato"}
    {column id="Descriz2" align="left" name="Descrizione2"}
    {column id="Esist2" align="center" name="Esistenza2"}
{/datatable}
</tt>
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />

{include file="std.footer.tpl" }

