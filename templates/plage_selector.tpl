<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
<td valign="top" align="left" width="98%">
{literal}
<script language="javascript">
	function setClose(){
		var list = document.frmSelector.list;
		var key = list.options[list.selectedIndex].value;
		var val = list.options[list.selectedIndex].text;
		window.opener.setPlage(key,val);
		window.close();
	}
</script>
{/literal}

<table cellspacing="0" cellpadding="3" border="0">
<form action="index.php" target="_self" name="frmSelector" method="get" encoding="">
<input type="hidden" name="m" value="dPplanningOp">
<input type="hidden" name="a" value="plage_selector">
<input type="hidden" name="dialog" value="1">
<tr>
	<th colspan=2>
		<a href="index.php?m=dPplanningOp&a=plage_selector&dialog=1&hour={$hour}&min={$min}&chir={$chir}&month={$pmonth}&year={$pyear}"><<</a>
		{$month} / {$year}
		<a href="index.php?m=dPplanningOp&a=plage_selector&dialog=1&hour={$hour}&min={$min}&chir={$chir}&month={$nmonth}&year={$nyear}">>></a>
	</th>
</tr>
<tr>
	<td colspan="2">
<select name="list"  size="14">
	{foreach from=$list item=curr_elem}
	<option value="{$curr_elem.id}">{$curr_elem.dateFormed}</option>
	{/foreach}
</select>
	</td>
</tr>
<tr>
	<td>
		<input type="button" class="button" value="annuler" onclick="window.close()" />
	</td>
	<td align="right">
		<input type="button" class="button" value="selectionner" onclick="setClose()" />
	</td>
</tr>
</form>
</table>

	</td>
</tr>
</table>
</body>
</html>