<!-- $Id$ -->

{literal}
<script language="javascript">
function checkForm() {
  var form = document.editFrm;
  var field = null;
  
  if (field = form.chir_id)
    if (field.value == 0) {
      alert("Chirurgien manquant");
      popChir();
      return false;
    }

  if (field = form.pat_id)
    if (field.value == 0) {
      alert("Patient manquant");
      popPat();
      return false;
    }

  if (field = form.CIM10_code)
    if (field.value.length == 0) {
      alert("Code CIM10 Manquant");
      popCode('cim10');
      return false;
    }

  if (field = form.CCAM_code)
    if (field.value.length == 0) {
      alert("Code CCAM Manquant");
      popCode('ccam');
      return false;
    }

/* Bug in IE
  if (form._hour_op.value == 0 && form._min_op.value == 0) {
    alert("Temps op�ratoire invalide");
    form.hour_op.focus();
    return false;
  }
*/

  if (field = form.plageop_id)
    if (field.value == 0) {
      alert("Intervention non planifi�e");
      popPlage();
      return false;
    }

  if (field = form._date_rdv_adm)
    if (field.value.length == 0) {
      alert("Admission: date manquante");
      popCalendar('_rdv_adm', '_rdv_adm');
      return false;
    }
/* Bug in IE
  if (field = form._hour_adm)
    if (field.value.length == 0) {
      alert("Admission: heure manquante");
      field.focus();
      return false;
    }
*/
  return true;
}

function popChir() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=chir_selector';
  url += '&dialog=1';
  
  window.open(url, 'Chirurgien', 'left=50, top=50, height=250, width=400, resizable=yes');
}

function setChir( key, val ){
  var f = document.editFrm;
   if (val != '') {
      f.chir_id.value = key;
      f._chir_name.value = val;
      window.chir_id = key;
      window._chir_name = val;
    }
}

function popPat() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=pat_selector';
  url += '&dialog=1';

  window.open(url, 'Patient', 'left=50, top=50, width=400, height=250, resizable=yes');
}

function setPat( key, val ) {
  var f = document.editFrm;

  if (val != '') {
    f.pat_id.value = key;
    f._pat_name.value = val;
    window.pat_id = key;
    window._pat_name = val;
  }
}

function popCode(type) {
  var url = './index.php?m=dPplanningOp';
  url += '&a=code_selector';
  url += '&dialog=1';
  url += '&chir='+ document.editFrm.chir_id.value;
  url += '&type='+ type;

  window.open(url, 'CIM10', 'left=50, top=50, width=600, height=500, resizable=yes');
}

function setCode( key, type ){
  var f = document.editFrm;

  if (key != '') {
    if(type == 'ccam') {
      f.CCAM_code.value = key;
      window.CCAM_code = key;
    }
    else {
      f.CIM10_code.value = key;
      window.CIM10_code = key;
    }
  }
}

function popPlage() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=plage_selector';
  url += '&dialog=1';
  url += '&chir=' + document.editFrm.chir_id.value;
  url += '&hour=' + document.editFrm._hour_op.value;
  url += '&min=' + document.editFrm._min_op.value;

  window.open(url, 'Plage', 'left=50, top=50, width=400, height=250, resizable=yes');
}

function setPlage( key, val ){
  var f = document.editFrm;

  if (key != '') {
    f.plageop_id.value = key
    f.date.value = val;
    window.plageop_id = key;
    window.date = val;
  }
}

function popProtocole() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=vw_protocoles';
  url += '&dialog=1';
  url += '&chir_id='   + document.editFrm.chir_id.value;
  url += '&CCAM_code=' + document.editFrm.CCAM_code.value;

  window.open(url, 'Protocole', 'top=200, left=250, width=600, height=400, scrollbars=yes, resizable=yes' );
}

function setProtocole(
    chir_id,
    chir_last_name,
    chir_first_name,
    prot_CCAM_code,
    prot_hour_op,
    prot_min_op,
    prot_examen,
    prot_type_adm,
    prot_duree_hospi) {

  var f = document.editFrm;
  
  f.chir_id.value = chir_id;
  f._chir_name.value = "Dr " + chir_last_name + " " + chir_first_name;
  f.CCAM_code.value = prot_CCAM_code;
  f._hour_op.value = prot_hour_op;
  f._min_op.value = prot_min_op;
  f.examen.value = prot_examen;
  f.type_adm.value = prot_type_adm;
  f.duree_hospi.value = prot_duree_hospi;
}

