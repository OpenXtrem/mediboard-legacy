<?php /* Smarty version 2.6.3, created on 2004-12-13 15:35:13
         compiled from print_planning.tpl */ ?>
<!-- $Id$ -->

<?php echo '
<script language="javascript">
function checkForm() {
  var form = document.paramFrm;
    
  if (form.debut.value > form.fin.value) {
    alert("Date de d�but superieure � la date de fin");
    return false;
  }
  popPlanning();
}

var calendarField = \'\';
var calWin = null;

function popCalendar( field ){
  calendarField = field;
  idate = eval( \'document.paramFrm.date_\' + field + \'.value\' );
  window.open( \'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=\' + idate, \'calwin\', \'top=250,left=250,width=280, height=250, scollbars=false\' );
}

function setCalendar( idate, fdate ) {
  fld_date = eval( \'document.paramFrm.date_\' + calendarField );
  fld_fdate = eval( \'document.paramFrm.\' + calendarField );
  fld_date.value = idate;
  fld_fdate.value = fdate;
}

function popCode(type) {
  var chir = document.paramFrm.chir.value;
  window.open(\'./index.php?m=dPbloc&a=code_selector&dialog=1&type=\'+type+\'&chir=\'+chir, type, \'left=50,top=50,height=500,width=600,resizable\');
}

function setCode( key, type ){
  var f = document.paramFrm;
   if (key != \'\') {
    if(type == \'ccam\'){
      f.CCAM_code.value = key;
        window.CCAM_code = key;
    }
    else{
      f.CIM10_code.value = key;
        window.CIM10_code = key;
    }
  }
}

function popPlanning() {
  var debut = document.paramFrm.date_debut.value;
  var fin = document.paramFrm.date_fin.value;
  var vide = document.paramFrm.vide.checked;
  var type = document.paramFrm.type.value;
  var chir = document.paramFrm.chir.value;
  var salle = document.paramFrm.salle.value;
  var CCAM = document.paramFrm.CCAM_code.value;
  var url = \'./index.php?m=dPbloc&a=view_planning&dialog=1\';
  url = url + \'&debut=\' + debut;
  url = url + \'&fin=\' + fin;
  url = url + \'&vide=\' + vide;
  url = url + \'&type=\' + type;
  url = url + \'&chir=\' + chir;
  url = url + \'&salle=\' + salle;
  url = url + \'&CCAM=\' + CCAM;
  window.open(url, \'Planning\', \'left=10,top=10,height=550,width=700,resizable=1,scrollbars=1\');
}
</script>
'; ?>


<form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkForm()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix de la periode</th></tr>
        <tr>
		      <th>D�but:</th>
          <td class="readonly">
            <input type="hidden" name="date_debut" value="<?php echo $this->_tpl_vars['todayi']; ?>
" />
            <input type="text" name="debut" value="<?php echo $this->_tpl_vars['todayf']; ?>
" readonly="readonly" />
            <a href="#" onClick="popCalendar( 'debut', 'debut');">
              <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
            </a>
          </td>
        </tr>
        <tr>
		      <th>Fin:</th>
          <td class="readonly">
            <input type="hidden" name="date_fin" value="<?php echo $this->_tpl_vars['todayi']; ?>
" />
            <input type="text" name="fin" value="<?php echo $this->_tpl_vars['todayf']; ?>
" readonly="readonly" />
            <a href="#" onClick="popCalendar( 'fin', 'fin');">
              <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
            </a>
          </td>
        </tr>
        <tr>
          <th>Afficher les plages vides</th>
          <td><input type="checkbox" name="vide" /></td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix des param�tres de tri</th></tr>
        <tr>
          <th>Affichage des interventions:</th>
          <td colspan="2"><select name="type">
            <option value="0">-- Toutes</option>
            <option value="1">ins�r�es dans le planning</option>
            <option value="2">� ins�rer dans le planning</option>
          </select></td>
        </tr>
        <tr>
          <th>Chirurgien:</th>
          <td colspan="2"><select name="chir">
            <option value="0">-- Tous</option>
            <?php if (count($_from = (array)$this->_tpl_vars['listChir'])):
    foreach ($_from as $this->_tpl_vars['curr_chir']):
?>
	            <option value="<?php echo $this->_tpl_vars['curr_chir']['id']; ?>
"><?php echo $this->_tpl_vars['curr_chir']['lastname']; ?>
 <?php echo $this->_tpl_vars['curr_chir']['firstname']; ?>
</option>
		        <?php endforeach; unset($_from); endif; ?>
          </select></td>
        </tr>
        <tr>
          <th>Salle:</th>
          <td colspan="2"><select name="salle">
            <option value="0">-- Toutes</option>
            <?php if (count($_from = (array)$this->_tpl_vars['listSalles'])):
    foreach ($_from as $this->_tpl_vars['curr_salle']):
?>
	            <option value="<?php echo $this->_tpl_vars['curr_salle']['id']; ?>
"><?php echo $this->_tpl_vars['curr_salle']['nom']; ?>
</option>
		        <?php endforeach; unset($_from); endif; ?>
          </select></td>
        </tr>
        <tr>
          <th>Code CCAM:</th>
          <td><input type="text" name="CCAM_code" size="10" value="" /></td>
          <td class="button"><input type="button" value="selectionner un code" onclick="popCode('ccam')"/></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form"><tr><td class="button"><input type="button" value="Afficher" onclick="checkForm()"</td></tr></table>

    </td>
  </tr>
</table>