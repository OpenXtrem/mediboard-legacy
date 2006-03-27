<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$type = mbGetValueFromGet("type", 0);

//Reconnaissance d'un code Cim10
$regCim10 = "[[:alpha:]][[:digit:]]{2}[.]?[[:alnum:]\+\*-]*";

// Reconnaissance d'un code CCAM
$regCCAM = "[[:alpha:]]{4}[[:digit:]]{3}";

switch($type) {
  case "AddCM" :
    addcm();
    break;
  case "AddDiagCM" :
    adddiagcm();
    break;
  case "AddActes" :
    addactes();
    break;
  case "AddGHM" :
    addghm();
    break;
  case "AddCMA" :
    addcma();
    break;
  case "AddIncomp" :
    addincomp();
    break;
  case "AddArbre" :
    addarbre();
    break;
  default:
    echo "Argument <strong>type</strong> manquant";
    break;
}

/** Ajout des CM, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/CM.txt
 * Ligne sous la forme "XX Nom du CM" */
function addcm() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  $fileName = "./modules/dPpmsi/ghm/CM.txt";
  do_connect($base);

  // Table des CM
  $sql = "DROP TABLE IF EXISTS `cm`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `cm` (
  `CM_id` varchar(2) NOT NULL default '0',
  `nom` varchar(100) default NULL,
  PRIMARY KEY  (`CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des cat�goris majeurs';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des CM cr��e<br />";
  }

  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouv�<br>";
    return;
  }
  
  $nCM = 0;

  // Ajout des lignes
  while (!feof($file)) {
    $id = fgets($file, 3);
    fgets($file, 2);
    $nom = fgets($file, 1024);
    $sql = "INSERT INTO CM values('$id', '".addslashes($nom)."');";
    db_exec($sql, $base);
    if($error = db_error($base)) {
      echo "$error ($sql)<br />";
    } else {
      $nCM++;
      //echo "<strong>Done :</strong> ".$id."-".$nom."<br />";
    }
  }
  echo "<strong>Done :</strong> $nCM CM cr��s<br />";
}

/** Ajout des diagnostics d'entr�e dans les CM, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/diagCM.txt */
function adddiagcm() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  $fileName = "./modules/dPpmsi/ghm/diagCM.txt";
  do_connect($base);
  $sql = "DROP TABLE IF EXISTS `diagCM`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `diagCM` (
  `diag` varchar(10) NOT NULL default '0',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`diag`, `CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des diagnostics d\'entree dans les CM';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des diagnostics d'entr�e cr��e<br />";
  }
  $file = @fopen( $fileName, 'rw' );
  if(! $file) {
    echo "Fichier non trouv�<br>";
    return;
  }
  $curr_cmd = null;
  $nCM = 0;
  $nDiags = 0;
  while (!feof($file) ) {
    $line = fgets($file, 1024);
    if(preg_match("`^Diagnostics d'entr�e dans la CMD n� ([[:digit:]]{2})`", $line, $cmd)) {
      $curr_cmd = $cmd[1];
      $nCM++;
    } else if(preg_match("`^($regCim10)`", $line, $diag)) {
      $sql = "INSERT INTO diagCM VALUES('".$diag[1]."', '$curr_cmd')";db_exec($sql, $base);
      if($error = db_error($base)) {
        echo "$error ($sql)<br />";
      } else {
        $nDiags++;
        //echo "<strong>Done :</strong> ".$diag[1]." ($curr_cmd)<br />";
      }
    }
  }
  echo "<strong>Done :</strong> $nDiags diagnostics cr��s dans $nCM CM<br />";
}

/** Ajout des listes d'actes, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/Actes.txt
 * Ligne sous la forme
 * "CMD XX"
 * "Liste AouD-XXX : nom"
 * "CCAMXXX/Phase Libelle" */
