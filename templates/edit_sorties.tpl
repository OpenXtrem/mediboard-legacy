{literal}
<script language="javascript">
</script>
{/literal}
<table class="main">
<tr>
<td>
<form name="typeVue" action="?m={$m}" method="get">
<input type="hidden" name="m" value="{$m}" />
<select name="vue" onchange="submit()">
  <option value="0" {if $vue == 0}selected="selected"{/if}>Tout afficher</option>
  <option value="1" {if $vue == 1}selected="selected"{/if}>Ne pas afficher les validés</option>
</select>
</form>
</td>

<th>
  {assign var="mypmonth" value=$pday|date_format:"%m"}
  {assign var="mynmonth" value=$nday|date_format:"%m"}
  {assign var="mynowmonth" value=$now|date_format:"%m"}
  <a href="index.php?m={$m}&amp;day={$pday|date_format:"%d"}&amp;month={$mypmonth-1}&amp;year={$pday|date_format:"%Y"}"><<</a>
  {$cday|date_format:"%A %d %B %Y"}
  <a href="index.php?m={$m}&amp;day={$nday|date_format:"%d"}&amp;month={$mynmonth-1}&amp;year={$nday|date_format:"%Y"}">>></a>
</th>

<th>
  <a href="index.php?m={$m}&amp;day={$now|date_format:"%d"}&amp;month={$mynowmonth-1}&amp;year={$now|date_format:"%Y"}">Aujourd'hui : {$now|date_format:"%A %d %B %Y"}</a>
</th>
<tr>
<td colspan="3">

<table><tr><td>

<table class="tbl">
  <tr>
    <th class="title" colspan="6">Confirmation des déplacements</th>
  </tr>
  <tr>
    <th>Confirmation</th>
    <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=0">Patient</a></th>
    <th>Praticien</th>
    <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=1">Origine</a></th>
    <th>Destination</th>
    <th>Heure prévue</th>
  </tr>
  {foreach from=$deplacements item=curr_sortie}
  <tr>
    <td>
    <form name="editFrm{$curr_sortie->affectation_id}" action="?m={$m}" method="post">
    <input type="hidden" name="m" value="{$m}" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{$curr_sortie->affectation_id}" />
    {if $curr_sortie->confirme}
    <input type="hidden" name="confirme" value="0" />
    <input type="hidden" name="effectue" value="0" />
    <button type="submit">
    <img src="modules/{$m}/images/cross.png" alt="Annuler" title="Annuler le déplacement">
    Annuler le déplacement
    </button>
    {else}
    <input type="hidden" name="confirme" value="1" />
    <input type="hidden" name="effectue" value="1" />
    <button type="submit">
    <img src="modules/{$m}/images/tick.png" alt="Confirmer" title="Confirmer le déplacement">
    Confirmer le déplacement
    </button>
    {/if}
    </form>
    </td>
    {if $curr_sortie->confirme}
    <td class="text" style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
    {else}
    <td class="text">
    {/if}
      <b>{$curr_sortie->_ref_operation->_ref_pat->_view}</b>
    </td>
    <td class="text" style="background:#{$curr_sortie->_ref_operation->_ref_chir->_ref_function->color}">
      {$curr_sortie->_ref_operation->_ref_chir->_view}
    </td>
    <td class="text">
      {$curr_sortie->_ref_lit->_ref_chambre->_ref_service->nom} -
      {$curr_sortie->_ref_lit->_ref_chambre->nom} -
      {$curr_sortie->_ref_lit->nom}
    </td>
    <td class="text">
      {$curr_sortie->_ref_next->_ref_lit->_ref_chambre->_ref_service->nom} -
      {$curr_sortie->_ref_next->_ref_lit->_ref_chambre->nom} -
      {$curr_sortie->_ref_next->_ref_lit->nom}
    </td>
    <td>{$curr_sortie->sortie|date_format:"%H h %M"}</td>
  </tr>
  {/foreach}
</table>

</td><td>

<table class="tbl">
  <tr>
    <th class="title" colspan="5">Confirmation des sorties</th>
  </tr>
  <tr>
    <th>Confirmation</th>
    <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=0">Patient</a></th>
    <th>Praticien</th>
    <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;typeOrder=1">Service</a></th>
    <th>Chambre</th>
  </tr>
  {foreach from=$sorties item=curr_sortie}
  <tr>
    <td>
    <form name="editFrm{$curr_sortie->affectation_id}" action="?m={$m}" method="post">
    <input type="hidden" name="m" value="{$m}" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{$curr_sortie->affectation_id}" />
    {if $curr_sortie->confirme}
    <input type="hidden" name="confirme" value="0" />
    <button type="submit">
    <img src="modules/{$m}/images/cross.png" alt="Annuler" title="Annuler la sortie">
    Annuler la sortie
    </button>
    {else}
    <input type="hidden" name="confirme" value="1" />
    <button type="submit">
    <img src="modules/{$m}/images/tick.png" alt="Confirmer" title="Confirmer la sortie">
    Confirmer la sortie
    </button>
    {/if}
    </form>
    </td>
    {if $curr_sortie->confirme}
    <td class="text" style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
    {else}
    <td class="text">
    {/if}
      <b>{$curr_sortie->_ref_operation->_ref_pat->_view}</b>
    </td>
    <td class="text" style="background:#{$curr_sortie->_ref_operation->_ref_chir->_ref_function->color}">
      {$curr_sortie->_ref_operation->_ref_chir->_view}
    </td>
    <td class="text">
      {$curr_sortie->_ref_lit->_ref_chambre->_ref_service->nom} -
      {$curr_sortie->_ref_lit->_ref_chambre->nom} -
      {$curr_sortie->_ref_lit->nom}
    </td>
    <td>{$curr_sortie->sortie|date_format:"%H h %M"}</td>
  </tr>
  {/foreach}
</table>

</td></tr></table>

</td>
</tr>
</table>