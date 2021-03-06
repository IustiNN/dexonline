{extends file="admin/layout.tpl"}

{block name=title}Cuvântul zilei{/block}

{block name=headerTitle}Cuvântul zilei{/block}

{block name=content}
  <a href="wotdImages.php">Imagini pentru cuvântul zilei</a> |
  <a href="http://wiki.dexonline.ro/wiki/Imagini_pentru_cuv%C3%A2ntul_zilei">instrucțiuni</a>
  <table id="wotdGrid"></table>
  <div id="wotdPaging"></div>

  <br/>

  <form action="wotdExport.php">
    Descarcă lista pentru luna
    {include file="bits/numericDropDown.tpl" name="month" start=1 end=13 selected=$downloadMonth}
    {include file="bits/numericDropDown.tpl" name="year" start=$downloadYear-3 end=$downloadYear+3 selected=$downloadYear}
    <input type="submit" name="submitButton" value="Descarcă"/>
  </form>
{/block}
