<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
  <head>
  <title>CCAM Desk</title>
  <link rel="stylesheet" media="screen" href="modules/[var.m]/css/ccam.css"/>
  </head>
  <body>
  
  <table width="100%" border=1 cellspacing=0 cellpadding=2>
    <tr>
	  <td width="100%" bgcolor="#ff9999" colspan=7>
	    <table width="100%">
          <tr>
	        <td valign="top" width="50%" bgcolor="#ffd5d5">
	          <table width="100%">
		        <tr>
			      <form action="main.php" target="_self" name="selection" method="get" encoding="">
		          <input type="hidden" name="menu" value="acte">
		          <td colspan=2 valign="top" align="center">
			        <b>Code de l'acte :</b>
					<input type="text" name="codeacte" value="[var.codeacte]">
					<input type="submit" value="afficher">
			      </td>
				  </form>
		        </tr>
				<tr>
				  <td colspan=2 valign="top" align="center">
				    <a href="sql.php?type=addfav&menu=acte&codeacte=[var.codeacte]">Ajouter � Mes Actes</a>
					[tbs_check.1;block=tr;if [var.islog]=1]
				  </td>
				</tr>
		        <tr>
		          <td colspan=2 valign="top">
			        <b>Description</b><br>
			        [var.libelle]
			      </td>
		        </tr>
		        <tr>
		          <td colspan=2 valign="top">
			        <i>[rq.val;block=tr]</i>
			      </td>
		        </tr>
		        <tr>
		          <td colspan=2 valign="top">
			        <b>Activit�s associ�es</b>
			      </td>
		        </tr>
		        <tr>
		          <td valign="top">
			        <b>[act.code;block=tr] :</b>
			      </td>
			      <td valign="top" width="100%">
			        [act.nom;block=tr]
			        <li>
				      [act.phases;block=tr] phase(s)
				    </li>
				    <li>
				      modificateurs : [act.modificateurs;block=tr]
				    </li>
			      </td>
		        </tr>
		        <tr>
			      <td colspan=2 valign="top">
			        <b>Procedure associ�e :</b>
			      </td>
		        </tr>
		        <tr>
		          <td valign="top">
			        <a href="main.php?menu=acte&codeacte=[var.codeproc]">[var.codeproc]</a>
			      </td>
			      <td valign="top">
			        [var.textproc]
			      </td>
		        </tr>
		      </table>
	        </td>
	        <td valign="top" width="50%" bgcolor="#ffd5d5">
	          <table width="100%">
		        <tr>
		          <td colspan=2 valign="top" align="center">
			         <b>Place dans la CCAM : [var.place]</b>
			      </td>
		        </tr>
		        <tr>
		          <td valign="top" align="right">
			        <b>[chap.rang;block=tr]</b>
			      </td>
			      <td valign="top">
			        [chap.nom]
			        <!--
			        <br>
			        <i>[chap.rq]</i>
			        -->
			      </td>
		        </tr>
		      </table>
	        </td>
	      </tr>
	      <tr>
	        <td valign="top" width="50%" bgcolor="#ffd5d5">
	          <table width="100%">
		        <tr>
		          <td colspan=2 valign="top" align="center">
			        <b>Actes associ�s ([asso.#])</b>
			      </td>
		        </tr>
		        <tr>
		          <td valign="top">
			        <b><a href="main.php?menu=acte&codeacte=[asso.code;block=tr]">[asso.code;block=tr]</a></b>
			      </td>
			      <td valign="top">
			        [asso.texte]
			      </td>
		        </tr>
		      </table>
	        </td>
	        <td valign="top" width="50%" bgcolor="#ffd5d5">
	          <table width="100%">
		        <tr>
		          <td colspan=2 valign="top" align="center">
			        <b>Actes incompatibles ([incomp.#])</b>
			      </td>
		        </tr>
		        <tr>
		          <td valign="top">
			        <b><a href="main.php?menu=acte&codeacte=[incomp.code;block=tr]">[incomp.code;block=tr]</a></b>
			      </td>
			      <td valign="top">
			        [incomp.texte;block=tr]
			      </td>
		        </tr>
		      </table>
	        </td>
	      </tr>
        </table>
	  </td>
	</tr>
  </table>

  </body>
</html>