{literal}
<script type="text/javascript">
//
// Per ogni div.div_separator arrotondo i bordi 
//
function roundDivs()
{
	$$('div.div_separator').each(function(id) { new Effect.Corner(id); });
}
var a = document.body.getAttribute('onload');
document.body.setAttribute('onload', a + 'roundDivs();');
</script>
{/literal}

</body>
</html>
