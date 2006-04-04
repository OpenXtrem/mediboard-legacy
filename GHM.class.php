<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author Romain Ollivier
 */

class CGHM {
  // Variables de structure 
  
  // Id de la base de donn�es (qui doit �tre dans le config.php)
  var $dbghm = null;
  
  // Informations sur le patient
  var $age = null;
  var $sexe = null;
  
  // Informations de diagnostic
  var $DP = null;   // Diagnostic principal
  var $DR = null;   // Diagnostic reli�
  var $DASs = null; // Diagnostics associ�s significatifs
  var $DADs = null; // Diagnostics associ�s documentaires
  
  // Informations sur les actes
  var $actes = null;
  
  // Informations sur l'hospi
  var $type_hospi = null;
  var $duree = null;
  var $s�ances = null;
  var $motif = null;
  var $destination = null;
  
  // Variable calcul�es
  var $CM = null;
  var $CM_nom = null;
  var $GHM = null;
  var $GHM_nom = null;
  var $GHM_groupe = null;
  var $GHS = null;
  var $borne_basse = null;
  var $borne_haute = null;
  var $tarif_2006 = null;
  var $EXH = null;
  var $chemin = null;

  // Chronom�tre
  var $chrono;

  // Constructeur
  function CGHM() {
    global $AppUI;
    
    // Connection � la base
    $this->dbghm = $AppUI->cfg['baseGHS'];
    do_connect($this->dbghm);
    
    // Initialisation des variables
    $this->type_hospi = "comp";
    $this->chemin = "";
    $this->chrono = new chronometer();
  }

  // V�rification de l'appartenance � une liste
  function isFromList($type, $liste) {
    $elements = array();
    $liste_ids = array();
    $column1 = null;
    $column2 = null;
    switch($type) {
      case "DP" :
        $table = "diag";
        $elements[] = $this->DP;
        break;
      case "DR" :
        $table = "diag";
        $elements[] = $this->DR;
        break;
      case "DAS" :
        $table = "diag";
        $elements = $this->DASs;
        break;
      case "Actes" :
        $table = "acte";
        foreach($this->actes as $acte) {
          $elements[] = $acte["code"];
        }
        break;
      default :
        return 0;
    }
    if(preg_match("`^[AD]-[[:digit:]]+`", $liste)) {
      $column1 = "code";
      $column2 = "liste_id";
      $liste_ids[] = $liste;
    } else if (preg_match("`^CMA([[:alpha:]]{0,3})`", $liste, $cma)) {
      $column1 = "cma".strtolower($cma[1])."_id";
      $table = "cma".strtolower($cma[1]);
      $liste_ids[] = "";
    } else if(preg_match("`^CM([[:digit:]]{2})`", $liste, $cm)) {
      $column1 = "code";
      $column2 = "CM_id";
      $liste_ids[] = $cm[1];
    } else {
      $column1 = "code";
      $column2 = "liste_id";
      $sql = "SELECT liste_id FROM liste WHERE nom LIKE '%$liste%'";
      $result = db_exec($sql, $this->dbghm);
      if(mysql_num_rows($result) == 0) {
        return 0;
      }
      while($row = db_fetch_array($result)) {
        $liste_ids[] = $row["liste_id"];
      }
    }
    $n = 0;
    foreach($elements as $element) {
      foreach($liste_ids as $liste_id) {
        $sql = "SELECT * FROM $table WHERE $column1 = '$element'";
        if($column2)
          $sql .= "AND $column2 = '$liste_id'";
        $result = db_exec($sql, $this->dbghm);
        $n = $n + mysql_num_rows($result);
      }
    }
    return $n;
  }