var calendarField = '';
var calWin = null;
 
function popCalendar( field ) {
  calendarField = field;
  idate = eval( 'document.editFrm._date' + field + '.value' );
  
  var url =  'index.php?m=public';
  url += '&a=calendar';
  url += '&dialog=1';
  url += '&callback=setCalendar';
  url += '&date=' + idate;
  
  window.open(url, 'calwin', 'left=250, top=250, width=280, height=250, scrollbars=yes' );
}

function setCalendar( idate, fdate ) {
  fld_date = eval( 'document.editFrm._date' + calendarField );
  fld_fdate = eval( 'document.editFrm.' + calendarField );
  fld_date.value = idate;
  fld_fdate.value = fdate;
}
  
function printForm() {
  // @todo Pourquoi ne pas seulement passer le operation_id? ca parait bcp moins r�gressif
  if (checkForm()) {
    url = './index.php?m=dPplanningOp';
    url += '&a=view_planning';
    url += '&dialog=1';
    url += '&chir_id='     + eval('document.editFrm.chir_id.value'    );
    url += '&pat_id='      + eval('document.editFrm.pat_id.value'     );
    url += '&CCAM_code='   + eval('document.editFrm.CCAM_code.value'  );
    url += '&cote='        + eval('document.editFrm.cote.value'       );
    url += '&hour_op='     + eval('document.editFrm._hour_op.value'    );
    url += '&min_op='      + eval('document.editFrm._min_op.value'     );
    url += '&date='        + eval('document.editFrm.date.value'       );
    url += '&info='        + eval('document.editFrm.info.value'       );
    url += '&rdv_anesth='  + eval('document.editFrm._rdv_anesth.value' );
    url += '&hour_anesth=' + eval('document.editFrm._hour_anesth.value');
    url += '&min_anesth='  + eval('document.editFrm._min_anesth.value' );
    url += '&rdv_adm='     + eval('document.editFrm._rdv_adm.value'    );
    url += '&hour_adm='    + eval('document.editFrm._hour_adm.value'   );
    url += '&min_adm='     + eval('document.editFrm._min_adm.value'    );
    url += '&duree_hospi=' + eval('document.editFrm.duree_hospi.value');
    url += '&type_adm='    + eval('document.editFrm.type_adm.value'   );
    url += '&chambre='     + eval('document.editFrm.chambre.value'    ); 
 
    window.open( url, 'printAdm', 'top=50,left=50, width=700, height=500, scrollbars=yes' );
  }
}
</script>
{/literal}

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="operation_id" value="{$op->operation_id}" />
<input type="hidden" name="rank" value="{$op->rank}" />

