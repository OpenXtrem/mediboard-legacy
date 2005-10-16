<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

set_time_limit( 1800 );

mbInsertCSV("modules/dPinterop/PLAGE CS.TXT", "dermato_import_consultations1");

?>