  // V�rification de l'appartenance � un groupe (op�ratoire, m�dical, ...)
  function isFromGroup($type, $groupe) {
    if($groupe == "non op�ratoires") {
      $n = 0;
      $sql = "SELECT * FROM liste WHERE nom LIKE '%(non op�ratoires)%'";
      $listeNO = db_loadList($sql, null, $this->dbghm);
      foreach($this->actes as $acte) {
        $isNO = 0;
        foreach($listeNO as $liste) {
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'" .
              "\nAND liste_id = '".$liste["liste_id"]."'" .
              "\nAND CM_id = '$this->CM'";
          $result = db_exec($sql, $this->dbghm);
          if (mysql_num_rows($result))
            $isNO = 1;
        }
        if($isNO)
          $n++;
      }
      if($n == count($this->actes))
        return $n;
      else
        return 0;
    } else if($groupe == "operatoire") {
      $n = 0;
      $sql = "SELECT * FROM liste WHERE nom LIKE '%(non op�ratoires)%'";
      $listeNO = db_loadList($sql, null, $this->dbghm);
      foreach($this->actes as $acte) {
        $isO = 1;
        foreach($listeNO as $liste) {
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'" .
              "\nAND liste_id = '".$liste["liste_id"]."'" .
              "\nAND CM_id = '$this->CM'";
          $result = db_exec($sql, $this->dbghm);
          if (mysql_num_rows($result))
            $isO = 0;
        }
        if($isO)
          $n++;
      }
      return $n;
    } else if($groupe == "non m�dical") {
      // A faire : liste A-173 ?
      // R�sultat : attendre la liste sur l'atih
      $n = 0;
      return $n;
    } else if($groupe == "activit� 4") {
      $n = 0;
      foreach($this->actes as $acte) {
        if($acte["activite"] == 4) {
          $n++;
        }
      }
      return $n;
    }
  }

  // Obtention de la cat�gorie majeure
  function getCM() {
    // V�rification du type d'hospitalisation
    if($this->type_hospi == "s�ance") {
      $this->CM = "28";
    } else if($this->type_hospi == "ambu") {
      $this->CM = "24";
    } else if($this->isFromList("Actes", "transplantation")) {
      $this->CM = "27";
    } else if($this->isFromList("DP", "D-039")) {
      $this->CM = "26";
    } else if(($this->isFromList("DP", "D-036") && $this->isFromList("DAS", "D-037"))||
              ($this->isFromList("DP", "D-037") && $this->isFromList("DAS", "D-036"))) {
      $this->CM = "25";
    } else {
      $sql = "SELECT * FROM diagcm WHERE diag = '$this->DP'";
      $result = db_exec($sql, $this->dbghm);
      if(mysql_num_rows($result) == 0) {
        $this->CM = 0;
      } else {
        $row = db_fetch_array($result);
        $this->CM = $row["CM_id"];
      }
    }
    if($this->CM) {
      $sql = "SELECT * FROM cm WHERE CM_id = '$this->CM'";
      $result = db_exec($sql, $this->dbghm);
      $row = db_fetch_array($result);
      $this->CM_nom = $row["nom"];
    }
    return $this->CM;
  }
  
  // V�rification des conditions de l'arbre
  function checkCondition($type, $cond) {
    $n = 0;
    $this->chemin .= "On teste ($type : $cond) -> ";
    if($type == "1A" || $type == "2A" || $type == "nA") {
      if($cond == "non op�ratoires" || $cond == "operatoire" || $cond == "non m�dical") {
        $n = $this->isFromGroup($type, $cond);
        if($type[0] != "n") {
          if($n >= $type[0]) {
            $n = 1;
          } else {
            $n = 0;
          }
        } else {
          if($n == count($this->actes)) {
            $n = 1;
          } else {
            $n = 0;
          }
        }
      } else {
        $n = $this->isFromList("Actes", $cond);
        if($type[0] != "n") {
          if($n >= $type[0]) {
            $n = 1;
          } else {
            $n = 0;
          }
        } else {
          if($n == count($this->actes)) {
            $n = 1;
          } else {
            $n = 0;
          }
        }
      }
    } else if($type == "DP") {
      $n = $this->isFromList("DP", $cond);
    } else if($type == "1DAS") {
      $n = $this->isFromList("DAS", $cond);
    } else if($type == "DR") {
      $n = $this->isFromList("DR", $cond);
    } else if($type == "Age") {
      preg_match("`^([<>])([[:digit:]]+)([[:alpha:]])`", $cond, $ageTest);
      if(preg_match("`^([[:digit:]]+)([[:alpha:]])`", $this->age, $agePat)) {
        if($ageTest[1] == ">") {
          if($ageTest[3] == "j" && $agePat[2] == "a") {
            $n = 1;
          } else if($ageTest[3] == $agePat[2] && $agePat[1] > $ageTest[2]) {
            $n = 1;
          }
        } else if($ageTest[1] == "<") {
          if($ageTest[3] == "a" && $agePat[2] == "j") {
            $n = 1;
          } else if($ageTest[3] == $agePat[2] && $agePat[1] < $ageTest[2]) {
            $n = 1;
          }
        }
      }
    } else if($type == "Sexe") {
      if($cond == $this->sexe)
        $n = 1;
    } else if($type == "DS") {
      preg_match("`([<>=]{1,2})([[:digit:]]+)`", $cond, $duree);
      if($duree[1] == ">=") {
        if($this->duree >= $duree[2]) {
          $n = 1;
        }
      } else if($duree[1] == "<") {
        if($this->duree < $duree[2]) {
          $n = 1;
        }
      }
    } else if($type == "NS") {
      preg_match("`([<>=]{1,2})([[:digit:]]+)`", $cond, $seances);
      if($seances[1] == ">=") {
        if($this->seances >= $seances[2]) {
          $n = 1;
        }
      } else if($seances[1] == "<") {
        if($this->seances < $seances[2]) {
          $n = 1;
        }
      }
    } else if($type == "MS" && $cond == $this->motif) {
      $n = 1;
    } else if($type == "Dest" && $cond == $this->destination) {
      $n = 1;
    }
    $this->chemin .= $n;
    return $n;
  }

