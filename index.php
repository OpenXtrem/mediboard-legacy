<?php 

$canRead = !getDenyRead( $m );
$canEdit = !getDenyEdit( $m );

if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$AppUI->savePlace();

if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'dPplanningOpIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'dPplanningOpIdxTab' ) !== NULL ? $AppUI->getState( 'dPplanningOpIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'dPplanningOpIdxTab' ) );

$titleBlock = new CTitleBlock( 'dPplanningOp', 'dPplanningOp.png', $m, "$m.$a" );
$titleBlock->addCell();
$titleBlock->show();

$tabBox = new CTabBox( "?m=dPplanningOp", "{$AppUI->cfg['root_dir']}/modules/dPplanningOp/", $tab );
$tabBox->add( 'vw_idx_planning', 'Consulter le planning' );
$tabBox->show();

?>