<!-- $Id$ -->

{literal}
<script language="javascript">
function checkForm() {
  var form = document.editFrm;
    
  if (form.chir_name.value.length == 0) {
    alert("Chirurgien manquant");
    popChir();
    return false;
  }
    
  if (form.pat_name.value.length == 0) {
    alert("Patient manquant");
    popPat();
    return false;
  }
/* Bug in IE
  if (form.hour_op.value == 0 && form.min_op.value == 0) {
    alert("Temps op�ratoire invalide");
    form.hour_op.focus();
    return false;
  }
*/
  if (form.plageop_id.value == 0) {
    alert("Intervention non planifi�e");
    popPlage();
    return false;
  }

  return true;
}

function popChir() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=chir_selector';
  url += '&dialog=1';
  
  window.open(url, 'Chirurgien', 'left=50,top=50,height=250,width=400,resizable');
}

function popPat() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=pat_selector';
  url += '&dialog=1';

  window.open(url, 'Patient', 'left=50,top=50,height=250,width=400,resizable');
}

function popCode(type) {
  var url = './index.php?m=dPplanningOp';
  url += '&a=code_selector';
  url += '&dialog=1';
  url += '&chir='+ document.editFrm.chir_id.value;
  url += '&type='+ type;

  window.open(url, 'CIM10', 'left=50,top=50,height=500,width=600,resizable');
}

function popPlage() {
  var url = './index.php?m=dPplanningOp';
  url += '&a=plage_selector';
  url += '&dialog=1';
  url += '&chir=' + document.editFrm.chir_id.value;
  url += '&hour=' + document.editFrm.hour_op.value;
  url += '&min=' + document.editFrm.min_op.value;

  window.open(url, 'Plage', 'left=50,top=50,height=250,width=400,resizable');
}

function setChir( key, val ){
  var f = document.editFrm;
   if (val != '') {
    f.chir_id.value = key;
      f.chir_name.value = val;
      window.chir_id = key;
      window.chir_name = val;
    }
}

