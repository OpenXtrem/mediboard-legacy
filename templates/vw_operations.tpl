<table class="main">
  <tr>
    <td>
      <table>
        <tr>
          <td>
            <form action="index.php" target="_self" name="selection" method="get" encoding="">
            <input type="hidden" name="m" value="{$m}">
            <input type="hidden" name="tab" value="{$t}">
            Choisir une salle :
            <select name="salle" onchange="this.form.submit()">
              <option value="0">Aucune salle</option>
              {foreach from=$listSalles item=curr_salle}
              <option value="{$curr_salle->id}" {if $curr_salle->id == $salle} selected="selected" {/if}>
                {$curr_salle->nom}
              </option>
              {/foreach}
            </select>
            </form>
          </td>
        </tr>

        {foreach from=$plages item=curr_plage}
        <tr>
          <td>
            <strong>Dr. {$curr_plage->_ref_chir->_view}
            de {$curr_plage->debut|date_format:"%Hh%M"} � {$curr_plage->fin|date_format:"%Hh%M"}</strong>
          </td>
        </tr>
        <tr>
          <td>
	        <table class="tbl">
              <tr>
                <th>Heure</th>
                <th>Patient</th>
                <th>Intervention</th>
                <th>Cot�</th>
                <th>Dur�e</th>
              </tr>
              {foreach from=$curr_plage->_ref_operations item=curr_operation}
              <tr>
                <td>
                  <a href="index.php?m={$m}&amp;op={$curr_operation->operation_id}">
                  {$curr_operation->time_operation|date_format:"%Hh%M"}
                  </a>
                </td>
                <td>{$curr_operation->_ref_pat->_view}</td>
                <td>
                  {$curr_operation->_ext_code_ccam->code}
                  {if $curr_operation->CCAM_code2}
                  <br />{$curr_operation->_ext_code_ccam2->code}
                  {/if}
                </td>
                <td>{$curr_operation->cote}</td>
                <td>{$curr_operation->temp_operation|date_format:"%Hh%M"}</td>
              </tr>
              {/foreach}
            </table>
          </td>
        </tr>
        {/foreach}
      </table>
    </td>
    <td class="greedyPane">
      <table class="tbl">
        {if $selOp->operation_id}
        <tr>
          <th class="title" colspan="2">
            {$selOp->_ref_pat->_view} - Dr. {$selOp->_ref_chir->_view}
          </th>
        </tr>
        <tr>
          <th>Entr�e en salle</th>
          <td>
            {if $selOp->entree_bloc}
            {$selOp->entree_bloc}
            {else}
            <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
              <input type="hidden" name="m" value="dPsalleOp" />
              <input type="hidden" name="a" value="do_set_hours" />
              <input type="hidden" name="entree" value="{$selOp->operation_id}" />
              <input type="submit" value="Entr�e" />
            </form>
            {/if}
          </td>
        </tr>
        <tr>
          <th>Dur�e pr�vue</th>
          <td>{$selOp->temp_operation|date_format:"%Hh%M"}</td>
        </tr>
        <tr>
          <th>Intervention</th>
          <td class="text">
            <strong>{$selOp->_ext_code_ccam->libelleLong}</strong> <i>({$selOp->_ext_code_ccam->code})</i>
            <ul>
            {foreach from=$selOp->_ext_code_ccam->activites item=curr_act}
              <li><i>{$curr_act.nom}</i>
              {$curr_act.modificateurs}</li>
            {/foreach}
            </ul>
            {if $selOp->CCAM_code2}
            <br />
            <strong>{$selOp->_ext_code_ccam2->libelleLong}</strong> <i>({$selOp->_ext_code_ccam2->code})</i>
            <ul>
            {foreach from=$selOp->_ext_code_ccam2->activites item=curr_act}
              <li><i>{$curr_act.nom}</i>
              {$curr_act.modificateurs}</li>
            {/foreach}
            </ul>
            {/if}
        </tr>
        <tr>
          <th>Cot�</th>
          <td>{$selOp->cote}</td>
        </tr>
        <tr>
          <th>Anesth�sie</th>
          <td>{$selOp->_lu_type_anesth} par le Dr. ??</td>
        </tr>
        {if $selOp->materiel}
        <tr>
          <th>Mat�riel</th>
          <td><strong>{$selOp->materiel|nl2br}</strong></td>
        </tr>
        {/if}
        <tr>
          <th>Remarques</th>
          <td>{$selOp->rques|nl2br}</td>
        </tr>
        <tr>
          <th>Sortie de salle</th>
          <td>
            {if $selOp->sortie_bloc}
            {$selOp->sortie_bloc}
            {else}
            <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
              <input type="hidden" name="m" value="dPsalleOp" />
              <input type="hidden" name="a" value="do_set_hours" />
              <input type="hidden" name="sortie" value="{$selOp->operation_id}" />
              <input type="submit" value="Sortie" />
            </form>
            {/if}
          </td>
        </tr>
        {else}
        <tr>
          <th class="title">
            Selectionnez une op�ration
          </th>
        </tr>
        {/if}
      </table>
    </td>
  </tr>
</table>