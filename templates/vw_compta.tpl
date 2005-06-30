<!-- $Id$ -->

{literal}
<script language="javascript">
function checkRapport(){
  var form = document.printFrm;
    
  if (form.date_debut_rapport.value > form.date_fin_rapport.value) {
    alert("Date de d�but superieure � la date de fin");
    return false;
  }

  var url = './index.php?m=dPcabinet&a=print_rapport&dialog=1';
  url += '&debut_rapport=' + form.date_debut_rapport.value;
  url += '&fin_rapport='   + form.date_fin_rapport.value;
  url += '&chir='  + form.chir.value;
  url += '&etat='  + form.etat.value;
  url += '&type='  + form.type.value;
  url += '&aff='   + form.aff.value;

  popup(700, 550, url, 'Rapport');
  
  return false;
}

function popCalendar( field ) {
  calendarField = field;
  idate = eval( 'document.printFrm.date_' + field + '.value' );
  var url = "index.php?m=public&a=calendar&dialog=1&callback=setCalendar";
  url += "&date=" + idate;
  popup(280, 250, url, 'calwin');
}

function setCalendar( idate, fdate ) {
  fld_date = eval( 'document.printFrm.date_' + calendarField );
  fld_fdate = eval( 'document.printFrm.' + calendarField );
  fld_date.value = idate;
  fld_fdate.value = fdate;
}
</script>
{/literal}

