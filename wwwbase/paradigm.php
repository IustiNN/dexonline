<?php
require_once("../phplib/util.php");

define('TYPE_SHOW_ONLY_VERBS', 'conjugare');
define('TYPE_SHOW_NO_VERBS', 'declinare');

$cuv = util_getRequestParameter('cuv');
$lexemId = util_getRequestParameter('lexemId');
$ajax = util_getRequestParameter('ajax');
$type = util_getRequestParameter('type');

$searchType = SEARCH_INFLECTED;
$arr = StringUtil::analyzeQuery($cuv);
$hasDiacritics = session_user_prefers(Preferences::FORCE_DIACRITICS) || $arr[0];

// LexemId search
if ($lexemId) {
  $searchType = SEARCH_LEXEM_ID;
  SmartyWrap::assign('lexemId', $lexemId);
  if (!StringUtil::validateAlphabet($lexemId, '0123456789')) {
    $lexemId = '';
  }
  $lexem = Lexem::get_by_id($lexemId);
  if ($lexem) {
    $lexems = array($lexem);
    $cuv = $lexem->formNoAccent;
  } else {
    $lexems = array();
    $cuv = NULL;
  }
}

if ($lexemId) {
  SmartyWrap::assign('paradigmLink', util_getWwwRoot() . "lexem/$cuv/$lexemId/paradigma");
}
else {
  SmartyWrap::assign('paradigmLink', util_getWwwRoot() . "definitie/$cuv/paradigma");
}

if ($cuv) {
  $cuv = StringUtil::cleanupQuery($cuv);
}

// Normal search
if ($searchType == SEARCH_INFLECTED) {
  $lexems = Lexem::searchInflectedForms($cuv, $hasDiacritics);
  if (count($lexems) == 0) {
    $cuv_old = StringUtil::tryOldOrthography($cuv);
    $lexems = Lexem::searchInflectedForms($cuv_old, $hasDiacritics);
  }
}

// Maps lexems to arrays of inflected forms (some lexems may lack inflections)
// Also compute the text of the link to the paradigm div,
// which can be 'conjugări', 'declinări' or both
if (!empty($lexems)) {
  $conjugations = false;
  $declensions = false;
  $filtered_lexems = array();
  foreach ($lexems as $l) {
    $lm = $l->getFirstLexemModel(); // One LexemModel suffices -- they all better have the same modelType.
    $isVerb = ($lm->modelType == 'V') || ($lm->modelType == 'VT');
    if (((TYPE_SHOW_ONLY_VERBS == $type) && $isVerb) ||
        ((TYPE_SHOW_NO_VERBS == $type) && !$isVerb) ||
        !$type) {

      $filtered_lexems[] = $l;
      $conjugations |= $isVerb;
      $declensions |= !$isVerb;
    }
  }

  if (empty($filtered_lexems)) {
    FlashMessage::add("Niciun rezultat pentru {$cuv}.");
  }

  $declensionText = $conjugations ? ($declensions ? 'Conjugare / Declinare' : 'Conjugare') : ($declensions ? 'Declinare' : '');

  if ($cuv && !empty($filtered_lexems)) {
    SmartyWrap::assign('cuv', $cuv);
    SmartyWrap::assign('declensionText', "{$declensionText}: {$cuv}");
  }

  // Exercise the fields we'll need later in the view.
  // TODO: this code replicates code from search.php
  $hasUnrecommendedForms = false;
  foreach ($filtered_lexems as $l) {
    foreach($l->getLexemModels() as $lm) {
      $lm->getModelType();
      $lm->getSourceNames();
      $map = $lm->loadInflectedFormMap();
      $lm->addLocInfo();
      foreach ($map as $ifs) {
        foreach ($ifs as $if) {
          $hasUnrecommendedForms |= !$if->recommended;
        }
      }
    }
  }

  SmartyWrap::assign('hasUnrecommendedForms', $hasUnrecommendedForms);
  SmartyWrap::assign('lexems', $filtered_lexems);
  SmartyWrap::assign('showParadigm', true);
  SmartyWrap::assign('locParadigm', session_user_prefers(Preferences::LOC_PARADIGM));
  SmartyWrap::assign('onlyParadigm', !$ajax);
}
else {
  FlashMessage::add("Niciun rezultat pentru {$cuv}.");
}

if ($ajax) {
  SmartyWrap::displayWithoutSkin('bits/multiParadigm.tpl');
}
else {
  SmartyWrap::addCss('paradigm');
  SmartyWrap::display('search.tpl');
}
?>
