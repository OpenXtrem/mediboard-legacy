<?php

$canRead = !getDenyRead( $m );
$canEdit = !getDenyEdit( $m );

if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

//save the workplace state (have a footprint on this site)
$AppUI->savePlace();

// retrieve any state parameters (temporary session variables that are not stored in db)

if (isset( $_GET['tab'] )) {
	// saves the current tab box state
	$AppUI->setState( "dPccamIdxTab", $_GET['tab'] );
}
$tab = $AppUI->getState( "dPccamIdxTab" ) !== NULL ? $AppUI->getState( "dPccamIdxTab" ) : 0;

// we prepare the User Interface Design with the dPFramework

// setup the title block with Name, Icon and Help
$titleBlock = new CTitleBlock( "dPccam", "dPccam.png", $m, "$m.$a" );
$titleBlock->show();

// now prepare and show the tabbed information boxes with the dPFramework

// build new tab box object
$tabBox = new CTabBox( "?m=$m", "{$AppUI->cfg['root_dir']}/modules/$m/", $tab );
$tabBox->add( "vw_idx_favoris", "Mes favoris" );
$tabBox->add( "vw_find_code"  , "Rechercher un code" );
$tabBox->add( "vw_full_code"  , "Affichage d'un code" );
$tabBox->show();

// this is the whole main site!
// all further development now has to be done in the files addedit.php, vw_idx_about.php, vw_idx_quotes.php
// and in the subroutine do_quote_aed.php
?>