function addactes() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  $fileName = "./modules/dPpmsi/ghm/Listes.txt";
  do_connect($base);
  $sql = "DROP TABLE IF EXISTS `liste`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `liste` (
  `liste_id` varchar(6) NOT NULL default '0',
  `nom` varchar(100) default NULL,
  PRIMARY KEY  (`liste_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des listes';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des listes cr��e<br />";
  }
  $sql = "DROP TABLE IF EXISTS `acte`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `acte` (
  `code` varchar(7) NOT NULL default '0',
  `phase` varchar(1) NOT NULL default '0',
  `liste_id` varchar(5) NOT NULL default 'A-001',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`code`, `phase`, `liste_id`, `CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des actes';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des actes cr��e<br />";
  }
  $sql = "DROP TABLE IF EXISTS `diag`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `diag` (
  `code` varchar(7) NOT NULL default '0',
  `liste_id` varchar(5) NOT NULL default 'D-001',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`code`, `liste_id`, `CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des diagnostics';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des diagnostics cr��e<br />";
  }
  $file = @fopen( $fileName, 'rw' );
  if(! $file) {
    echo "Fichier non trouv�<br>";
    return;
  }
  $curr_cmd = null;
  $curr_liste = null;
  $nCM = 0;
  $nListes = 0;
  $nActes = 0;
  $nDiags = 0;
  while (!feof($file) ) {
    $line = fgets($file, 1024);
    if(preg_match("`^CMD ([[:digit:]]{2})`", $line, $cmd)) {
      $curr_cmd = $cmd[1];
      $nCM++;
    } else if(preg_match("`^Liste ([AD]-[[:digit:]]*) : ([[:alnum:][:space:][:punct:]]*)`", $line, $liste) && $curr_cmd) {
      $curr_liste = $liste[1];
      $sql = "INSERT INTO liste VALUES('".$liste[1]."', '".addslashes($liste[2])."')";
      db_exec($sql, $base);
      if($error = db_error($base)) {
        // L'erreur est comment�e car certaines listes sont entr�es en doublon
        //echo "$error ($sql)<br />";
      } else {
        $nListes++;
        //echo "<strong>Done :</strong> ".$liste[1]." : ".$liste[2]." (".$curr_cmd.")<br />"; 
      }
    } else if(preg_match("`^($regCCAM)/([[:digit:]])`", $line, $acte) && $curr_liste) {
      $sql = "INSERT INTO acte VALUES('".$acte[1]."', '".$acte[2]."', '$curr_liste', '$curr_cmd')";
      db_exec($sql, $base);
      if($error = db_error($base)) {
        echo "$error ($sql)<br />";
      } else {
        $nActes++;
        //echo "<strong>Done :</strong> ".$acte[1]."/".$acte[2]." ($curr_liste, CMD $curr_cmd)<br />";
      }
    } else if(preg_match("`^($regCim10)`", $line, $diag) && $curr_liste) {
      $sql = "INSERT INTO diag VALUES('".$diag[1]."', '$curr_liste', '$curr_cmd')";
      db_exec($sql, $base);
      if($error = db_error($base)) {
        //echo "$error ($sql)<br />";
      } else {
        $nDiags++;
        //echo "<strong>Done :</strong> ".$diag[1]." ($curr_liste, CMD $curr_cmd)<br />";
      }
    }
  }
  echo "<strong>Done :</strong> $nCM CM trouv�s et $nListes listes, $nActes actes et $nDiags diagnostics cr��s<br />";
}

/** Ajout des GHM, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/GHM.txt */
function addghm() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  $fileName = "./modules/dPpmsi/ghm/GHM.txt";
  do_connect($base);

  // Table des CM
  $sql = "DROP TABLE IF EXISTS `ghm`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `ghm` (
  `GHM_id` varchar(6) NOT NULL default '0',
  `nom` text default NULL,
  `groupe` varchar(100) NOT NULL default 'groupes chirurgicaux',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`GHM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des groupements homog�nes de malades';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des GHM cr��e<br />";
  }

  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouv�<br>";
    return;
  }
  
  $nGHM = 0;
  $curr_CM = null;
  $curr_group = null;

  // Ajout des lignes
  while (!feof($file)) {
    $line = fgets($file, 1024);
    if(preg_match("`^CAT�GORIE MAJEURE DE DIAGNOSTIC : ([[:digit:]]{2})`", $line, $cm)) {
      $curr_CM = $cm[1];
      //echo "<strong>Done :</strong> Curr_CM = $curr_CM<br />";
    } else if(preg_match("`^Groupes ([[:alnum:][:space:][:punct:]]*)`", $line, $groupe)) {
      $curr_group = $groupe[1];
      //echo "<strong>Done :</strong> Curr_groupe = $curr_groupe<br />";
    } else if(preg_match("`^([[:digit:]]{2}[[:alpha:]][[:digit:]]{2}[[:alpha:]]) ([[:alnum:][:space:][:punct:]]*)`", $line, $GHM)) {
      $sql = "INSERT INTO ghm" .
          "\nvalues('".addslashes($GHM[1])."', '".addslashes($GHM[2])."'," .
          "\n'".addslashes($curr_group)."', '".addslashes($curr_CM)."');";
      db_exec($sql, $base);
      if($error = db_error($base)) {
        echo "$error ($sql)<br />";
      } else {
        //echo "<strong>Done :</strong> ".$id."-".$nom."<br />";
        $nGHM++;
      }
    }
  }
  echo "<strong>Done :</strong> $nGHM GHM cr��s<br />";
}

