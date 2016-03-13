{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();

rules[0] = 'docvenDate|date|Data inserita non valida';

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

</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>OUTLET - Inserimento Articoli - Selezione</h2>

<form id="f_editor" name="editor" method="POST" action="">
<input type="hidden" name="job" value="1" />
<tt>Considera in outlet dal...: </tt>&nbsp;<input type="text" id="docvenDate" name="docvenDate" maxlength="10" /><span id="errorsDiv_docvenDate"></span><br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" /><br />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px" row_onclick="changeSel(this);"}
    {column id="codArt" align="center" checkboxes="inOutlet" name="Inserisci "}
    {column id="codArt" align="left" name="Codice Articolo" }
    {column id="descriz" align="left" name="Descrizione" }
    {column id="esist"  align="center" name="Esistenza" }
    {column id="pVen" align="right" output_printf="€ %8.2f" name="Prezzo P1"}
    {column id="pPromo" align="right" output_printf="€ %8.2f" name="Prezzo P10"}
    {column id="pPromoDval" align="center" name="Validità"}
    {column id="EAN" align="left" name="Codice EAN" }
{/datatable}
<input type="button" value=" Seleziona tutti " onclick="selAll('inOutlet[]');" />&nbsp;<input type="button" value=" Deseleziona tutti " onclick="unselAll('inOutlet[]');" />&nbsp;<input type="button" value=" Aggiorna Outlet " onclick="$('f_editor').action='main.php?method=up';if(yav.performCheck('editor', rules, 'inline')) $('f_editor').submit();"/>
<br /><br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{literal}
<script type="text/javascript">
var dpck_docvenDate = new DatePicker({
        relative    : 'docvenDate',
        language    : 'it', 
        disablePastDate : false,
        disableFutureDate : true, 
        afterClose : yav.performCheck('editor', rules, 'inline')        
});
dpck_docvenDate.setDateFormat([ "dd", "mm", "yyyy" ], "/");
</script>
{/literal}
{include file="std.footer.tpl" }

