<?php
require_once("../phplib/util.php");

$user = session_getUser();
$haveEuPlatescCredentials = Config::get('euplatesc.euPlatescMid') && Config::get('euplatesc.euPlatescKey');

SmartyWrap::assign('suggestHiddenSearchForm', true);
SmartyWrap::assign('suggestNoBanner', true);
SmartyWrap::assign('haveEuPlatescCredentials', $haveEuPlatescCredentials);
SmartyWrap::assign('defaultEmail', $user ? $user->email : '');
SmartyWrap::addCss('jqueryui');
SmartyWrap::addJs('jqueryui');
SmartyWrap::display('doneaza.tpl');

?>
