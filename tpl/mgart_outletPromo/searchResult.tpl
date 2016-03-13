{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
// data fine validità p10
var rules = new Array(); 
rules[0] = 'dataVal|date|Data inserita non valida';

yav.addHelp('dataVal', 'Inserire la data finale di validità del P10');




function applySc(tableId, cellFrom, cellTarget, perc)
{
    var k = 0;
    var rows = $(tableId).tBodies[0].rows.length;
 
    for(k = 0; k < rows; k++) {
        
        src = $(tableId).tBodies[0].rows[k].cells[cellFrom].innerHTML.stripTags().gsub('&euro;', ' ').gsub('€', ' ').strip();
        sc = (perc==0)?0:(Math.round(src * perc) / 100);
        dst = Math.round( (src - sc) * 100) / 100; 
        $(tableId).tBodies[0].rows[k].cells[cellTarget].down().value = dst;
        
    }
}




function roundAt9(tableId, cellN)
{
    var k = 0;
    var rows = $(tableId).tBodies[0].rows.length;
    
    for(k = 0; k < rows; k++) {
        
        var old = $(tableId).tBodies[0].rows[k].cells[cellN].down().value;
        old = Math.round(old * 100);
        
        if(old % 10 == 9 || old % 10 == 0) {
            
            continue;
            
        }
        
        var unit = old % 10;
        old = old + ( 9 - unit);
        $(tableId).tBodies[0].rows[k].cells[cellN].down().value = (old / 100); 
        
    }
    
}




</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>OUTLET - Revisione P10</h2>

<form id="f_editor" name="editor" method="POST" action="">
<input type="hidden" name="job" value="1" />
<tt>Applica sconto perc...:</tt>&nbsp;<select name="perc" onchange="$('f_editor').round99.checked = false; applySc('datatable_0', 3, 4, this.value);" >{html_options options=$select_checkType selected=$checkType}</select><span id="errorsDiv_perc"></span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Arrotonda a 99 cent...:</a>&nbsp;<input type="checkbox" name="round99" onchange="if(this.checked) roundAt9('datatable_0', 4);"/><br />
<tt>Data di validità P10..:</tt>&nbsp;<input type="text" id="dataVal" name="dataVal" value="{$lastOfYear}" /><span id="errorsDiv_dataVal"></span><br />
<br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" /><br />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px" row_onclick=""}
    {column id="codArt" align="left" name="Codice Articolo" }
    {column id="descriz" align="left" name="Descrizione" }
    {column id="esist"  align="center" name="Esistenza" }
    {column id="pVen" align="right" output_printf="€ %8.2f" name="Prezzo P1"}
    {column id="pPromo" align="right" output_printf="€ %8.2f" name="Prezzo P10"}
    {column id="pPromoDval" align="center" name="Validità"}
    {column id="EAN" align="left" name="Codice EAN" }
{/datatable}
<input type="button" value=" Aggiorna Prezzi P10 " onclick="$('f_editor').action='main.php?method=up';if(yav.performCheck('editor', rules, 'inline')) $('f_editor').submit();"/>
<br /><br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{literal}
<script type="text/javascript">
var dpck_dataVal = new DatePicker({
        relative    : 'dataVal',
        language    : 'it', 
        disablePastDate : true,
        disableFutureDate : false,
        afterClose : yav.performCheck('editor', rules, 'inline')        
});
dpck_dataVal.setDateFormat([ "dd", "mm", "yyyy" ], "/");

convertCellToInputText('datatable_0', 4, 'P10', 0, 'size="10" maxlength="10" style="text-align:right;"');
</script>
{/literal}
{include file="std.footer.tpl" }

