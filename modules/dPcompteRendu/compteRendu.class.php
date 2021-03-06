<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass('mbobject' ));

require_once($AppUI->getModuleClass('mediusers'));
require_once($AppUI->getModuleClass('mediusers', 'functions'));
require_once($AppUI->getModuleClass('dPcabinet', 'consultation'));
require_once($AppUI->getModuleClass('dPplanningOp', 'planning'));

$ECompteRenduType = array(
  "consultation", 
  "operation", 
  "hospitalisation", 
  "autre"
);

class CCompteRendu extends CMbObject {
  // DB Table key
  var $compte_rendu_id = null;

  // DB References
  var $chir_id = null; // not null when is a template associated to a user
  var $function_id = null; // not null when is a template associated to a function
  var $object_id = null; // null when is a template, not null when a document

  // DB fields
  var $nom = null;
  var $source = null;
  var $type = null;
  var $valide = null;
  
  /// Form fields
  var $_is_document = false;
  var $_is_modele = false;
  var $_object_className = null;
  
  // Referenced objects
  var $_ref_chir = null;
  var $_ref_function = null;
  var $_ref_object = null;

  function CCompteRendu() {
    $this->CMbObject("compte_rendu", "compte_rendu_id");

    $this->_props["chir_id"]     = "ref";
    $this->_props["function_id"] = "ref";
    $this->_props["object_id"]   = "ref";
    $this->_props["nom"]         = "str|notNull|confidential";
    $this->_props["source"]      = "html|confidential";
    $this->_props["type"]        = "enum|operation|hospitalisation|consultation|notNull";
  }
  
  function check() {
    if ($this->chir_id and $this->function_id) {
      return "Un mod�le ne peut pas appartenir � la fois � une fonction et un utilisateur";
    }

    if (!$this->object_id and !($this->chir_id or $this->function_id)) {
      return "Un mod�le doit appertenir � un utilisateur ou une fonction";
    }
    
    if ($this->object_id and ($this->chir_id or $this->function_id)) {
		  return "un document n'appartient ni � un utilisateur ni une fonction, il doit �tre li� � un objet'";
		}
    
    return parent::check();

  }
  
  function loadModeles($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!isset($where['object_id'])) {
      $where['object_id'] = "IS NULL";
    }
    
    return parent::loadList($where, $order, $limit, $group, $leftjoin);
  }

  function loadDocuments($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!isset($where['object_id'])) {
      $where['object_id'] = "IS NOT NULL";
    }
    
    return parent::loadList($where, $order, $limit, $group, $leftjoin);
  }
  
  function updateFormFields() {
    switch($this->type) {
      case "consultation" :
        $this->_object_className = "CConsultation";
        break;
      case "operation" :
        $this->_object_className = "COperation";
        break;
      case "hospitalisation" :
        $this->_object_className = "COperation";
        break;
      case "autre" :
        $this->_object_className = "COperation";
    }
    if($this->object_id == null)
      $this->_view = "Mod�le : ";
    else
      $this->_view = "Document : ";
    $this->_view .= $this->nom;
  }


  function loadRefsFwd() {
    // Forward references

    // Objet
    $this->_ref_object = new $this->_object_className;
    if($this->object_id)
      $this->_ref_object->load($this->object_id);
      $this->_ref_object->loadRefsFwd();

    // Chirurgien
    $this->_ref_chir = new CMediusers;
    if($this->chir_id) {
      $this->_ref_chir->load($this->chir_id);
    } elseif($this->object_id) {
      switch($this->_object_className) {
        case "CConsultation" :
          $this->_ref_chir->load($this->_ref_object->_ref_plageconsult->chir_id);
          break;
        case "COperation" :
          $this->_ref_chir->load($this->_ref_object->chir_id);
          break;
      }
    }

    // Fonction
    $this->_ref_function = new CFunctions;
    if($this->function_id)
      $this->_ref_function->load($this->function_id);
  }
}

?>