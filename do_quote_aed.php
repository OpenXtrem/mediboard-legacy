<?php
// this doSQL script is called from the addedit.php script
// its purpose is to use the CdPccam class to interoperate with the database (store, edit, delete)

/* the following variables can be retreived via POST from dPccam/addedit.php:
** int dPccam_id	is '0' if a new database object has to be stored or the id of an existing quote that should be overwritten or deleted in the db
** str dPccam_quote	the text of the quote that should be stored
** int del		bool flag, in case of presence the row with the given dPccam_id has to be dropped from db
*/

// create a new instance of the dPccam class
$obj = new CdPccam();
$msg = '';	// reset the message string

// bind the informations (variables) retrieved via post to the dPccam object
if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// detect if a deleete operation has to be processed
$del = dPgetParam( $_POST, 'del', 0 );


if ($del) {
	// check if there are dependencies on this object (not relevant for dPccam, left here for show-purposes)
	if (!$obj->canDelete( $msg )) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}

	// see how easy it is to run database commands with the object oriented architecture !
	// simply delete a quote from db and have detailed error or success report
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );			// message with error flag
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "Quote deleted", UI_MSG_ALERT);		// message with success flag
		$AppUI->redirect( "m=dPccam" );
	}
} else {
	// simply store the added/edited quote in database via the store method of the dPccam child class of the CDpObject provided ba the dPFramework
	// no sql command is necessary here! :-)
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['dPccam_id'];
		$AppUI->setMsg( $isNotNew ? 'Quote updated' : 'Quote inserted', UI_MSG_OK);
	}
	$AppUI->redirect();
}
?>