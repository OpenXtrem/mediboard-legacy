<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getModuleClass('dPhospi', 'lit'));
require_once($AppUI->getModuleClass('dPhospi', 'service'));

/**
 * Classe CChambre. 
 * @abstract G�re les chambre d'hospitalisation
 * - contient des lits
 */
class CChambre extends CDpObject {
  // DB Table key
	var $chambre_id = null;	
  
  // DB References
  var $service_id = null;

  // DB Fields
  var $nom = null;
  var $caracteristiques = null; // c�t� rue, fen�tre, lit accompagnant, ...

  // Form Fields
  var $_nb_lits_dispo = null;
  var $_overbooking = null;

  // Object references
  var $_ref_service = null;
  var $_ref_lits = null;

	function CChambre() {
		$this->CDpObject('chambre', 'chambre_id');
	}

  function loadRefsFwd() {
    $this->_ref_service = new CService;
    $this->_ref_service->load($this->service_id);
  }

  function loadRefsBack() {
    $where = array (
      "chambre_id" => "= '$this->chambre_id'"
    );
    
    $this->_ref_lits = new CLit;
    $this->_ref_lits = $this->_ref_lits->loadList($where);
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'Lits', 
      'name' => 'lit', 
      'idfield' => 'lit_id', 
      'joinfield' => 'chambre_id'
    );
        
    return CDpObject::canDelete($msg, $oid, $tables);
  }
  
  function checkChambre() {
    assert($this->_ref_lits !== null);
    $this->_nb_lits_dispo = count($this->_ref_lits);
    
    foreach ($this->_ref_lits as $lit) {
      assert($lit->_overbooking !== null);
      $this->_overbooking += $lit->_overbooking;

		  assert($lit->_ref_affectations !== null);
      if (count($lit->_ref_affectations)) {
				$this->_nb_lits_dispo--;
			}
		}
  }
}
?>