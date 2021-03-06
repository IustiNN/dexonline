{extends file="layout.tpl"}

{block name=title}Moara cuvintelor{/block}

{block name=content}
  <script>
   var word = "";
   var difficulty = 1;
   var answer = "";
   var round = 1;
   var answeredCorrect = 0;
   var answerId = 0;
   var guessed = 0;
   var definitions = [];
  </script>

  <div class="millArea">
    <p class="paragraphTitle">Moara cuvintelor</p>

    <div id="mainPage">
      <form id="main" action="">Nivel:
        <button class="difficultyButtons btn" type="button" value="1">Ușor</button>
        <button class="difficultyButtons btn" type="button" value="2">Mediu</button>
        <button class="difficultyButtons btn" type="button" value="3">Greu</button>
        <button class="difficultyButtons btn" type="button" value="4">Foarte greu</button>
      </form>
      Se poate juca și cu tastatura folosind tastele: 1, 2, 3, 4.
    </div>

    <div id="questionPage">
      {section name=round start=1 loop=11} {* Yes, Smarty, 11 means 10 *}
        <span class="questionImage">
          <img id="statusImage{$smarty.section.round.index}" src="{$imgRoot}/mill/pending.png" alt="imagine pentru runda {$smarty.section.round.index}"/>
          <span class="questionNumber">{$smarty.section.round.index}</span>
        </span>
      {/section}

      <form id="mill" action="">
        <label>Definiția corectă pentru <span class="word"></span>:</label>
        <button class="optionButtons btn" type="button" value="1">a&#41;</button>
        <button class="optionButtons btn" type="button" value="2">b&#41;</button>
        <button class="optionButtons btn" type="button" value="3">c&#41;</button>
        <button class="optionButtons btn" type="button" value="4">d&#41;</button>
      </form>
    </div>

    <div id="resultsPage">
      <p>Felicitări! Ai răspuns corect la <b id="answeredCorrect">0</b> definiții din 10.</p>
      <button id="newGameButton" class="btn">Joc nou</button>
      <button id="definitionsButton" class="btn">Vezi toate definițiile</button>
    </div>

    <div id="definitionsSection">
    </div>

  </div>
{/block}
