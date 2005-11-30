<?php /* STYLE/DEFAULT $Id$ */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $AppUI->cfg['company_name'];?> :: Mediboard Login</title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
	<meta http-equiv="Pragma" content="no-cache" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Sant�" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
  <?php mbLinkShortcutIcon("style/$uistyle/images/favicon.ico"); ?>
  <?php mbLinkStyleSheet("style/$uistyle/main.css"); ?>
  <?php mbLoadScript("includes/javascript/gosu/array.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/cookie.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/debug.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/ie5.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/keyboard.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/string.js"); ?>
  <?php mbLoadScript("includes/javascript/gosu/validate.js"); ?>
  <?php mbLoadScript("includes/javascript/functions.js"); ?>
  <?php mbLoadScript("includes/javascript/cjl_cookie.js"); ?>
  <?php mbLoadScript("includes/javascript/url.js"); ?>
  <?php mbLoadScript("includes/javascript/forms.js"); ?>
  <?php mbLoadScript("includes/javascript/printf.js"); ?>
  <?php mbLoadScript("lib/jscalendar/calendar.js"); ?>
  <?php mbLoadScript("lib/jscalendar/lang/calendar-fr.js"); ?>
  <?php mbLoadScript("lib/jscalendar/calendar-setup.js"); ?>
</head>

<body onload="main()">
<div id="login">
  <form name="loginFrm" action="./index.php" method="post" onsubmit="return checkForm(this)">
  
	<input type="hidden" name="login" value="<?php echo time();?>" />
	<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
	<table class="form">
		<tr>
      <th class="category" colspan="3"><?php echo $AppUI->cfg['company_name'];?></th>
    </tr>

		<tr>
      <td class="logo" colspan="3 ">
        <a href="http://www.mediboard.org/">
          <img src="./style/mediboard/images/mbNormal.gif" alt="MediBoard logo" />
        </a>
        <p>
          Plateforme Open Source pour les Etablissements de Sant�<br/>
          Version <?php echo "$mb_version_major.$mb_version_minor.$mb_version_patch"; ?>
        </p>
      </td>
    </tr>

    <tr>
      <th class="category" colspan="2">Connexion</th>
<?php if ($dPconfig['demo_version']) { ?>
      <th class="category">Comptes disponibles</th>
<?php } ?>
    </tr>

    <tr>
      <th><label for="username" title="Nom de compte utilisateur. Obligatoire"><?php echo $AppUI->_('Username'); ?>:</label></th>
      <td><input type="text" name="username" title="notNull|str" size="25" maxlength="20" /></td>
<?php if ($dPconfig['demo_version']) { ?>
      <td rowspan="3" class="category">
        <strong>Administrateur</strong>: admin/admin<br />
        <strong>Chirurgien</strong>: chir/chir<br />
        <strong>PMSI</strong>: pmsi/pmsi<br />
        <strong>Surveillante de bloc</strong>: survbloc/survbloc<br />
        <strong>Hospitalisation</strong>: hospi/hospi
      </td>
<?php } ?>
    </tr>

    <tr>
      <th><label for="password" title="Mot de passe utilisateur. Obligatoire"><?php echo $AppUI->_('Password'); ?>:</label></th>
      <td><input type="password" name="password" title="notNull|str" size="25" maxlength="32" /></td>
    </tr>
    
    <tr>
      <td colspan="2" class="button"><input type="submit" value="<?php echo $AppUI->_('login'); ?>" /></td>
    </tr>

    <tr>
      <th class="category">Bas� sur</th>
<?php if ($dPconfig['demo_version']) { ?>
      <th class="category">H�berg� chez</th>
<?php } ?>
      <th class="category">Propuls� par</th>
    </tr>

    <tr>
      <td class="logo">
        <a href="http://www.dotproject.net/">
          <img src="./style/mediboard/images/dp_icon.gif" alt="dotProject logo" />
        </a>
        <p>Version <?php echo @$AppUI->getVersion(); ?></p>
      </td>

<?php if ($dPconfig['demo_version']) { ?>
      <td class="logo">
        <a href="http://www.sourceforge.net/projects/mediboard/" title="Projet Mediboard sur Sourceforge">
          <img src="http://www.sourceforge.net/sflogo.php?group_id=112072&amp;type=2" alt="Sourceforge Project Logo" />
        </a>
        <p>H�bergement du code source</p>
      </td>
<?php } ?>

      <td class="logo">
        <a href="http://www.mozilla-europe.org/fr/products/firefox/" title="T�l�charger Firefox">
          <img src="http://www.spreadfirefox.com/community/images/affiliates/Buttons/80x15/firefox_80x15.png" alt="Firefox Logo" />
        </a>
        <p>Pour un meilleur confort et plus de s�curit�, nous recommandons d'utiliser le navigateur Firefox</p>
      </td>

    </tr>

	</table>
  
  </form>
</div>
  
<?php

if ($errorMsg = $AppUI->getMsg())
    echo "<div class='error'>Error: $errorMsg</div>";

if (phpversion() < "4.1")
	echo "<div class='warning'>Warning: dotproject is NOT SUPPORT for this PHP Version ($phpVersion)</div>";

if (!function_exists("mysql_pconnect"))
	echo "<div class='warning'>Warning: PHP may not be compiled with MySQL support.  This will prevent proper operation of dotProject.  Please check you system setup.</div>";

?>

</body>
</html>