/** Ajout des CMA, valide pour la version 1010
 * Fichiers texte :
 * ./modules/dPpmsi/ghm/cma.txt
 * ./modules/dPpmsi/ghm/cmas.txt
 * ./modules/dPpmsi/ghm/cmasnt.txt */
function addcma() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  do_connect($base);

  // Table des Complications et Morbidit�s Associ�es, CMA S�v�res et CMAS Non Traumatiques
  $listCM = array("cma", "cmas", "cmasnt");
  foreach($listCM as $typeCM) {
    //$typeCM = "cma";
    $sql = "DROP TABLE IF EXISTS `$typeCM`;";
    db_exec($sql, $base);
    if($error = db_error($base)) {
      echo "$error ($sql)<br />";
    }
    $sql = "CREATE TABLE `$typeCM` (
    `".$typeCM."_id` varchar(10) NOT NULL default '0',
    PRIMARY KEY  (`".$typeCM."_id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des $typeCM';";
    db_exec($sql, $base);
    if($error = db_error($base)) {
      echo "$error ($sql)<br />";
    } else {
      echo "<strong>Done :</strong> Table des $typeCM cr��e<br />";
    }
  
    $fileName = "./modules/dPpmsi/ghm/$typeCM.txt";
    // Lecture du fichier
    $file = @fopen($fileName, 'rw');
    if(!$file) {
      echo "Fichier non trouv�<br>";
      return;
    }
    
    $nombre = 0;
  
    // Ajout des lignes
    while (!feof($file)) {
      $line = fgets($file, 1024);
      if(preg_match("`^($regCim10)`", $line, $CMA)) {
        $sql = "INSERT INTO $typeCM values('$CMA[1]');";
        db_exec($sql, $base);
        if($error = db_error($base)) {
          echo "$error ($sql)<br />";
        } else {
          $nombre++;
        }
      }
    }
    echo "<strong>Done :</strong> $nombre $typeCM cr��s<br />";
  }
}

/** Ajout des incompatibilit�s entre DP - CMA, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/incomp.txt */
function addincomp() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  do_connect($base);

  // Table des incompatibilit�s
  $sql = "DROP TABLE IF EXISTS `incomp`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }
  $sql = "CREATE TABLE `incomp` (
  `CIM1` varchar(10) NOT NULL default '0',
  `CIM2` varchar(10) NOT NULL default '0',
  PRIMARY KEY  (`CIM1`, `CIM2`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des incompatibilit�s DP - CMA';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table des incompatibilit�s cr��e<br />";
  }

  $fileName = "./modules/dPpmsi/ghm/incomp.txt";
  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouv�<br>";
    return;
  }
  
  $nIncomp = 0;
  $baseCode = null;
  $n = 0;

  // Ajout des lignes
  $tabIncomp = array();
  while (!feof($file)) {
    $line = fgets($file, 1024);
    // A t'on au moins un code au d�but
    if(preg_match_all("`$regCim10`", $line, $incomp)) {
      $listIncomp = $incomp[0];
      // A t'on plus d'un code ?
      if(count($listIncomp) > 1) {
        // Sommes nous en d�but de liste ?
        if($listIncomp[0] >= $listIncomp[1]) {
          $baseCode = $listIncomp[0];
          foreach($listIncomp as $place => $code) {
            if($place > 0) {
              $tabIncomp[$baseCode][] = $code;
            }
          }
        } else {
          foreach($listIncomp as $place => $code) {
            $tabIncomp[$baseCode][] = $code;
          }
        }
      // A t'on une liste dupliqu�e ?
      } else if(preg_match("`m�me liste que (".$regCim10.")`", $line, $duplicata)){
        $baseCode = $listIncomp[0];
        $copy = $duplicata[1];
        $tabIncomp[$baseCode] = $tabIncomp[$copy];
      } else {
        $tabIncomp[$baseCode][] = $listIncomp[0];
      }
    }
    $n++;
  }
  //Remplissage de la base
  foreach($tabIncomp as $baseCode => $liste) {
    foreach($liste as $code) {
      $sql = "INSERT INTO incomp VALUES('$baseCode', '$code');";
      db_exec($sql, $base);
      if($error = db_error($base)) {
        echo "$error ($sql)<br />";
      } else {
        $nIncomp++;
      }
    }
  }
  echo "<strong>Done :</strong> $nIncomp incompatibilit�s cr��es<br />";
}

/** Cr�ation de l'arbre de d�cision pour l'orientation vers les GHM
 * valide pour la version 1010
 * Fichier CSV : ./modules/dPpmsi/ghm/arbreGHM.csv
 * premi�re ligne      : nom des colonnes
 * s�parateur          : ',' (virgule)
 * s�parateur du texte : ''' (simple guillemet)
 */

function addarbre() {
  global $AppUI, $regCim10, $regCCAM;
  $base = $AppUI->cfg['baseGHS'];
  do_connect($base);

  // Table des incompatibilit�s
  $sql = "DROP TABLE IF EXISTS `arbre`;";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  }

  $fileName = "./modules/dPpmsi/ghm/arbreGHM.csv";
  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouv�<br>";
    return;
  }
  
  $line = fgets($file, 1024);
  $trans = array(
      "'" => "",
      "\n" => "",
      "\r" => "");
  $line = strtr($line, $trans);
  $columns = explode(",", $line);
  
  $sql = "CREATE TABLE `arbre` (" .
      "\n`arbre_id` INT(11) NOT NULL auto_increment,";
  foreach($columns as $column) {
    $sql .= "\n `$column` VARCHAR(25) DEFAULT NULL,";
  }
  $sql .= "\nPRIMARY KEY (`arbre_id`)," .
      "\nKEY `CM_id` (`CM_id`)" .
      ") ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de l\'arbre de d�cision pour les GHM';";
  db_exec($sql, $base);
  if($error = db_error($base)) {
    echo "$error ($sql)<br />";
  } else {
    echo "<strong>Done :</strong> Table de l'arbre de d�cision cr��e<br />";
  }
  
  $nPass = 0;
  $nFailed = 0;

  $trans = array(
    "\n" => "",
    "\r" => "",
    ",," => ",'',");
  // Ajout des lignes
  while (!feof($file)) {
    $line = fgets($file, 1024);
    $line = strtr($line, $trans);
    $line = strtr($line, $trans);
    if(substr($line, -1, 1) == ",")
      $line .= "''";
    $sql = "INSERT INTO arbre" .
        "\nvalues('', $line);";
    db_exec($sql, $base);
    if($error = db_error($base)) {
      echo "$error ($sql)<br />";
      $nFailed++;
    } else {
      //echo "<strong>Done :</strong> $line<br />";
      $nPass++;
    }
  }
  echo "<strong>Done :</strong> $nPass lignes cr��s, $nFailed lignes �chou�es<br />";
}
?>