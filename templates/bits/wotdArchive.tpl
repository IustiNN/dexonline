<div id="wotdArchiveHeader">
  {if $showPrev==1}
    <span onclick="loadAjaxContent('{$wwwRoot}{$prevMonth}', '#wotdArchive');" class="nav" id="navLeft"></span>
  {else}
    &nbsp
  {/if}

  <p id="wotdDate">{$month|capitalize} {$year}</p>

  {if $showNext==1}
    <span onclick="loadAjaxContent('{$wwwRoot}{$nextMonth}', '#wotdArchive');" class="nav" id="navRight"></span>
  {else}
    &nbsp;
  {/if}
</div>


<table class="wotdArchiveTable">
  <tr class="wotdDays">
    <td>Luni</td>
    <td>Marți</td>
    <td>Miercuri</td>
    <td>Joi</td>
    <td>Vineri</td>
    <td>Sâmbătă</td>
    <td>Duminică</td>
  </tr>
  {foreach from=$words item=week}
    <tr>
      {foreach from=$week item=day}
        {if $day}
          <td class="activeMonth">
            <div class="wotdDoM">{$day.dayOfMonth}</div>
            <div>{if $day.visible}<a href="{$wwwRoot}cuvantul-zilei/{$day.wotd->displayDate|replace:'-':'/'}">{$day.def->lexicon}</a>{else}&nbsp;{/if}</div>
            <div class="thumb">
              {if $day.wotd && $day.wotd->image && $day.visible}
                {strip}
                  <a href="{$wwwRoot}cuvantul-zilei/{$day.wotd->displayDate|replace:'-':'/'}">
                    <img src="{$day.wotd->getThumbUrl()}" alt="thumbnail {$day.def->lexicon}"/>
                  </a>
                {/strip}
              {/if}
            </div>
          </td>
        {else}
          <td>&nbsp;</td>
        {/if}
      {/foreach}
    </tr>
  {/foreach}
</table>
