<!-- $Id$ -->

{literal}
<script type="text/javascript">
//<![CDATA[
function checkPatient() {
  var form = document.editFrm;
    
  if (form.nom.value.length == 0) {
    alert("Nom manquant");
    form.nom.focus();
    return false;
  }
    
  return true;
}
//]]>
</script>
{/literal}

<table class="main">
  <tr>
    <td class="greedyPane">
    
      <form name="find" action="./index.php" method="get">
      <input type="hidden" name="m" value="{$m}" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">Identit�</th>
        </tr>
  
        <tr>
          <th>Nom:</th>
          <td><input tabindex="1" type="text" name="nom" value="{$nom}" /></td>
        </tr>
        
        <tr>
          <th>Pr�nom:</th>
          <td><input tabindex="2" type="text" name="prenom" value="{$prenom}" /></td>
        </tr>
        
        <tr>
          <td class="button" colspan="2"><input type="submit" value="rechercher" /></td>
        </tr>
      </table>

      </form>
      
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Pr�nom</th>
          <th>Adresse</th>
          <th>Ville</th>
        </tr>

        {foreach from=$patients item=curr_patient}
        <tr>
          <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;id={$curr_patient.patient_id}">{$curr_patient.nom}</a></td>
          <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;id={$curr_patient.patient_id}">{$curr_patient.prenom}</a></td>
          <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;id={$curr_patient.patient_id}">{$curr_patient.adresse}</a></td>
          <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;id={$curr_patient.patient_id}">{$curr_patient.ville}</a></td>
        </tr>
        {/foreach}
        
      </table>

    </td>
 
    {if $patient->patient_id}
    <td class="pane">
      <table class="form">
        <tr>
          <th class="category" colspan="2">Identit�</th>
          <th class="category" colspan="2">Information m�dicales</th>
        </tr>

        <tr>
          <th>Nom:</th>
          <td>{$patient->nom}</td>
          <th>Incapable majeur:</th>
          <td>
            {if $patient->incapable_majeur == "o"} oui {/if}
            {if $patient->incapable_majeur == "n"} non {/if}
          </td>
        </tr>
        
        <tr>
          <th>Pr�nom:</th>
          <td>{$patient->prenom}</td>
          <th>ATNC:</th>
          <td>
            {if $patient->ATNC == "o"} oui {/if}
            {if $patient->ATNC == "n"} non {/if}
        </tr>
        
        <tr>
          <th>Date de naissance:</th>
          <td>{$patient->_jour} / {$patient->_mois} / {$patient->_annee}</td>
        </tr>
        
        <tr>
          <th>Sexe:</th>
          <td>
            {if $patient->sexe == "m"} masculin {/if}
            {if $patient->sexe == "f"} f�minin  {/if} 
          </td>
        </tr>
        
        <tr>
          <th class="category" colspan="2">Coordonn�es</th>
          <th class="category" colspan="2">Information administratives</th>
        </tr>
        
        <tr>
          <th>Adresse:</th>
          <td>{$patient->adresse}</td>
          <th>Num�ro d'assur� social:</th>
          <td>{$patient->matricule}</td>
        </tr>
        
        <tr>
          <th>Ville:</th>
          <td>{$patient->ville}</td>
          <th>Code administratif:</th>
          <td>{$patient->SHS}</td>
        </tr>
        
        <tr>
          <th>Code Postal:</th>
          <td>{$patient->cp}</td>
        </tr>
        
        <tr>
          <th>T�l�phone:</th>
          <td>{$patient->tel}</td>
        </tr>
        
        {if $canEdit}
        <tr>
          <td class="button" colspan="4">
            <form name="modif" action="./index.php" method="get">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="tab" value="vw_edit_patients" />
            <input type="hidden" name="id" value="{$patient->patient_id}" />
            <input type="submit" value="Modifier" />
            </form>
          </td>
        </tr>
        {/if}

      </table>
      
    </td>
    {/if}

  </tr>
  {if $patient->_ref_operations}
  <tr>
    <td class="pane"></td>
    <td class="pane">
      <table class="form">
        <tr><th>Interventions</th></tr>
        <tr><th>Date</th><th>code CCAM</th></tr>
        {foreach from=$patient->_ref_operations item=curr_op}
        <tr>
          <td>{curr_op->date}</td><td>{curr_op->CCAM_code}</td>
        </tr>
          {/foreach}
      </table>
    </td>
</table>
      