function setPat( key, val ){
  var f = document.editFrm;

  if (val != '') {
    f.pat_id.value = key;
    f.pat_name.value = val;
    window.pat_id = key;
    window.pat_name = val;
  }
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

function setPlage( key, val ){
  var f = document.editFrm;

  if (key != '') {
    f.plageop_id.value = key
    f.date.value = val;
    window.plageop_id = key;
    window.date = val;
  }
}

var calendarField = '';
var calWin = null;
 
function popCalendar( field ){
  calendarField = field;
  idate = eval( 'document.editFrm.date_' + field + '.value' );
  
  var url =  'index.php?m=public';
  url += '&a=calendar';
  url += '&dialog=1';
  url += '&callback=setCalendar';
  url += '&date=' + idate;
  
  window.open(url, 'calwin', 'top=250,left=250,width=280, height=250, scollbars=false' );
}

function setCalendar( idate, fdate ) {
  fld_date = eval( 'document.editFrm.date_' + calendarField );
  fld_fdate = eval( 'document.editFrm.' + calendarField );
  fld_date.value = idate;
  fld_fdate.value = fdate;
}
	
function printForm() {
  if (checkForm()) {
    url = './index.php?m=dPplanningOp';
    url += '&a=view_planning';
    url += '&dialog=1';
    url += '&chir_id='     + eval('document.editFrm.chir_id.value'    );
    url += '&pat_id='      + eval('document.editFrm.pat_id.value'     );
    url += '&CCAM_code='   + eval('document.editFrm.CCAM_code.value'  );
    url += '&cote='        + eval('document.editFrm.cote.value'       );
    url += '&hour_op='     + eval('document.editFrm.hour_op.value'    );
    url += '&min_op='      + eval('document.editFrm.min_op.value'     );
    url += '&date='        + eval('document.editFrm.date.value'       );
    url += '&info='        + eval('document.editFrm.info.value'       );
    url += '&rdv_anesth='  + eval('document.editFrm.rdv_anesth.value' );
    url += '&hour_anesth=' + eval('document.editFrm.hour_anesth.value');
    url += '&min_anesth='  + eval('document.editFrm.min_anesth.value' );
    url += '&rdv_adm='     + eval('document.editFrm.rdv_adm.value'    );
    url += '&hour_adm='    + eval('document.editFrm.hour_adm.value'   );
    url += '&min_adm='     + eval('document.editFrm.min_adm.value'    );
    url += '&duree_hospi=' + eval('document.editFrm.duree_hospi.value');
    url += '&type_adm='    + eval('document.editFrm.type_adm.value'   );
    url += '&chambre='     + eval('document.editFrm.chambre.value'    ); 
 
    window.open( url, 'printAdm', 'top=10,left=10,width=800, height=600, scollbars=true' );
  }
}
</script>
{/literal}

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="chir_id" value="{$chir.id}" />
<input type="hidden" name="pat_id" value="" />
<input type="hidden" name="rank" value="0" />

<table class="main">
  <tr>
    <td>
	
      <table class="form">
        <tr><th class="category" colspan="3">Informations concernant l'op�ration</th></tr>

        <tr>
		      <th class="mandatory">Chirurgien:</th>
          <td class="readonly"><input type="text" name="chir_name" size="30" value="{$chir.name}" readonly="readonly" /></td>
          <td class="button"><input type="button" value="choisir un chirurgien" onclick="popChir()"></td>
        </tr>

        {if (!$protocole)}
        <tr>
          <th class="mandatory">Patient:</th>
          <td class="readonly"><input type="text" name="pat_name" size="30" value="" readonly="readonly" /></td>
          <td class="button"><input type="button" value="rechercher un patient" onclick="popPat()" /></td>
        </tr>
        {/if}
        
        <tr>
          <th class="mandatory">Diagnostic (CIM10):</th>
          <td><input type="text" name="CIM10_code" size="10" value="" /></td>
          <td class="button"><input type="button" value="selectionner un code" onclick="popCode('cim10')" /></td>
        </tr>

        <tr>
          <th class="mandatory">Acte m�dical (CCAM):</th>
          <td><input type="text" name="CCAM_code" size="10" value="" /></td>
          <td class="button"><input type="button" value="selectionner un code" onclick="popCode('ccam')"/></td>
        </tr>

        <tr>
          <th>Cot�:</th>
          <td colspan=2>
            <select name="cote">
        	  <option selected>total</option>
        	  <option>droit</option>
        	  <option>gauche</option>
        	  <option>bilat�ral</option>
        	</select>
          </td>
        </tr>

        <tr>
          <th class="mandatory">Temps op�ratoire:</th>
          <td colspan="2">
            <select name="hour_op">
              <option>0</option>
              <option selected>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
            </select>
            :
            <select name="min_op">
              <option selected>00</option>
              <option>15</option>
              <option>30</option>
              <option>45</option>
            </select>
          </td>
        </tr>

        {if (!$protocole)}
        <tr>
          <th class="mandatory">Date de l'intervention:</th>
          <td class="readonly">
            <input type="hidden" name="plageop_id" value="" />
            <input type="text" name="date" readonly="readonly" size="10" value="" />
          </td>
          <td class="button"><input type="button" value="choisir une date" onclick="popPlage()" /></td>
        </tr>
        {/if}
        
        <tr>
          <th>Examens compl�mentaires:</th>
          <td colspan="2"><textarea name="examen" rows="3"></textarea></td>
        </tr>

        {if (!$protocole)}
        <tr>
          <th>Mat�riel � pr�voir:</th>
          <td colspan="2"><textarea name="materiel" rows="3"></textarea></td>
        </tr>

        <tr>
          <th>Information du patient:</th>
          <td  colspan="2">
            <input name="info" value="o" type="radio" />Oui
            <input name="info" value="n" type="radio" checked="checked" />Non
          </td>
        </tr>
        {/if}

      </table>

    </td>
    <td>

      <table class="form">
        {if (!$protocole)}
        <tr><th class="category" colspan="3">RDV d'anesth�sie</th></tr>

        <tr>
          <th>Date:</th>
          <td class="readonly">
            <input type="hidden" name="date_rdv_anesth" value="{$todayi}" />
            <input type="text" name="rdv_anesth" value="{$todayf}" readonly="readonly" />
            <a href="#" onClick="popCalendar( 'rdv_anesth', 'rdv_anesth');">
              <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
            </a>
          </td>
        </tr>

        <tr>
          <th>Heure:</th>
          <td>
            <select name="hour_anesth">
              <option>08</option>
              <option>09</option>
              <option>10</option>
              <option>11</option>
              <option>12</option>
              <option>13</option>
              <option>14</option>
              <option>15</option>
              <option>16</option>
              <option>17</option>
              <option>18</option>
              <option>19</option>
            </select>
            :
            <select name="min_anesth">
              <option>00</option>
              <option>15</option>
              <option>30</option>
              <option>45</option>
            </select>
          </td>
        </tr>
        {/if}
        
        <tr><th class="category" colspan="3">Admission</th></tr>

        {if (!$protocole)}
        <tr>
          <th class="mandatory">Date:</th>
          <td class="readonly">
            <input type="hidden" name="date_rdv_adm" value="{$todayi}" />
            <input type="text" name="rdv_adm" value="{$todayf}" readonly="readonly" />
            <a href="#" onClick="popCalendar( 'rdv_adm', 'rdv_adm');">
              <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
            </a>
          </td>
        </tr>

        <tr>
          <th class="mandatory">Heure:</th>
          <td>
            <select name="hour_adm">
			        <option>07</option>
              <option>08</option>
              <option>09</option>
              <option>10</option>
              <option>11</option>
              <option>12</option>
              <option>13</option>
              <option>14</option>
              <option>15</option>
              <option>16</option>
              <option>17</option>
              <option>18</option>
              <option>19</option>
            </select>
            :
            <select name="min_adm">
              <option>00</option>
              <option>15</option>
              <option>30</option>
              <option>45</option>
            </select>
          </td>
        </tr>
        {/if}

        <tr>
          <th>Dur�e d'hospitalisation:</th>
          <td><input type"text" name="duree_hospi" size="1" value="0">jours</td>
        </tr>
        <tr>
          <th>Admission en:</th>
          <td>
            <input name="type_adm" value="comp" type="radio" checked="checked" />hospitalisation compl�te<br />
            <input name="type_adm" value="ambu" type="radio" />Ambulatoire<br />
			      <input name="type_adm" value="exte" type="radio" />Externe
          </td>
        </tr>
        
        {if (!$protocole)}
        <tr>
          <th>Chambre particuli�re:</th>
          <td>
            <input name="chambre" value="o" type="radio" checked="checked" />Oui
            <input name="chambre" value="n" type="radio" />Non
          </td>
        </tr>
        <tr><th class="category" colspan="3">Autre</th></tr>
        <tr>
          <th>Risque ATNC:</th>
          <td>
            <input name="ATNC" value="o" type="radio" />Oui
            <input name="ATNC" value="n" type="radio" checked="checked" />Non
          </td>
        </tr>
        <tr>
          <th>Remarques:</th>
          <td><textarea name="rques" rows="3"></textarea></td>
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
            <input class="button" type="submit" value="Cr�er" />
            <input class="button" type="button" value="Imprimer" onClick="printForm()" />
          </td>
        </tr>
      </table>
    
    </td>
  </tr>

</table>

</form>
