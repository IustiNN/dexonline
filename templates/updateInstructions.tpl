<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>dexonline - update.php</title>
  </head>

  <body>
    <p>Acest script exportă baza de date a <i>dexonline</i> sau
    porțiuni ale ei. Dacă doriți să scrieți un client care să
    transfere baza de date și să o folosească off-line, comunicați cu
    acest script pentru a vă sincroniza periodic baza de date.</p>

    <p><b>LICENȚĂ:</b> Copierea, folosirea și redistribuirea acestor
    definițiilor din <i>dexonline</i> sunt permise sub <a
    href="{$wwwRoot}licenta">Licența Publică Generală GNU</a>.</p>

    <p><b>OBSERVAȚIE:</b> Este obligatoriu ca mecanismul pe care îl
    folosiți pentru transfer să accepte date comprimate cu gzip. Mai
    exact, trebuie ca, la stabilirea conexiunii, să setați antetul
    <b>Accept-Encoding: gzip</b> (cel puțin). Datele pe care le primiți
    sunt comprimate cu gzip; există biblioteci publice pentru cam orice
    limbaj pe care le puteți folosi pentru a decomprima datele. Dacă nu
    vă conformați acestei cerințe, veți primi un răspuns 403
    (Forbidden). De remarcat că orice browser modern folosește gzip,
    deci îl puteți folosi pentru a inspecta vizual datele.</p>

    <p>Parametri acceptați de acest script:</p>

    <h4>timestamp=&lt;long&gt;</h4>

    <p>Data ultimei actualizări făcute. Scriptul va returna toate
    definițiile care au fost adăugate sau modificate după această
    dată. <b>Timestamp</b> înseamnă numărul de secunde trecute de la 1
    ianuarie 1970 GMT. Programul în care dezvoltați aplicația ar trebui
    să aibă funcții de conversie din data/ora calendaristică în
    timestamp. Dacă nu, puteți apela prima oară acest script cu
    timestamp=0, pentru a prelua toate definițiile.  Scriptul vă va
    returna timestamp-ul curent, pe care îl puteti păstra și folosi ca
    parametru la următoarea actualizare.</p>

    <h4>version=&lt;string&gt;</h4>

    Versiunile acceptate în prezent și caracteristicile lor sunt:

    <ul>
      <li><b>1.0:</b> Aceasta este prima versiune a protocolului. Dacă nu
        specificați versiunea dorită, veți primi implicit datele în versiunea
        1.0.
        <ul>
	  <li>Înainte de lista rezultatelor, această versiune exportă câmpul
          <tt>&lt;NumResults&gt;</tt>, care indică numărul aproximativ de
	  rezultate conținute în mesaj. Numărul nu este întotdeauna exact
          deoarece baza de date se poate modifica în timpul
          transferului.</li>
          <li>Acceptă opțiunea <tt>flags=d</tt>, care include și cuvăntul-titlu
          cu diacritice pentru fiecare definiție. Începând cu versiunea 2.0,
          acest lucru se petrece automat.</li>
        </ul>
      </li>

      <li><b>2.0:</b>
        <ul>
	  <li>Nu mai include câmpul <tt>&lt;NumResults&gt;</tt>. Pentru a avea
          o măsură a progresului, folosiți câmpul <tt>&lt;Timestamp&gt;</tt>,
          prezent în fiecare definiție. Datele sunt sortate în ordinea
          crescătoare a acestui câmp.</li>
          <li>Exportă toate cuvintele-cheie pentru fiecare definiție (există
          întotdeauna un cuvânt-cheie, dar pot exista mai multe).
          Cuvintele-cheie sunt exportate atât cu diacritice (în câmpul
          <tt>&lt;Dname&gt;</tt>), cât și fără (în câmpul
          <tt>&lt;Name&gt;</tt>).</li>
        </ul>
      </li>
    </ul>

    <h4>flags=[ad]</h4>

    <p>Opțiuni pentru a primi datele în diverse formate.</p>

    <ul>
       <li><b>a:</b> Exportă și cuvântul-titlu cu diacritice pentru fiecare
       definiție. Această opțiune este ignorată începând cu versiunea 2.0.</li>

       <li><b>d:</b> Înlocuiește unele notații interne ale <i>dexonline</i>
       (-, **, *, &lt;, &gt;) cu caractere Unicode (&amp;#x2013;, &amp;diams;,
       &amp;loz;, &amp;lt;, &amp;gt;).</li>
    </ul>

  </body>
</html>
