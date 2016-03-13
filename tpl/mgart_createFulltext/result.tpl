{include file="std.header.tpl" }

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Utilità di sistema - Crea archivio ricerche fulltext</h2>

<p>

{if isset($errors)}
<div class="errordiv">
{foreach item=row from=$errors}
- {$row}
{/foreach}
</div>
{/if}

<tt>
Associazioni create..: {$rows}<br />
<br />
Time Fulltext....: {$timeElapsedFulltext} secondi<br />
</tt>
</p>
<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />

</div>

{include file="std.footer.tpl" }

