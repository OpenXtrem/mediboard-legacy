<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('dp' ) );

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPpatients', 'medecin') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

/**
 * The CPatient Class
 */
class CPatient extends CDpObject {
  // DB Table key
	var $patient_id = null;

  // DB Fields
	var $nom = null;
	var $prenom = null;
	var $naissance = null;
	var $sexe = null;
	var $adresse = null;
	var $ville = null;
	var $cp = null;
	var $tel = null;
	var $tel2 = null;
	var $medecin_traitant = null;
	var $medecin1 = null;
	var $medecin2 = null;
	var $medecin3 = null;
	var $incapable_majeur = null;
	var $ATNC = null;
	var $matricule = null;
	var $SHS = null;
	var $rques = null;

  // Form fields
	var $_jour = null;
	var $_mois = null;
	var $_annee = null;
	var $_tel1 = null;
	var $_tel2 = null;
	var $_tel3 = null;
	var $_tel4 = null;
	var $_tel5 = null;
	var $_tel21 = null;
	var $_tel22 = null;
	var $_tel23 = null;
	var $_tel24 = null;
	var $_tel25 = null;
	var $_age = null;

  // Object References
  var $_ref_operations = null;
  var $_ref_consultations = null;
  var $_ref_medecin_traitant = null;
  var $_ref_medecin1 = null;
  var $_ref_medecin2 = null;
  var $_ref_medecin3 = null;

	function CPatient() {
		$this->CDpObject( 'patients', 'patient_id' );
	}
  
  function updateFormFields() {
    $this->_jour  = substr($this->naissance, 8, 2);
    $this->_mois  = substr($this->naissance, 5, 2);
    $this->_annee = substr($this->naissance, 0, 4);

    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);
    $this->_tel21 = substr($this->tel2, 0, 2);
    $this->_tel22 = substr($this->tel2, 2, 2);
    $this->_tel23 = substr($this->tel2, 4, 2);
    $this->_tel24 = substr($this->tel2, 6, 2);
    $this->_tel25 = substr($this->tel2, 8, 2);

    $annais = substr($this->naissance, 0, 4);
    $anjour = date("Y");
    $moisnais = substr($this->naissance, 5, 2);
    $moisjour = date("m");
    $journais = substr($this->naissance, 8, 2);
    $jourjour = date("d");
    $this->_age = $anjour-$annais;
    if($moisjour<$moisnais){$this->_age=$this->_age-1;}
    if($jourjour<$journais && $moisjour==$moisnais){$this->_age=$this->_age-1;}
  }
  
  function updateDBFields() {
    $this->tel = 
      $this->_tel1 .
      $this->_tel2 .
      $this->_tel3 .
      $this->_tel4 .
      $this->_tel5;
    $this->tel2 = 
      $this->_tel21 .
      $this->_tel22 .
      $this->_tel23 .
      $this->_tel24 .
      $this->_tel25;

    $this->naissance = 
      $this->_annee . "-" .
      $this->_mois  . "-" .
      $this->_jour;
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
    
    if ($this->matricule && !ereg ("([1-2])([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{2})", $this->matricule)) {
      $msg .= "Matricule invalide: '$this->matricule'<br />";
    }
        
    return $msg . parent::check();
	}
	
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'opération(s)', 
      'name' => 'operations', 
      'idfield' => 'operation_id', 
      'joinfield' => 'pat_id'
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  function loadRefs() {
    // Backward references
    // opérations
    $obj = new COperation();
    $this->_ref_operations = $obj->loadList("pat_id = '$this->patient_id'");
    // consultations
    $obj = new CConsultation();
    $this->_ref_consultations = $obj->loadList("patient_id = '$this->patient_id'");
    // Forward references
    // medecin_traitant
    $obj = new CMedecin();
    if($obj->load($this->medecin_traitant))
      $this->_ref_medecin_traitant = $obj;
    // medecin1
    $obj = new CMedecin();
    if($obj->load($this->medecin1))
      $this->_ref_medecin1 = $obj;
    // medecin2
    $obj = new CMedecin();
    if($obj->load($this->medecin2))
      $this->_ref_medecin2 = $obj;
    // medecin3
    $obj = new CMedecin();
    if($obj->load($this->medecin3))
      $this->_ref_medecin3 = $obj;
  }

  function getSiblings() {
    $sql = "SELECT patient_id, nom, prenom, naissance, adresse, ville, CP " .
      		"FROM patients WHERE " .
      		"patient_id != '$this->patient_id' " .
      		"AND ((nom    = '$this->nom'    AND prenom    = '$this->prenom'   ) " .
      		  "OR (nom    = '$this->nom'    AND naissance = '$this->naissance') " .
      		  "OR (prenom = '$this->prenom' AND naissance = '$this->naissance'))";
    $siblings = db_loadlist($sql);
    return $siblings;
  }
}
?>