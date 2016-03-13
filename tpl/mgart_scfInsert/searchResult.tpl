{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();

rules[0] = 'scfMt|required';

yav.addHelp('scfNewDesc', 'Inserire il nome del nuovo scaffale');
yav.addHelp('scfSector', 'Selezionare lo scaffale dalla lista o inserirne uno nuovo a lato');
yav.addHelp('scfMt', 'Selezionare il metro/testata/griglia dalla lista');

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

function checkCodArtsExists()
{
	var t = $('datatable_0').tBodies[0];
	
	for(k=0; k<t.rows.length; k++) {
	
		checkbox = t.rows[k].cells[0].down().checked;
		codart = t.rows[k].cells[1].innerHTML;
		
		if(codart.empty() && checkbox) {
		
			alert('Impossibile aggiornare poichè esistono articoli selezionati ma non riconosciuti');
			return false;
			
		}
	}
	
	return true;
}	
</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Gestione Scaffali - Inserimento Articoli - Selezione</h2>

<form id="f_editor" name="editor" method="POST" action="main.php?">
<input type="hidden" name="job" value="1" />
<tt>Scaffale........:</tt><select name="scfSector">{html_options options=$selectScfSector}</select>&nbsp;<tt>o inserisci nuovo:</tt>&nbsp;<input type="text" name="scfNewDesc" value="" /><span id="errorsDiv_scfSector"></span><span id="errorsDiv_scfNewDesc"></span><br />
<tt>Metro...........:</tt><select name="scfMt">{html_options options=$selectScfMt}</select>&nbsp;<span id="errorsDiv_scfMt"></span><br />
<br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Elimina vecchio metro....:</a><input type="checkbox" name="scfEraseBefore" /><span class="innerHelp">(elimina gli articoli presenti nel metro di scaffale prima di caricare i nuovi)</span><br />
<a onclick="linkToCheckbox(this, 'NEXT');" class="checkbox_link">Sposta articoli presenti.:</a><input type="checkbox" name="scfUnlinkExists" /><span class="innerHelp">(se un codice articolo esiste su un altro scaffale viene eliminato e risulterà solo nel presente scaffale )</span><br />
<br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" /><br />
{datatable data=$datatable sortable=0 cycle=1 mouseover=1 width="100%" height="300px" row_onclick="changeSel(this);"}
    {column id="codArt" align="center" checkboxes="inScf" name="Inserisci "}
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
<input type="button" value=" Seleziona tutti " onclick="selAll('inScf[]');" />&nbsp;<input type="button" value=" Deseleziona tutti " onclick="unselAll('inScf[]');" />&nbsp;<input type="button" value=" Aggiorna Scaffale " onclick="$('f_editor').action += '&method=up';if(checkCodArtsExists() && yav.performCheck('editor', rules, 'inline')) $('f_editor').submit();"/>
<br /><br />
<input type="button" value=" Ritorna Indietro " onclick="document.location='main.php';" />&nbsp;<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

