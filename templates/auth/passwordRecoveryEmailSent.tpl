{extends file="layout.tpl"}

{block name=title}Recuperarea parolei{/block}

{block name=content}
  Am trimis la adresa <b>{$email}</b> un e-mail cu instrucțiuni pentru recuperarea parolei. În mod normal, îl veți primi în maximum 5 minute. Nu uitați să
  verificați și folderul de spam, în eventualitatea în care mesajul ajunge acolo. Codul de recuperare este activ 24 de ore; vă rugăm să îl folosiți până
  atunci.<br/><br/>

  Apoi, puteți relua <a href="{$wwwRoot}auth/login">autentificarea cu OpenID</a>.
{/block}
