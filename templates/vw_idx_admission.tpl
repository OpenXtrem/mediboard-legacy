<table class="main">
  <tr>
    <td colspan="2">
      <form action="index.php" target="_self" name="selection" method="get" encoding="">
      <input type="hidden" name="m" value="{$m}">
      <input type="hidden" name="tab" value="{$tab}">
      Type d'affichage :
        <select name="selAff" onchange="this.form.submit()">
          <option value="0" {if $selAff == "0"} selected = "selected" {/if}>Toutes les admissions</option>
          <option value="o" {if $selAff == "o"} selected = "selected" {/if}>Admissions effectu�es</option>
          <option value="n" {if $selAff == "n"} selected = "selected" {/if}>Admissions non effectu�es</option>
    </select>
    </form>
      </td>
  </tr>
  <tr>
    <th width="50%">
        <a href="index.php?m={$m}&amp;tab={$tab}&amp;day={$pmonthd}&amp;month={$pmonth}&amp;year={$pmonthy}"><<</a>
        {$title1}
        <a href="index.php?m={$m}&amp;tab={$tab}&amp;day={$nmonthd}&amp;month={$nmonth}&amp;year={$nmonthy}">>></a>
      </th>
      <th width="50%">
        <a href="index.php?m={$m}&amp;tab={$tab}&amp;day={$pday}&amp;month={$pdaym}&amp;year={$pdayy}"><<</a>
        {$title2}
        <a href="index.php?m={$m}&amp;tab={$tab}&amp;day={$nday}&amp;month={$ndaym}&amp;year={$ndayy}">>></a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>
            Date
          </th>
          <th>
            Nombre d'admissions
          </th>
        </tr>
        {foreach from=$list item=curr_list}
        <tr>
          <td align="right">
            <a href="index.php?m={$m}&amp;tab={$tab}&amp;day={$curr_list.day}&amp;month={$month}&amp;year={$year}">
            {$curr_list.dateFormed}
            </a>
          </td>
          <td align="center">
            {$curr_list.num}
          </td>
        </tr>
        {/foreach}
      </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>
            Nom
          </th>
          <th>
            Pr�nom
          </th>
          <th>
            Chirurgien
          </th>
          <th>
            Heure
          </th>
        </tr>
        {foreach from=$today item=curr_adm}
        <tr>
          <td>
            <a href="index.php?m={$m}&amp;tab=vw_dtl_admission&amp;id={$curr_adm.operation_id}">
            {$curr_adm.nom}
            </a>
          </td>
          <td>
            <a href="index.php?m={$m}&amp;tab=vw_dtl_admission&amp;id={$curr_adm.operation_id}">
            {$curr_adm.prenom}
            </a>
          </td>
          <td>
            <a href="index.php?m={$m}&amp;tab=vw_dtl_admission&amp;id={$curr_adm.operation_id}">
            Dr. {$curr_adm.chir_lastname} {$curr_adm.chir_firstname}
            </a>
          </td>
          <td>
            <a href="index.php?m={$m}&amp;tab=vw_idx_admission&amp;id={$curr_adm.operation_id}">
            {$curr_adm.hour}
            </a>
          </td>
          {if $curr_adm.admis == "n"}
          <td>
            <form name="editFrm{$curr_adm.id}" action="index.php" method="get">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="a" value="do_edit_admis" />
            <input type="hidden" name="id" value="{$curr_adm.operation_id}" />
            <input type="submit" value="Admis" />
            </form> 
          </td>
          {/if}
        </tr>
        {/foreach}
      </table>
    </td>
  </tr>
</table>