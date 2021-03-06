{extends file="layout.tpl"}

{block name=title}Dicționar explicativ al limbii române{/block}

{block name=content}
  <div style="font-weight: bold; text-align: center">Decât tasta-ți un cuvânt și <i>dexonline</i> vil caută!!</div>

  <div id="wotdMobile" class="widget">
    Cuvântul zilei: {include file="bits/wotdurl.tpl"}
  </div>

  <div id="likes">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <g:plusone size="medium" href="https://dexonline.ro"></g:plusone>
    <iframe src="https://www.facebook.com/plugins/like.php?ref=mobile_home&locale=en_US&app_id=225575497453880&amp;href=https%3A%2F%2Ffacebook.com%2Fdexonline&&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe> 
  </div>
{/block}
