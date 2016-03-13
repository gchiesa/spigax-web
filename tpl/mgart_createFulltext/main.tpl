{include file="std.header.tpl" }

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Utilità di sistema - Crea archivio ricerche fulltext</h2>

<p>
<form action="main.php" method="POST">
<input type="hidden" name="job" value="1" />
<input type="submit" value=" Crea Archivio Fulltext " onclick="document.location='main.php?job=1';" />
&nbsp;&nbsp;
<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</p>

</div>

{include file="std.footer.tpl" }

