<?php /* ADMIN  $Id$ */ ?>
<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tbl">
<tr>
	<td width="60" align="right">
		&nbsp; <?php echo $AppUI->_('sort by');?>:&nbsp;
	</td>
	<th width="150">
		<a href="?m=admin&amp;a=index&amp;orderby=user_username" class="hdr"><?php echo $AppUI->_('Login Name');?></a>
	</th>
	<th>
		<a href="?m=admin&amp;a=index&amp;orderby=user_last_name" class="hdr"><?php echo $AppUI->_('Real Name');?></a>
	</th>
</tr>
<?php 
foreach ($users as $row) {
?>
<tr>
	<td align="right" nowrap="nowrap">
<?php if ($canEdit) { ?>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<a href="./index.php?m=admin&amp;a=addedituser&amp;user_id=<?php echo $row["user_id"];?>" title="<?php echo $AppUI->_('edit');?>">
					<?php echo dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' ); ?>
				</a>
			</td>
			<td>
				<a href="?m=admin&amp;a=viewuser&amp;user_id=<?php echo $row["user_id"];?>&amp;tab=1" title="">
					<img src="images/obj/lock.gif" width="16" height="16" border="0" alt="<?php echo $AppUI->_('edit permissions');?>">
				</a>
			</td>
			<td>
				<a href="javascript:delMe(<?php echo $row["user_id"];?>, '<?php echo $row["user_first_name"] . " " . $row["user_last_name"];?>')" title="<?php echo $AppUI->_('delete');?>">
					<?php echo dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' ); ?>
				</a>
			</td>
		</tr>
		</table>
<?php } ?>
	</td>
	<td>
		<a href="./index.php?m=admin&amp;a=viewuser&amp;user_id=<?php echo $row["user_id"];?>"><?php echo $row["user_username"];?></a>
	</td>
	<td>
		<a href="mailto:<?php echo $row["user_email"];?>"><img style="float: left; margin: 0 2px" src="images/obj/email.gif" width="16" height="16" border="0" alt="email"></a>
		<?php echo $row["user_last_name"].', '.$row["user_first_name"];?>
	</td>
</tr>
<?php }?>

</table>
