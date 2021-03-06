{literal}
<script language="JavaScript" type="text/javascript">

function checkForm() {
  var form = document.paramFrm;
    
  if (form.deb.value > form.fin.value) {
    alert("Date de d�but superieure � la date de fin");
    return false;
  }

  popRapport();
}

function popRapport() {
  var form = document.paramFrm;
  var url = new Url();
  url.setModuleAction("dPressources", "print_rapport");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.type);
  url.addElement(form.prat_id);
  url.popup(700, 550, "Rapport");
}

function pageMain() {
  initGroups("plages");
  regFieldCalendar("paramFrm", "deb");
  regFieldCalendar("paramFrm", "fin");  
}

</script>
{/literal}

<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="3" class="title">Plages en attente de paiement &mdash; {$today|date_format:"%A %d %B %Y"}</th>
        </tr>
        <tr>
          <th>Praticien</th>
          <th>Quantit�</th>
          <th>Montant</th>
        </tr>
        {foreach from=$list item=curr_prat}
        <tr class="groupcollapse" id="plages{$curr_prat.prat_id}" onclick="flipGroup('{$curr_prat.prat_id}', 'plages')">
          <td>Dr. {$curr_prat.praticien->_view}</td>
          <td>{$curr_prat.total} plage(s)</td>
          <td>{$curr_prat.somme} �</td>
        </tr>
          {foreach from=$curr_prat.plages item=curr_plage}
            <tr class="plages{$curr_prat.prat_id}">
              <td>
                <form name="editPlage{$curr_plage->plageressource_id}" action="?m={$m}" method="post">
                <input type='hidden' name='dosql' value='do_plageressource_aed' />
                <input type='hidden' name='del' value='0' />
                <input type='hidden' name='plageressource_id' value='{$curr_plage->plageressource_id}' />
                <input type='hidden' name='paye' value='1' />
                <button type="submit">Valider le paiement</button>
                </form>
              </td>
              <td>{$curr_plage->date|date_format:"%A %d %B %Y"}</td>
              <td>{$curr_plage->tarif} �</td>
            </tr>
          {/foreach}
        {/foreach}
        <tr>
          <th>{$total.prat} praticien(s)</th>
          <th>{$total.total} plage(s)</th>
          <th>{$total.somme} �</th>
      </table>

    </td>
    <td>
    
      <form name="paramFrm" action="?m=dPressources" method="post" onsubmit="return checkForm()">
      <table class="form">
        <tr>
          <th class="title" colspan="2">Edition des rapports</th>
        </tr>
        <tr>
          <th><label for="deb">D�but:</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_deb_da">{$today|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="deb" value="{$today}" />
            <img id="paramFrm_deb_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <th><label for="fin">Fin:</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_fin_da">{$today|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="fin" value="{$today}" />
            <img id="paramFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <th><label for="type">Filtre:</label></th>
          <td>
            <select name="type">
              <option value="0">Plages non pay�es</option>
              <option value="1">Plages pay�es</option>
            </select>
        </tr>
        <tr>
          <th><label for="prat_id">Praticien:</label></th>
          <td>
            <select name="prat_id">
              {foreach from=$listPrats item=curr_prat}
              <option value="{$curr_prat->user_id}">{$curr_prat->_view}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button"><button type="button" onclick="checkForm()">Afficher</button></td>
        </tr>
      </table>
      </form>

    </td>
  </tr>
</table>