<table class="main">
  <tr>
    <td>
  
      <table class="form">
        <tr><th class="category" colspan="3">Informations concernant l'op�ration</th></tr>

        {if !$protocole}
        <tr>
          <td class="button" colspan="3"><input type="button" value="Choisir un protocole" onclick="popProtocole()" /></td>
        </tr>
        {/if}
        
        <tr>
          <th class="mandatory">
            <input type="hidden" name="chir_id" value="{$chir->user_id}" />
            <label for="editFrm_chir_id">Chirurgien:</label>
          </th>
          <td class="readonly"><input type="text" name="_chir_name" size="30" value="{if ($chir)}Dr. {$chir->user_last_name} {$chir->user_first_name}{/if}" readonly="readonly" /></td>
          <td class="button"><input type="button" value="choisir un chirurgien" onclick="popChir()"></td>
        </tr>

        {if !$protocole}
        <tr>
          <th class="mandatory">
            <input type="hidden" name="pat_id" value="{$pat->patient_id}" />
            <label for="editFrm_chir_id">Patient:</label>
          </th>
          <td class="readonly"><input type="text" name="_pat_name" size="30" value="{$pat->nom} {$pat->prenom}" readonly="readonly" /></td>
          <td class="button"><input type="button" value="rechercher un patient" onclick="popPat()" /></td>
        </tr>
        
        <tr>
          <th class="mandatory"><label for="editFrm_CIM10_code">Diagnostic (CIM10):</label></th>
          <td><input type="text" name="CIM10_code" size="10" value="{$op->CIM10_code}" /></td>
          <td class="button"><input type="button" value="selectionner un code" onclick="popCode('cim10')" /></td>
        </tr>
        {/if}

        <tr>
          <th class="mandatory"><label for="editFrm_CCAM_code">Acte m�dical (CCAM):</label></th>
          <td><input type="text" name="CCAM_code" size="10" value="{$op->CCAM_code}" /></td>
          <td class="button"><input type="button" value="selectionner un code" onclick="popCode('ccam')"/></td>
        </tr>

        {if !$protocole}
        <tr>
          <th><label for="editFrm_cote">Cot�:</label></th>
          <td colspan="2">
            <select name="cote">
              <option {if !$op && $op->cote == "total"} selected="selected" {/if} >total</option>
              <option {if $op->cote == "droit"    } selected="selected" {/if} >droit    </option>
              <option {if $op->cote == "gauche"   } selected="selected" {/if} >gauche   </option>
              <option {if $op->cote == "bilat�ral"} selected="selected" {/if} >bilat�ral</option>
            </select>
          </td>
        </tr>
        {/if}

        <tr>
          <th class="mandatory"><label for="editFrm__hour_op">Temps op�ratoire:</label></th>
          <td colspan="2">
            <select name="_hour_op">
            {foreach from=$hours key=key item=hour}
              <option {if (!$op && $key == 1) || $op->_hour_op == $key} selected="selected" {/if}>{$key}</option>
            {/foreach}
            </select>
            :
            <select name="_min_op">
            {foreach from=$mins item=min}
              <option {if (!$op && $min == 0) || $op->_min_op == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>

        {if !$protocole}
        <tr>
          <th class="mandatory">
            <input type="hidden" name="plageop_id" value="{$plage->id}" />
            <label for="editFrm_date">Date de l'intervention:</label>
          </th>
          <td class="readonly"><input type="text" name="date" readonly="readonly" size="10" value="{$plage->_date}" /></td>
          <td class="button"><input type="button" value="choisir une date" onclick="popPlage()" /></td>
        </tr>
        {/if}
        
        <tr>
          <th><label for="editFrm_examen">Examens compl�mentaires:</label></th>
          <td colspan="2"><textarea name="examen" rows="3">{$op->examen}</textarea></td>
        </tr>

        {if !$protocole}
        <tr>
          <th><label for="editFrm_materiel">Mat�riel � pr�voir:</label></th>
          <td colspan="2"><textarea name="materiel" rows="3">{$op->materiel}</textarea></td>
        </tr>

        <tr>
          <th><label for="editFrm_info_n">Information du patient:</label></th>
          <td colspan="2">
            <input name="info" value="o" type="radio" {if $op->info == "o"} checked="checked" {/if}/>
            <label for="editFrm_info_o">Oui</label>
            <input name="info" value="n" type="radio" {if !$op || $op->info == "n"} checked="checked" {/if}/>
            <label for="editFrm_info_n">Non</label>
          </td>
        </tr>
        {/if}

      </table>

    </td>
    <td>

      <table class="form">
        {if !$protocole}
        <tr><th class="category" colspan="3">RDV d'anesth�sie</th></tr>

        <tr>
          <th><label for="editFrm__rdv_anesth">Date:</label></th>
          <td class="readonly">
            <input type="hidden" name="_date_rdv_anesth" value="{$op->_date_rdv_anesth}" />
            <input type="text" name="_rdv_anesth" value="{$op->_rdv_anesth}" readonly="readonly" />
            <a href="#" onClick="popCalendar('_rdv_anesth', '_rdv_anesth');">
              <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
            </a>
          </td>
        </tr>

        <tr>
          <th><label for="editFrm__hour_anesth">Heure:</label></th>
          <td>
            <select name="_hour_anesth">
            {foreach from=$hours item=hour}
              <option {if $op->_hour_anesth == $hour} selected="selected" {/if}>{$hour}</option>
            {/foreach}
            </select>
            :
            <select name="_min_anesth">
            {foreach from=$mins item=min}
              <option {if $op->_min_anesth == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>
        {/if}
        
        <tr><th class="category" colspan="3">Admission</th></tr>

        {if !$protocole}
        <tr>
          <th class="mandatory"><label for="editFrm__rdv_adm">Date:</label></th>
          <td class="readonly">
            <input type="hidden" name="_date_rdv_adm" value="{$op->_date_rdv_adm}" />
            <input type="text" name="_rdv_adm" value="{$op->_rdv_adm}" readonly="readonly" />
            <a href="#" onClick="popCalendar( '_rdv_adm', '_rdv_adm');">
              <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
            </a>
          </td>
        </tr>

        <tr>
          <th class="mandatory"><label for="editFrm__hour_adm">Heure:</label></th>
          <td>
            <select name="_hour_adm">
            {foreach from=$hours item=hour}
              <option {if $op->_hour_adm == $hour} selected="selected" {/if}>{$hour}</option>
            {/foreach}
            </select>
            :
            <select name="_min_adm">
            {foreach from=$mins item=min}
              <option {if $op->_min_adm == $min} selected="selected" {/if}>{$min}</option>
            {/foreach}
            </select>
          </td>
        </tr>
        {/if}

        <tr>
          <th><label for="editFrm_duree_hospi">Dur�e d'hospitalisation:</label></th>
          <td><input type"text" name="duree_hospi" size="1" value="{$op->duree_hospi}"> jours</td>
        </tr>
        <tr>
          <th><label for="editFrm_type_adm_comp">{tr}type_adm{/tr}:</label></th>
          <td>
            <input name="type_adm" value="comp" type="radio" {if !$op || $op->type_adm == "comp"} checked="checked" {/if} />
            <label for="editFrm_type_adm_comp">{tr}comp{/tr}</label><br />
            <input name="type_adm" value="ambu" type="radio" {if $op->type_adm == "ambu"} checked="checked" {/if} />
            <label for="editFrm_type_adm_ambu">{tr}ambu{/tr}</label><br />
            <input name="type_adm" value="exte" type="radio" {if $op->type_adm == "exte"} checked="checked" {/if} />
            <label for="editFrm_type_adm_exte">{tr}exte{/tr}</label><br />
          </td>
        </tr>
        
        {if !$protocole}
        <tr>
          <th><label for="editFrm_chambre_o">Chambre particuli�re:</label></th>
          <td>
            <input name="chambre" value="o" type="radio" {if !$op || $op->chambre == "o"} checked="checked" {/if}/>
            <label for="editFrm_chambre_o">Oui</label>
            <input name="chambre" value="n" type="radio" {if $op->chambre == "n"} checked="checked" {/if}/>
            <label for="editFrm_chambre_n">Non</label>
          </td>
        </tr>
        <tr><th class="category" colspan="3">Autre</th></tr>
        <tr>
          <th><label for="editFrm_ATNC_n">Risque ATNC:</th>
          <td>
            <input name="ATNC" value="o" type="radio" {if $op->ATNC == "o"} checked="checked" {/if} />
            <label for="editFrm_ATNC_o">Oui</label>
            <input name="ATNC" value="n" type="radio" {if !$op || $op->ATNC == "n"} checked="checked" {/if} />
            <label for="editFrm_ATNC_n">Non</label>
          </td>
        </tr>
        <tr>
          <th><label for="editFrm_rques">Remarques:</label></th>
          <td><textarea name="rques" rows="3">{$op->rques}</textarea></td>
        </tr>
        {/if}

      </table>
    
    </td>
  </tr>

  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
          {if $op}
            <input type="reset" value="R�initialiser" />
            <input type="submit" value="Modifier" />
            <input type="button" value="Supprimer" onclick="{literal}if (confirm('Veuillez confirmer la suppression')) {this.form.del.value = 1; this.form.submit();}{/literal}"/>
          {else}
            <input type="submit" value="Cr�er" />
          {/if}
          {if !$protocole}
            <input type="button" value="Imprimer" onClick="printForm()" />
          {/if}
          </td>
        </tr>
      </table>
    
    </td>
  </tr>

</table>

</form>
