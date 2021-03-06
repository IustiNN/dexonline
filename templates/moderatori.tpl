{extends file="layout.tpl"}

{block name=title}Moderatori{/block}

{block name=content}
  <form method="post" action="moderatori">
    <table class="sources">
      <tr>
        <th>Nume utilizator</th>
        <th>Admin</th>
        <th>Moderator LOC</th>
        <th>Moderator</th>
        <th>Ghid de exprimare<br/>(nefolosit)</th>
        <th>Cuvântul zilei</th>
        <th>Acces căutare</th>
        <th>«Structurist» al definițiilor</th>
        <th>Dicționarul vizual</th>
      </tr>
      {foreach from=$users item=user}
        <tr>
          <td>
            <a href="{$wwwRoot}utilizator/{$user->nick}">{$user->nick}</a>
            {* Ensure this user is processed even if all the boxes are unchecked *}
            <input type="hidden" name="userIds[]" value="{$user->id}"/>
          </td>
          {section name="bit" loop=$smarty.const.NUM_PRIVILEGES}
            {math equation="1 << x" x=$smarty.section.bit.index assign="mask"}
            <td>
              <input type="checkbox" name="priv_{$user->id}[]" value="{$mask}" {if $user->moderator & $mask}checked="checked"{/if}/>
            </td>
          {/section}
        </tr>
      {/foreach}
      <tr>
        <td>
          Adaugă un moderator nou:<br/>
	        <input type="text" name="newNick" value=""/>
        </td>
        {section name="bit" loop=$smarty.const.NUM_PRIVILEGES}
          {math equation="1 << x" x=$smarty.section.bit.index assign="mask"}
          <td><input type="checkbox" name="newPriv[]" value="{$mask}"/></td>
        {/section}
      </tr>
    </table>
    <input type="submit" name="submitButton" value="Salvează"/>
  </form>
{/block}
