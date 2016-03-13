{include file="std.header.tpl" }

</head>
<body onload="">

{include file="std.frameup.tpl" }

{literal}
<script type="text/javascript">
sectionOpened = null;

function sectionOpen(section)
{
    
    if(sectionOpened == section) {
        
        sectionClose(sectionOpened);
        return false;
    
    }
    
    if(sectionOpened != null) {
        
        sectionClose(sectionOpened);
        
    }
    
    new Effect.SlideDown(section, { duration : 0.5});
    sectionOpened = section;
}


function sectionClose(section)
{
    new Effect.SlideUp(section, { duration : 0.5});
    sectionOpened = null;
}
</script>
{/literal}
<div id="content">
<h2>Menu Principale</h2>

<p>
<table width="50%" style="float:left;">
<tr>
    <td valign="top" width="50%">
        <a href="#" onclick="sectionOpen('section_articoli'); return false;" class="menulink menulinkbg JSRoundCorners">Articoli</a>
        <div id="section_articoli"  style="display:none;width:auto;height:auto;background-color:#ffffe0;font-size:0.9em;">
            <div>
                <ul>
                    <li><a href="../mgart_search/main.php" class="menulink">Archivio Articoli :: Consultazione Articoli</a></li>
                </ul>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td valign="top">
        <a href="#" onclick="sectionOpen('section_alternativi'); return false;" class="menulink menulinkbg JSRoundCorners">Codici Alternativi</a>
        <div id="section_alternativi"  style="display:none;width:auto;height:auto;background-color:#ffffe0;font-size:0.9em;">
            <div>
                <ul>
                    <li><a href="../mgcaa_checkEZM/main.php" class="menulink">Codici Alternativi :: Ricerca Incongruenze</a></li>
                    <li><a href="../mgcaa_bcodePrint/main.php?function=db" class="menulink">Codici Alternativi :: Stampa Bar Code</a></li>
                </ul>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td>
        <a href="#" onclick="sectionOpen('section_outlet'); return false;" class="menulink menulinkbg JSRoundCorners">OUTLET</a>
        <div id="section_outlet" style="display:none;width:auto;height:auto;background-color:#ffffe0;font-size:0.9em;">
            <div>
                <ul>
                    <li><a href="../mgart_outletInsert/main.php" class="menulink">Inserimento Articoli</a></li>
                    <li><a href="../mgart_outletRemove/main.php" class="menulink">Rimozione Articoli</a></li>
                    <li><a href="../mgart_outletPromo/main.php" class="menulink">Carica Prezzi</a></li>
                    <li><a href="../mgcaa_bcodePrint/main.php?function=mysql_outlet" class="menulink">Stampa Etichette</a></li>
                    <li><a href="../mgart_outletStatVend/main.php" class="menulink">Statistiche Vendita</a></li>
                </ul>
            </div>
        </div>
    
    </td>
</tr>
<tr>
    <td>
        <a href="#" onclick="sectionOpen('section_scf'); return false;" class="menulink menulinkbg JSRoundCorners">SCAFFALI&nbsp;<font color="red" style="font-size:0.7em;"><b>beta</b></font></a>
        <div id="section_scf" style="display:none;width:auto;height:auto;background-color:#ffffe0;font-size:0.9em;">
            <div>
                <ul>
                    <li><a href="../mgart_scfInsert/main.php" class="menulink">Inserimento Articoli</a></li>
                </ul>
            </div>
        </div>
    
    </td>
</tr>
</table>

<!-- COLONNA N.2 -->
<table width="50%" style="float:right;">
<tr>
    <td valign="top" width="100%">
    	<a href="#" onclick="sectionOpen('section_sistema'); return false;" class="menulink menulinkbg JSRoundCorners">Gestione Sistema</a>
        <div id="section_sistema"  style="display:none;width:auto;height:auto;background-color:#ffffe0;font-size:0.9em;">
            <div>
                <ul>
                    <li><a href="../mgart_createFulltext/main.php" class="menulink">Generazione archivio ricerche fulltext</a></li>
                </ul>
            </div>
        </div>
	</td>    	
</tr>
</table>
<!-- COLONNE N.2 END -->
</p>

</div>

{literal}
<script type="text/javascript">

function roundCorners()
{
	$$('a.JSRoundCorners').each(function(id) { new Effect.Corner(id); });
}
var a = document.body.getAttribute('onload');
document.body.setAttribute('onload', a + 'roundCorners();');
</script>
{/literal}

{include file="std.footer.tpl" }

