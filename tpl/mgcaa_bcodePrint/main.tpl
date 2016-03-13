{include file="std.header.tpl" }

{literal}
<script type="text/javascript">
var rules = new Array();
rules[0] = 'test|required';

</script>
{/literal}

</head>
<body onload="yav.init('editor', rules);">

{include file="std.frameup.tpl" }


<div id="content">
<h2>Stampa Bar Code</h2>

{if isset($errors)}
<div class="errordiv">
<ul>
{foreach item=row from=$errors}
<li>{$row}</li>
{/foreach}
</ul>
</div>
{/if}
<div class="div_separator">&nbsp;Seleziona il tipo di stampa</div>
<form id="f_editor" name="editor" method="GET" action="main.php">
<tt>Sorgente dati...:</tt>&nbsp;<select name="function">{html_options options=$functions selected=$defFunction}</select><span class="innerHelp">( seleziona la sorgente dati da cui prelevare le etichette)</span><br />
<br />
<input type="submit" value=" Seleziona " />
<br /><br />
<input type="button" value=" Esci " onclick="document.location='{$RELINSTDIR}';" />
</form>
</div>

{include file="std.footer.tpl" }

