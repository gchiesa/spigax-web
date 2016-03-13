{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
function changeSel(id)
{
    cb = id.down('input[type="checkbox"]');
    cb.checked = (cb.checked)?false:true;
}
function selAll(checkBox)
{
    cb = $('f_editor').elements[checkBox];
    
    if(!cb.length) cb.checked = true;
    
    for(var c = 0; c < cb.length; c++) {
        
        cb[c].checked = true;
    
    }
}
function unselAll(checkBox)
{
    cb = $('f_editor').elements[checkBox];
    
    if(!cb.length) cb.checked = false;
    
    for(var c = 0; c < cb.length; c++) {
        
        cb[c].checked = false;
    
    }
}

function checkDelete()
{
    if(confirm('Confermi la rimozione degli elementi selezionati?\r\nLa cancellazione è irreversibile.\r\nSe richiesto verranno azzerate le promozioni P10 su archivio prezzi SPIGA X')) {
        
        $('f_editor').action='main.php?method=up';
        $('f_editor').submit();
        
    }
}
</script>
{/literal}

</head>
<body onload="">

{include file="std.frameup.tpl" }


<div id="content">
<h2>OUTLET - Rimozione Articoli - Selezione</h2>

<form id="f_editor" name="editor" method="POST" action="">
<input type="hidden" name="job" value="1" />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Disattiva link a P10 se presenti...:</a><input type="checkbox" name="unlinkP10" checked /><br/>
<br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" /><br />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px" row_onclick="changeSel(this);"}
    {column id="codArt" align="center" checkboxes="delOutlet" name="Rimuovi"}
    {column id="codArt" align="left" name="Codice Articolo" }
    {column id="descriz" align="left" name="Descrizione" }
    {column id="esist"  align="center" name="Esistenza" }
    {column id="pVen" align="right" output_printf="€ %8.2f" name="Prezzo P1"}
    {column id="pPromo" align="right" output_printf="€ %8.2f" name="Prezzo P10"}
    {column id="pPromoDval" align="center" name="Validità"}
    {column id="EAN" align="left" name="Codice EAN" }
{/datatable}
<input type="button" value=" Seleziona tutti " onclick="selAll('delOutlet[]');" />&nbsp;<input type="button" value=" Deseleziona tutti " onclick="unselAll('delOutlet[]');" />&nbsp;<input type="button" value=" Aggiorna Outlet " onclick="checkDelete();"/>
<br /><br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