  // Obtention du GHM
  function getGHM() {
    $this->chrono->start();
    $this->GHM = null;
    foreach($this->DASs as $key => $DAS) {
      $sql = "SELECT * FROM incomp WHERE CIM1 = '$DAS' AND CIM2 = '".$this->DP."'";
      $result = db_exec($sql, $this->dbghm);
      if(mysql_num_rows($result)) {
        $this->DADs[] = $DAS;
        unset($this->DASs[$key]);
      }
    }
    if(!$this->CM)
      $this->getCM();
    $sql = "SELECT * FROM arbre WHERE CM_id = '$this->CM'";
    $listeBranches = db_loadList($sql, null, $this->dbghm);
    $parcoursBranches = 0;
    $row = $listeBranches[0];
    $maxcond = 5;
    for($i = 1; ($i <= $maxcond*2) && ($this->GHM === null); $i = $i + 2) {
      $type = $i;
      $cond = $i + 1;
      // On v�rifie qu'on a pas d�j� fait le test
      if(isset($oldrow) && $row != $oldrow) {
        while($row[$type] == $oldrow[$type] && $row[$cond] == $oldrow[$cond]) {
          // On avance d'une ligne
          $parcoursBranches++;
          $row = $listeBranches[$parcoursBranches];
        }
      }
      $oldrow = $row;
      $this->chemin .= "Pour i = ".(($i+1)/2).", arbre_id = ".$row["arbre_id"].", ";
      if($row[$type] == '') {
        $this->chemin .= "c'est bon";
        $this->chemin .= " pour ".$row["GHM"]."<br />";
        $this->GHM = $row["GHM"];
      } else if(!($this->checkCondition($row[$type], $row[$cond]))) {
        $this->chemin .= " pour ".$row["GHM"]."<br />";
        // On avance d'une ligne
        $parcoursBranches++;
        $row = $listeBranches[$parcoursBranches];
        if(!($row = @$listeBranches[$parcoursBranches])) {
          $this->GHM = 0;
        } else if(!$row[$type]) {
          $this->GHM = $row["GHM"];
        } else {
          // On reviens � la derni�re condition correcte
          $j = $i - 2; $nj = $j + 1;
          if($j > 0) {
            while($row[$j] != $oldrow[$j] && $row[$nj] != $oldrow[$nj] && $i > 1) {
              $i = $j; $j = $j - 2; $nj = $j + 1;
            }
          }
        }
        $i = $i - 2;
      } else {
        $this->chemin .= " pour ".$row["GHM"]."<br />";
      }
    }
    if($this->GHM) {
      $sql = "SELECT * FROM ghm WHERE GHM_id = '$this->GHM'";
      $result = db_exec($sql, $this->dbghm);
      $row = db_fetch_array($result);
      $this->GHM_nom = $row["nom"];
      $this->GHM_groupe = $row["groupe"];
      $this->GHS = $row["GHS"];
      $this->borne_basse = $row["borne_basse"];
      $this->borne_haute = $row["borne_haute"];
      $this->tarif_2006 = $row["tarif_2006"];
      $this->EXH = $row["EXH"];
    }
    $this->chrono->stop();
    $this->chemin .= "Calcul� en ".$this->chrono->total." secondes";
    return $this->GHM;
  }
}