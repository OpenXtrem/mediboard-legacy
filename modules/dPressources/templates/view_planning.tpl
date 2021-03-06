{literal}
<script language="JavaScript" type="text/javascript">

function alertAction() {
  if(confirm("Voulez confirmer votre action ?")) {
    return true;
  }
  return false;
}

function pageMain() {

  initGroups("impayes");
  initGroups("inf15");
  initGroups("sup15");
  
  regFieldCalendar("addPlage", "date");

  {/literal}
  regRedirectPopupCal("{$debut}", "index.php?m={$m}&tab={$tab}&debut=");
  {literal}
  
}

</script>
{/literal}

<table class="main">
  <tr>
    <th class="title">
      <a href="index.php?m={$m}&amp;debut={$prec}">&lt;&lt;&lt;</a>
      semaine du {$debut|date_format:"%A %d %B %Y"}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="index.php?m={$m}&amp;debut={$suiv}">&gt;&gt;&gt;</a>
    </th>
    <th class="title">Votre compte</th>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th></th>
          {foreach from=$plages key=curr_day item=plagesPerDay}
          <th>{$curr_day|date_format:"%A %d"}</th>
          {/foreach}
        </tr>
        {foreach from=$listHours item=curr_hour}
        <tr>
          <th>{$curr_hour}h</th>
          {foreach from=$plages key=curr_day item=plagesPerDay}
            {assign var="isNotIn" value=1}
            {foreach from=$plagesPerDay item=curr_plage}
              {if $curr_plage->_hour_deb == $curr_hour}
                {if ($curr_plage->_state == $smarty.const.PR_PAYED) && ($curr_plage->prat_id != $app->user_id)}
                <td align="center" bgcolor="{$smarty.const.PR_OUT}" rowspan="{$curr_plage->_hour_fin-$curr_plage->_hour_deb}">
                {else}
                <td style="vertical-align:middle; text-align:center; background-color:{$curr_plage->_state}" rowspan="{$curr_plage->_hour_fin-$curr_plage->_hour_deb}">
                {/if}
                  {if $curr_plage->prat_id == $app->user_id}
                  <font style="font-weight: bold; color: #060;">
                  {/if}
                  {if $curr_plage->libelle}
                    {$curr_plage->libelle}
                    <br />
                  {/if}
                  {$curr_plage->tarif} �
                  <br />
                  {$curr_plage->debut|date_format:"%H"}h - {$curr_plage->fin|date_format:"%H"}h
                  {if $curr_plage->prat_id}
                    <br />
                    Dr. {$curr_plage->_ref_prat->_view}
                  {/if}
                  {if $curr_plage->prat_id == $app->user_id}
                  </font>
                  {/if}
                  <br />
                  {if $isprat && (($curr_plage->_state == $smarty.const.PR_FREE) || (($curr_plage->_state == $smarty.const.PR_BUSY) && ($curr_plage->prat_id == $app->user_id)))}
                  <form name="editPlage{$curr_plage->plageressource_id}" action="?m={$m}" method="post" onSubmit=" return alertAction()">
                  <input type='hidden' name='dosql' value='do_plageressource_aed' />
                  <input type='hidden' name='del' value='0' />
                  <input type='hidden' name='plageressource_id' value='{$curr_plage->plageressource_id}' />
                    {if $curr_plage->_state == $smarty.const.PR_FREE}
                    <input type='hidden' name='prat_id' value='{$app->user_id}' />
                    <button type="submit">R�server</button>
                    {else}
                    <input type='hidden' name='prat_id' value='' />
                    <button type="submit">Annuler</button>
                    {/if}
                  </form>
                  {/if}
                </td>
              {/if}
              {if ($curr_plage->_hour_deb <= $curr_hour) && ($curr_plage->_hour_fin > $curr_hour)}
                {assign var="isNotIn" value=0}
              {/if}
            {/foreach}
            {if $isNotIn}
              <td bgcolor="#ffffff"></td>
            {/if}
          {/foreach}
        </tr>
        {/foreach}
      </table>
    </td>
    <td>
      <table class="form">
        {if $isprat}
        <tr class="groupcollapse" id="impayes" onclick="flipGroup('', 'impayes')">
          <th style="background:#ddf">Plages � r�gler:</th>
          <td>{$compte.impayes.total} ({$compte.impayes.somme} �)</td>
        </tr>
        {foreach from=$compte.impayes.plages item=curr_plage}
          <tr class="impayes">
            <td colspan="2" class="text">
              <a href="index.php?m={$m}&amp;debut={$curr_plage->date|date_format:"%Y-%m-%d"}">
              {$curr_plage->date|date_format:"%A %d %B %Y"} &mdash;
              {if $curr_plage->libelle}
                {$curr_plage->libelle} &mdash;
              {/if}
              de {$curr_plage->debut|date_format:"%H"}h � {$curr_plage->fin|date_format:"%H"}h &mdash;
              {$curr_plage->tarif} �
              </a>
            </td>
          </tr>
        {/foreach}
        <tr class="groupcollapse" id="inf15" onclick="flipGroup('', 'inf15')">
          <th style="background:#ddf">Plages r�serv�es et bloqu�es:</th>
          <td>{$compte.inf15.total} ({$compte.inf15.somme} �)</td>
        </tr>
        {foreach from=$compte.inf15.plages item=curr_plage}
          <tr class="inf15">
            <td colspan="2" class="text">
              <a href="index.php?m={$m}&amp;debut={$curr_plage->date|date_format:"%Y-%m-%d"}">
              {$curr_plage->date|date_format:"%A %d %B %Y"} &mdash;
              {if $curr_plage->libelle}
                {$curr_plage->libelle} &mdash;
              {/if}
              de {$curr_plage->debut|date_format:"%H"}h � {$curr_plage->fin|date_format:"%H"}h &mdash;
              {$curr_plage->tarif} �
              </a>
            </td>
          </tr>
        {/foreach}
        <tr class="groupcollapse" id="sup15" onclick="flipGroup('', 'sup15')">
          <th style="background:#ddf">Plages r�serv�es � plus de 15 jours:</th>
          <td>{$compte.sup15.total} ({$compte.sup15.somme} �)</td>
        </tr>
        {foreach from=$compte.sup15.plages item=curr_plage}
          <tr class="sup15">
            <td colspan="2" class="text">
              <a href="index.php?m={$m}&amp;debut={$curr_plage->date|date_format:"%Y-%m-%d"}">
              {$curr_plage->date|date_format:"%A %d %B %Y"} &mdash;
              {if $curr_plage->libelle}
                {$curr_plage->libelle} &mdash;
              {/if}
              de {$curr_plage->debut|date_format:"%H"}h � {$curr_plage->fin|date_format:"%H"}h &mdash;
              {$curr_plage->tarif} �
              </a>
            </td>
          </tr>
        {/foreach}
        {/if}
        <tr>
          <th colspan="2" class="category">L�gende</th>
        </tr>
        <tr>
          <th style="background:{$smarty.const.PR_OUT}" />
          <td class="text">Plage termin�e</td>
        </tr>
        <tr>
          <th style="background:{$smarty.const.PR_FREE}" />
          <td class="text">Plage libre</td>
        </tr>
        <tr>
          <th style="background:{$smarty.const.PR_FREEB}" />
          <td class="text">Plage libre non r�servable (dans plus d'1 mois)</td>
        </tr>
        <tr>
          <th style="background:{$smarty.const.PR_BUSY}" />
          <td class="text">Plage r�serv�e (ech�ance dans plus de 15 jours)</td>
        </tr>
        <tr>
          <th style="background:{$smarty.const.PR_BLOCKED}" />
          <td class="text">Plage bloqu�e (�ch�ance dans moins de 15 jours)</td>
        </tr>
        <tr>
          <th style="background:{$smarty.const.PR_PAYED}" />
          <td class="text">Plage r�gl�e</td>
        </tr>
        <tr>
          <th style="font-weight: bold; color: #060;">Dr. {$prat->_view}</th>
          <td class="text">Plage vous appartenant</td>
        </tr>
      </table>
    </td>
  </tr>
</table>