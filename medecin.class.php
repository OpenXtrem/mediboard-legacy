<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('dp' ) );

require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

/**
 * The CMedecin Class
 */
class CMedecin extends CDpObject {
  // DB Table key
	var $medecin_id = null;

  // DB Fields
	var $nom = null;
	var $prenom = null;
	var $adresse = null;
	var $ville = null;
	var $cp = null;
	var $tel = null;
	var $fax = null;
	var $email = null;

  // Form fields
	var $_tel1 = null;
	var $_tel2 = null;
	var $_tel3 = null;
	var $_tel4 = null;
	var $_tel5 = null;
	var $_fax1 = null;
	var $_fax2 = null;
	var $_fax3 = null;
	var $_fax4 = null;
	var $_fax5 = null;

  // Object References
  var $_ref_patients = null;

	function CMedecin() {
		$this->CDpObject( 'medecin', 'medecin_id' );
	}
  
  function load($oid = null, $strip = true) {
    if (!parent::load($oid, $strip)) {
      return false;
    }

    // Form fields computation

    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);

    $this->_fax1 = substr($this->fax, 0, 2);
    $this->_fax2 = substr($this->fax, 2, 2);
    $this->_fax3 = substr($this->fax, 4, 2);
    $this->_fax4 = substr($this->fax, 6, 2);
    $this->_fax5 = substr($this->fax, 8, 2);

    return true;
  }
  
  function store() {
    // Form fields computation
    $this->tel = 
      $this->_tel1 .
      $this->_tel2 .
      $this->_tel3 .
      $this->_tel4 .
      $this->_tel5;

    $this->fax = 
      $this->_fax1 .
      $this->_fax2 .
      $this->_fax3 .
      $this->_fax4 .
      $this->_fax5;
      
    return parent::store();
  }

	function check() {
    // Data checking
    $msg = null;

    if (!strlen($this->nom)) {
      $msg .= "Nom invalide: '$this->nom'<br />";
    }

    if (!strlen($this->prenom)) {
      $msg .= "Nom invalide: '$this->prenom'<br />";
    }
        
    return $msg . parent::check();
	}
	
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'patient(s)', 
      'name' => 'patients', 
      'idfield' => 'patient_id', 
      'joinfield' => 'medecin_traitant'
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  function loadRefs() {
    // Backward references
    $obj = new CPatient();
    $this->_ref_patients = $obj->loadList("medecin_traitant = '$this->medecin_id'");
  }

  function getSiblings() {
    $sql = "SELECT medecin_id, nom, prenom, adresse, ville, CP " .
      		"FROM medecin WHERE " .
      		"medecii_id != '$this->medecin_id' " .
      		"AND nom = '$this->nom' AND prenom = '$this->prenom'";
    $siblings = db_loadlist($sql);
    return $siblings;
  }
}
?>