<table class="main"><tr>

  <td class="halfPane">
  <form name="printFrm" action="./index.php" method="get" onSubmit="return checkRapport()">
  <input type="hidden" name="a" value="print_rapport" />
  <input type="hidden" name="dialog" value="1" />
  <table class="form">
    <tr><th class="title" colspan="2">Edition de rapports</th></tr>
    <tr><th class="category" colspan="2">Choix de la periode</th></tr>
    <tr>
      <th><label for="paramFrm_debut_rapport">D�but:</label></th>
      <td class="readonly" colspan="2">
        <input type="hidden" name="date_debut_rapport" value="{$todayi}" />
        <input type="text" name="debut_rapport" value="{$todayf}" readonly="readonly" />
        <a href="#" onClick="popCalendar( 'debut_rapport', 'debut_rapport');">
          <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
        </a>
      </td>
    </tr>
    <tr>
      <th><label for="paramFrm_fin_rapport">Fin:</label></th>
      <td class="readonly" colspan="2">
        <input type="hidden" name="date_fin_rapport" value="{$todayi}" />
        <input type="text" name="fin_rapport" value="{$todayf}" readonly="readonly" />
        <a href="#" onClick="popCalendar( 'fin_rapport', 'fin_rapport');">
          <img src="./images/calendar.gif" width="24" height="12" alt="Choisir une date" />
        </a>
      </td>
    </tr>
    <tr><th class="category" colspan="2">Options sur le rapport</th></tr>
    <tr><th>Praticien :</th>
      <td><select name="chir">
        <!-- <option value="0">&mdash; Tous &mdash;</option> -->
        {foreach from=$listPrat item=curr_prat}
        <option value="{$curr_prat->user_id}">{$curr_prat->user_last_name} {$curr_prat->user_first_name}</option>
        {/foreach}
    <tr><th>Etat des paiements :</th>
      <td><select name="etat">
        <option value="-1">&mdash; Tous &mdash;</option>
        <option value="1">Pay�s</option>
        <option value="0">Impay�s</option>
      </select></td>
    </tr>
    <tr><th>Type de paiement :</th>
      <td><select name="type">
        <option value="0">Tout type</option>
        <option value="cheque">Ch�ques</option>
        <option value="CB">CB</option>
        <option value="especes">Esp�ces</option>
        <option value="tiers">Tiers-payant</option>
        <option value="autre">Autre</option>
      </select></td>
    </tr>
    <tr><th>Type d'affichage :</th>
      <td><select name="aff">
        <option value="1">Liste compl�te</option>
        <option value="0">Totaux</option>
      </select></td>
    </tr>
    <tr><td class="button" colspan="2"><input type="submit" value="imprimer" /></td></tr>
  </table></form></td>

  <td class="halfPane"><table align="center">

    {if $tarif->tarif_id}
    <tr><td colspan="3"><a href="index.php?m={$m}&amp;tarif_id=null"><b>Cr�er un nouveau tarif</b></a></td</tr>
    {/if}

    <tr><td><table class="tbl">
      <tr><th colspan="3">Tarifs du praticien</th></tr>
      <tr><th>Nom</th><th>Secteur 1</th><th>Secteur 2</th></tr>
      {foreach from=$listeTarifsChir item=curr_tarif}
      <tr>
        <td><a href="index.php?m={$m}&amp;tarif_id={$curr_tarif->tarif_id}">{$curr_tarif->description}</a></td>
        <td><a href="index.php?m={$m}&amp;tarif_id={$curr_tarif->tarif_id}">{$curr_tarif->secteur1} �</a></td>
        <td><a href="index.php?m={$m}&amp;tarif_id={$curr_tarif->tarif_id}">{$curr_tarif->secteur2} �</a></td>
      </tr>
      {/foreach}
    </table></td>

    <td><table class="tbl">
      <tr><th colspan="3">Tarifs du cabinet</th></tr>
      <tr><th>Nom</th><th>Secteur 1</th><th>Secteur 2</th></tr>
      {foreach from=$listeTarifsSpe item=curr_tarif}
      <tr>
        <td><a href="index.php?m={$m}&amp;tarif_id={$curr_tarif->tarif_id}">{$curr_tarif->description}</a></td>
        <td><a href="index.php?m={$m}&amp;tarif_id={$curr_tarif->tarif_id}">{$curr_tarif->secteur1} �</a></td>
        <td><a href="index.php?m={$m}&amp;tarif_id={$curr_tarif->tarif_id}">{$curr_tarif->secteur2} �</a></td>
      </tr>
      {/foreach}
    </table></td>

    <td>
      <form name="editFrm" action="./index.php?m={$m}" method="post">
      <input type="hidden" name="dosql" value="do_tarif_aed" />
      <input type="hidden" name="tarif_id" value="{$tarif->tarif_id}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="chir_id" value="{if $tarif->tarif_id}{$tarif->chir_id}{else}{$mediuser->user_id}{/if}" />
      <input type="hidden" name="function_id" value="{if $tarif->tarif_id}{$tarif->function_id}{else}{$mediuser->function_id}{/if}" />
      <table class="form">
        {if $tarif->tarif_id}
        <tr><th class="category" colspan="2">Editer ce tarif</th></tr>
        {else}
        <tr><th class="category" colspan="2">Cr�er un nouveau tarif</th></tr>
        {/if}
        <tr>
          <th>Type :</th>
          <td><select name="_type">
            <option value="chir" {if $tarif->chir_id} selected="selected" {/if}>Tarif personnel</option>
            <option value="function" {if $tarif->function_id} selected="selected" {/if}>Tarif de cabinet</option>
          </select></td>
        </tr>
        <tr><th>Nom :</th>
          <td><input type="text" name="description" value="{$tarif->description}" /></td></tr>
        <tr><th>Secteur1 :</th>
          <td><input type="text" name="secteur1" value="{$tarif->secteur1}" size="6" /> �</td></tr>
        <tr><th>Secteur2 :</th>
          <td><input type="text" name="secteur2" value="{$tarif->secteur2}" size="6" /> �</td></tr>
        <tr><td class="button" colspan="2">
          {if $tarif->tarif_id}
          <input type="submit" value="Modifier" />
          <input type="button" value="Supprimer" onclick="confirmDeletion(this.form, 'le tarif', '{$tarif->description|escape:javascript}')" />
          {else}
          <input type="submit" name="btnFuseAction" value="Cr�er">
          {/if}
        </td></tr>
      </table>
      </form>
    </td></tr>

  </table></td>

</tr></table>