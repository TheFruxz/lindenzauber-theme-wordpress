# Brigittas Kritikpunkte – Punkt für Punkt

Grundlage ist das Craft-Dokument „Lindenzauber Kritik und rohe Ideen von Brigitta“,
vollständig gelesen (beide Seiten). Jeder Punkt steht hier im Wortlaut, dahinter
was daraus geworden ist.

---

## Allgemein

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 1 | „Ist zwar Wordpress Theme, aber die Texte sind alle Fest → sind nicht bearbeitbar in HTML → sollte mit Wordpress Editor sein“ | **Erledigt.** Alle Seiten bestehen jetzt aus reinen WordPress-Kernblöcken. Automatisch geprüft: 342 Blöcke, **0** Roh-HTML-Blöcke, **0** ungültige Blöcke (`tools/block-check.mjs`). Vorher steckte der komplette Seiteninhalt in „Individuelles HTML“-Blöcken. |
| 2 | „Logo auf der Startseite nicht sichtbar → Ist aber in den Mediatheken“ | **Erledigt.** Ursache: Das Logo war im Quelltext auskommentiert (`<!-- Hier stand ein Logo-Bild … -->`). Jetzt steht es als normaler Bild-Block ganz oben im Kopfbereich und ist per Klick austauschbar. |
| 3 | „Die erzählenden → Blockartig → liest sich wie ein zugekramter Block“ | **Erledigt.** Siehe Abschnitt „Die Erzählenden“ unten: wechselnde Bildseite, einheitliches Bildformat, weiche Ausblendung langer Texte, klare Trennlinien. |
| 4 | „‚Es fängt wieder von vorne an‘ bei Programm“ | **Erledigt.** Ursache: `/programm/` begann mit denselben Überschriften und fast demselben Text wie die Startseite. Die Programmseite hat jetzt einen eigenen Einstieg mit Kurzübersicht und drei Sprungmarken (Samstagabend · Sonntagnachmittag · Ort und Anfahrt). |
| 5 | „Förderer → Sponsoren Warum sind die so klein und aufgeteilt“ | **Erledigt.** Die Logos im weißen Band sind von 64 px auf 104 px gewachsen und stehen in einer Reihe. Auf der Startseite und der Förderer-Seite stehen zusätzlich vier große, vollständig anklickbare Kacheln. |
| 6 | „Kontakt Impressum Datenschutz in dere Fußzeile“ | **Erledigt.** Eigener Menüplatz „Fußzeile: Kontakt, Impressum, Datenschutz“, in der Fußzeile unter der Überschrift „Rechtliches“. Auch ohne zugewiesenes Menü erscheinen die drei Links. |
| 7 | „Kontakt ist Gelb, warum“ | **Erledigt.** Ursache gefunden: Auf der Seite `/kontakt/` stand das **Impressum** mit gelb hervorgehobenen Platzhaltern (`<mark>`). Kontakt und Impressum sind jetzt sauber getrennt, die Platzhalter sind durch die echten Vereinsdaten ersetzt. |
| 8 | „Die Überschriften sind hässlich“ | **Erledigt.** Ursache: Es war gar keine Schrift mitgeliefert – die „Schreibschrift“ fiel auf Windows und Android auf *Georgia kursiv* zurück. Jetzt sind zwei echte Schriften im Theme: **Great Vibes** für die Schwungschrift, **Lato** für alles andere. Beide liegen lokal, es wird nichts von Google geladen. |
| 9 | „Die Faust ins Auge“ | **Erledigt.** Kompletter Neuaufbau nach dem Plakat: Nachthimmel mit Sternen, warmer Laternenschein, Lindenbaum mit Laterne, Dorfsilhouette, wärmeres Gold (`#f0c868` statt des zitronigen `#fce36d`), Sans statt Serif. |
| 10 | „Button auf der Startseite ist ein Renderfehler“ | **Erledigt.** Ursache: `.entry-content ul { padding-left: 1.3rem }` überschrieb `.lz-actions { padding: 0 }` – die zentrierte Buttonreihe saß sichtbar rund 21 px zu weit rechts. Solche Kollisionen sind ausgeschlossen: Die gesamte Gestaltung hängt jetzt an den Blockklassen selbst, es gibt keine `.entry-content > *`-Regeln mehr. |
| 11 | „Struktur ist zwar da, aber das aussehen ist in diesen Details hässlich“ | **Erledigt.** Struktur beibehalten, Gestaltung neu. |
| 12 | „Keine HTML Dateien, die man einfügen muss. Kein Visual Editor“ | **Erledigt.** Der Blockcode wird **einmal** pro Seite eingefügt – danach nie wieder. Zusätzlich sind alle Abschnitte als Muster im Einfügen-Menü unter „Lindenzauber“ hinterlegt, dann entfällt sogar das eine Mal. |
| 13 | „Metadaten Müssen richtig sein“ | **Erledigt.** Beschreibung je Seite kommt aus dem Feld „Auszug“ im Editor. Vorschaubild fürs Teilen im Customizer wählbar. Die Veranstaltungsdaten für Google kommen aus den Eckdaten – vorher standen Datum und Uhrzeit fest im Quelltext. |

## Startseite

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 14 | „Die Vier punkte auf der Startseite weiter unten eingebettet“ | **Erledigt.** Der Abschnitt „Was Sie vor Ort erwartet“ steht jetzt unterhalb des Plakats statt im Kopfbereich. |
| 15 | „Ebenerdig erreichbar, weiss ich ncht“ | **Umgesetzt mit Hinweis.** Der Punkt ist drin („Alle Räume sind ebenerdig zu erreichen“), steht aber als eigener Listenpunkt und lässt sich mit zwei Klicks löschen. Siehe HANDBUCH.md. |
| 16 | „Keine Anmeldung nötig dafür präsenter“ | **Erledigt.** Steht jetzt direkt unter der Banderole „Eintritt frei“, in Gold mit Funkelzeichen – die auffälligste Stelle der ganzen Seite. |
| 17 | „Die Aufteilungen / Verlinkungen waren schon gut mit dem Button“ | **Beibehalten.** Zwei Knöpfe im Kopfbereich, je ein Knopf in den beiden Festtag-Karten. |
| 18 | „Das gut bis inklusive die gleichbedeutenden Karte“ | **Beibehalten.** Reihenfolge unverändert: Kopfbereich → Einleitung → die beiden gleichwertigen Karten. |
| 19 | „Dahinter platzhalter mit dem Flyer ausfüllen“ | **Erledigt.** Direkt nach den beiden Karten kommt der Abschnitt „Das Plakat zum Lindenzauber“. Solange das Plakat fehlt, steht dort ein gestalteter goldener Rahmen statt eines kaputten Bildes – siehe SETUP.md, Schritt 7. |
| 20 | „Die sechste Stimme ist noch nicht da, aber kommt noch“ | **Vorbereitet.** Sechster Platz mit Lindenblatt-Feld und Text „Wird noch bekannt gegeben“. Foto und Text eintragen, fertig. |
| 21 | „Die 4 Sponsoren viel größer und anklickbar“ | **Erledigt.** Kacheln mit 96 px hohen Logos, die **komplette Kachel** ist anklickbar, mit Anhebe-Effekt beim Überfahren. |
| 22 | „In einer Reihe aber besser wären 4 Felder in so nem Grid“ | **Erledigt.** Am Rechner vier Felder in einer Reihe, auf dem Tablet und Handy 2 × 2. |
| 23 | „Footer kann auch bleiben, der war gut“ | **Beibehalten**, nur an die neue Typografie angepasst. |

## Programmseite

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 24 | „Wirkt wie lieblos aneinandergeklatschte Blöcke“ | **Erledigt.** Klare Dramaturgie: Einstieg mit Übersicht → Samstag → Sonntag → Ort und Anfahrt, mit abwechselnden Abschnittshintergründen. |
| 25 | „Verwandschaft erkennbar sein → eine Veranstaltung und ein Märchennachmittag“ | **Erledigt.** Beide Tage benutzen dieselbe Kartenform, dieselbe Überzeile („Der erste Tag“ / „Der zweite Tag“) und dieselbe Typo-Abfolge. |
| 26 | „Das Icon unterscheidet ja … Das ein bissl herausarbeiten → Bitte was abstracktes“ | **Erledigt.** Zwei abstrakte Goldzeichen direkt vom Plakat: Mond mit Sternen für den Abend, drei Figuren im Kreis für den Familientag. Dazu eine goldene Oberkante auf jeder Karte, die die Zusammengehörigkeit zeigt. |
| 27 | „Sponsoren hier auch wieder größer → wenn es im Footer steckt, dann doch neben im Footer“ | **Erledigt.** Kein zweiter Sponsorenblock auf der Programmseite. Stattdessen tragen die vier Logos nebeneinander im weißen Fußband, deutlich größer als vorher. |
| 28 | „Karte mit einblenden als ein Bild, damit man weiß ‚ah, wenn ich hin will muss ich hier schauen‘“ | **Erledigt.** Abschnitt „Ort und Anfahrt“ mit Kartenbild, goldener Ortsmarke, Adresse und Knopf „Route planen“. Das Kartenbild ist eine normale Bilddatei – beim Aufruf der Seite wird **kein** fremder Kartendienst geladen. |

## Die Erzählenden

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 29 | „Text zwar vollkommen da, aber ab nen moment ausgegraut und da ‚mehr anzeigen‘“ | **Erledigt.** Ab etwa acht Zeilen blendet der Text weich aus, darunter steht „Mehr anzeigen“. Passiert von selbst, es muss nichts eingestellt werden. Ohne JavaScript steht der volle Text da – es geht nie Inhalt verloren. |
| 30 | „Bild mal links und mal rechts“ | **Erledigt.** Wechselt automatisch: erstes Porträt links, zweites rechts, drittes links … Auch beim Umsortieren oder Einfügen neuer Porträts. |
| 31 | „Die Karten / schnellinfos passten“ | **Beibehalten**, als Rollenzeile unter dem Namen. |

## Über Lindenzauber

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 32 | „Wieder so ein render fehler button“ | **Erledigt**, gleiche Ursache und gleiche Lösung wie Nr. 10. |
| 33 | „Ließt sich sehr schwerfällig die liste“ | **Erledigt.** Die achtzeilige Tabelle ist aufgelöst: oben die beiden Tage als Karten, darunter sechs kompakte Fakten in einem zweispaltigen Raster mit feinen Trennlinien. |
| 34 | „Samstag und Sonntag seperat darstellen“ | **Erledigt.** Zwei eigene Karten mit je eigenem Goldzeichen, statt zweier Tabellenzeilen. |
| 35 | „Veranstalter und kooperation auflockern, damit man trennungen erkennt“ | **Erledigt.** Drei klar abgesetzte Karten mit Rolle als Überzeile, Name groß, Erläuterung darunter und eigenem Link. |
| 36 | „Brigitta wortmann auf privat seite maerchentruhe“ | **Erledigt.** Verlinkt auf `diemaerchentruhe.de` – auf der Seite „Die Erzählenden“ im Porträt und auf „Über Lindenzauber“ in der Karte „Konzeption und Organisation“. |
| 37 | „Kontaktaufnehmen ist ein bissl weird“ | **Erledigt.** Aus „Kontakt aufnehmen“ wurde „Schreiben Sie uns“; auf der Kontaktseite stehen zwei direkte Knöpfe: „E-Mail schreiben“ und „Anrufen oder WhatsApp“. |

## Förderer

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 38 | „Lindenzauber untertützen ist nicht mehr nötig“ | **Erledigt.** Der Abschnitt „Sie möchten den Lindenzauber unterstützen?“ ist ersatzlos entfernt. |
| 39 | „alle anklickbar und schöner“ | **Erledigt.** Vier große weiße Kacheln, jede komplett anklickbar. Am Schluss ein Dank an den Kindergarten. |

## Extra

| Nr. | Brigittas Punkt | Umsetzung |
|----|----|----|
| 40 | „Extra Seite mit Fotogalerie – Von dem Event Fotos ggf. später erst freischalten aber vorbereiten“ | **Vorbereitet.** Fertige Seite „Fotogalerie“ mit Einleitung, Galerie-Raster im Nachtlook und Abschluss. Sie liegt als **Entwurf** bereit; Fotos einsetzen, veröffentlichen, fertig. |

---

## Zusätzlich gefunden und behoben

Diese Punkte standen nicht im Craft-Dokument, fielen aber bei der Durchsicht der
Live-Seite auf:

* **Kontakt und Impressum waren vertauscht.** Auf `/kontakt/` stand das Impressum,
  auf `/impressum/` standen Brigittas Kontaktdaten. Jetzt richtig getrennt.
* **„Registernummer: VR 110170 eintragen“** – das Wort „eintragen“ vom Platzhalter
  war stehengeblieben und wurde live angezeigt. Entfernt.
* **Auf „Die Erzählenden“ stand live nur Brigitta Wortmann.** Die anderen fünf
  fehlten. Jetzt sind alle sechs Plätze da.
* **Der Platzhaltertext „Hier das Plakat als Bild-Block einsetzen.“** war auf der
  Startseite öffentlich sichtbar. Ersetzt durch einen gestalteten Rahmen.
* **Seitlicher Überlauf auf dem Handy.** Die Seite ließ sich nach rechts schieben.
  Behoben und automatisch nachgeprüft (390 px, 768 px, 1440 px).
* **Porträtfotos in drei verschiedenen Formaten** (hoch, quer, quadratisch) ließen
  die Seite unruhig wirken. Jetzt einheitlich 4:5 zugeschnitten.

---

# Zweite Runde – Cedrics Durchsicht

## Die gemeinsame Wurzel der Abstandsfehler

Fast alle gemeldeten Stellen hatten dieselbe Ursache. WordPress erzeugt den Abstand
zwischen zwei Blöcken selbst, als `margin-block-start`. Zwölf Regeln im Theme haben
diesen Rand auf null oder negativ gesetzt – und damit den Abstand gelöscht.

Behoben wurde nicht Stelle für Stelle, sondern das Prinzip: **kein Blockstil setzt
mehr `margin-block-start`.** Wo bewusst wenig Abstand gewollt ist, steht das an einer
einzigen Stelle in `blocks.css`, Abschnitt 1, als Geschwisterregel. Damit kann derselbe
Fehler nicht an anderer Stelle wieder auftauchen – auch nicht bei allem, was Brigitta
später selbst einfügt.

Zweite Ursache: `.entry-content` brachte eigenes Innenmaß mit, zusätzlich zu dem jedes
Abschnitts. Oben und unten stand doppelte Luft.

Dritte Ursache, dabei gefunden: WordPress hat die **Abstands-Skala des Themes mit
seiner eigenen überschrieben**. Alle Abschnitte hatten dadurch 36 px statt der
vorgesehenen 56–88 px. `defaultSpacingSizes: false` in der `theme.json` behebt das.

| Nr. | Cedrics Punkt | Umsetzung |
|----|----|----|
| 41 | „Zum Programm und Die Erzählenden sind unterschiedlich groß“ | **Erledigt.** Ursache: Der Kern-Stil „Kontur“ setzt eigene Innenabstände und einen 2 px breiten Rahmen und gewann gegen die Theme-Vorgabe. Beide Knöpfe haben jetzt exakt dieselben Maße, dazu Druck-Rückmeldung und sichtbaren Fokusring. |
| 42 | „Häuserreihe schwebt mitten im Bild“ | **Erledigt.** Der Hintergrund war am Bildschirm festgenagelt, dadurch hatte die Silhouette keinen Boden. Jetzt: aufgehellter Himmel oben, darunter durchgehend derselbe Grundton, und die Häuser sitzen genau auf der Kante – mit Dunst hinter der Dachlinie und einem Rest Laternenlicht am Horizont. Es liest sich als Horizont. |
| 43 | „Parallax sieht tot und ungewollt aus“ | **Erledigt.** `position: fixed` ist raus, alles scrollt mit. Geblieben ist ein Nachlauf von 4 % über die CSS-Scroll-Zeitleiste – auf der Grafikkarte, ohne JavaScript. Unterhalb des Kopfbereichs liegt eine **vorab weichgezeichnete** Sterntextur; die Unschärfe steckt in der Grafik, ein CSS-Weichzeichner über diese Fläche hätte Leistung gekostet. Bei „Bewegung reduzieren“ steht alles still. |
| 44 | „Förderer sind nicht klickbar vom Aussehen“ | **Erledigt.** Echter Fund: Der Absatz war `position: relative`, dadurch spannte sich die Klickfläche nur über den Namen statt über die Kachel. Jetzt: Zeigefinger auf der ganzen Kachel, Link über allem, Hover mit Goldrahmen, Anheben, Schatten und größerem Logo, Druck-Rückmeldung, Fokusring und ein Pfeil als Hinweis. |
| 45 | „Förderer nicht doppelt anzeigen“ | **Erledigt.** Das freistehende weiße Band entfällt. Der Fußbereich hat jetzt eine eigene erste Zeile mit den Logos auf hellen Kacheln im Raster. Auf der Startseite bleibt sie aus – dort stehen die Förderer weiter oben, größer als vorher (Logos bis 132 px statt 96 px). |
| 46 | „Zwischen Titel und Boxen ist kein Gap“ (Bild 1) | **Erledigt**, siehe gemeinsame Wurzel oben. Nach jeder Überschrift steht jetzt verlässlich Luft. |
| 47 | „Kurzfassung unter dem Namen hat kein Padding“ (Bild 2) | **Erledigt.** Der negative Rand ist weg; Name → Rolle → Text folgen einem festen Takt. |
| 48 | „Mehr anzeigen ist zackig da“ | **Erledigt.** Der Text entfaltet sich jetzt über eine gemessene Höhe hinweg. Die Ausblendung ist eine Maske statt einer Farbfläche – dadurch passt sie auf jedem Hintergrund und es entsteht kein heller Kasten mehr. |
| 49 | „Text unter Förderer ist nicht zentral in der Box“ (Bild 3) | **Erledigt**, siehe zweite Ursache oben. |
| 50 | „Finde weitere Fehler dieser Art“ | **Erledigt und abgesichert.** `tools/layout-check.mjs` prüft alle Seiten in drei Breiten auf fehlende Abstände, außermittige Abschnitte, Klickflächen ohne Zeiger, seitlichen Überlauf und zu schwachen Kontrast. Ergebnis: **keine Befunde**. |

## In dieser Runde zusätzlich gefunden

* **Alle Abschnitte waren zu eng** – die Abstands-Skala des Themes wurde von
  WordPress überschrieben (36 px statt 86 px).
* **Die Ausblendung bei „Mehr anzeigen“ war als heller Kasten sichtbar**, weil die
  Verlaufsfarbe den Hintergrund nicht traf.
* **Auf dem Handy standen die Förderer-Kacheln untereinander statt 2 × 2** – die
  Breitenrechnung passte um wenige Pixel nicht zum Spaltenabstand.
* **Der Laternenschein klebte am Bildschirm**, statt zur Laterne zu gehören.
* **„Gut zu wissen“ war als einziger Abschnitt der Startseite linksbündig** und wirkte
  dadurch versehentlich. Jetzt mittig wie die übrigen.
* **Der Festtitel hatte keinen Schattenwurf** – einzelne Sterne standen mitten in den
  Buchstaben. Jetzt hebt sich der Text sauber ab.

---

# Dritte Runde – Cedrics Durchsicht

| Nr. | Cedrics Punkt | Umsetzung |
|----|----|----|
| 51 | „Startseite: Unter dem Footer ist plötzlich ganz viel Luft“ | **Erledigt.** Die Sternenebene ist bewusst 4 % höher als die Seite, damit beim Scroll-Nachlauf unten nichts frei liegt. `.site` schnitt aber nur **waagerecht** ab (`overflow-x: clip`) – senkrecht verlängerte der Überstand die Seite um 200–280 px. Jetzt `overflow: clip` auf beiden Achsen. Nachgemessen: Unterkante Fußbereich = Dokumentende, exakt 0 px, in 390/768/1440 px und nach vollem Durchscrollen. |
| 52 | „Bild 1: Hintergrund von Box und Bild sind ungleich“ | **Erledigt.** Die Kacheln im Fußbereich hatten `rgba(255,255,255,0.94)`; über dem dunklen Grund ergab das rgb(240,240,241). Die Logodateien bringen aber deckendes Weiß mit – man sah ein helleres Rechteck im Rahmen stehen. Kacheln jetzt reines `#ffffff`; die Rückmeldung beim Überfahren macht seither der Goldrahmen, nicht mehr ein Farbwechsel. |
| 53 | „Das Kartenbild wäre super von Apple Maps, das sieht wertiger aus“ | **Anders gelöst – bewusst.** Apple-Karten dürfen nur innerhalb von Apples eigenen Frameworks verwendet werden; ein eingebettetes MapKit würde außerdem bei **jedem** Seitenaufruf Daten an Apple senden. Stattdessen ist die Karte jetzt **selbst gezeichnet**: `tools/make-karte.py` fragt einmal beim Bauen OpenStreetMap ab und erzeugt ein SVG im Farbklang der Seite – Nachthimmel, Straßen in abgestuftem Gold nach Wichtigkeit mit weichem Schein darunter, Häuser als ruhige Blöcke, Grünflächen, Bahnlinie gestrichelt, Straßennamen in der Theme-Schrift und der Kindergarten als leuchtende Goldmarke. Gestochen scharf auf jedem Bildschirm, 88 KB statt 455 KB, und es wird weiterhin nichts nachgeladen. |
| 54 | „Bild 2: Dieser Bereich wirkt eng, zu dicht, wie hineingequetscht“ | **Erledigt.** Räume und Zeiten standen als zwei kleine Listen mitten im Fließtext der schmalen Spalte. Sie sind jetzt eine eigene **Ablauf-Tafel**: ein gerahmter Block, der aus der Textspalte heraustritt, mit Titel und Uhrzeichen. Die vier Räume sind große Kacheln mit goldener Oberkante geworden – vier Türen statt vier Stichpunkte –, die Erzählzeiten ein **Zeitstrahl** mit goldenen Punkten auf einer Linie. Zwischen den beiden Hälften steht die größte Pause der Seite. Auf dem Handy wird aus dem Strahl eine Leiter und aus den vier Kacheln ein 2 × 2-Feld. |
| 55 | „Schaue nochmal, wo es noch Fehler gab“ | **Erledigt und abgesichert.** `tools/layout-check.mjs` hat drei neue Prüfungen bekommen (siehe unten) und meldet über alle sieben Seiten in drei Breiten **keine Befunde**. Dazu ein Durchgang von Hand. |

## Das Prüfwerkzeug prüft jetzt auch das

Die Fehler 51 und 52 waren beide messbar – das alte Werkzeug hat nur nicht danach
gesucht. Neu dazugekommen:

1. **Leerraum hinter dem Fußbereich** – Abstand zwischen Unterkante Fußbereich und
   Dokumentende.
2. **Element ragt unter den Fußbereich** – und zwar gemessen an dem, was man
   *sieht*: was ein Vorfahre abschneidet, zählt nicht. Sonst hätte die Prüfung
   nach der Behebung weiter gemeldet, obwohl nichts mehr zu sehen ist.
3. **Farbbruch Bild ⟷ Kachel** – die Eckpixel jedes Bildes werden über ein Canvas
   ausgelesen und mit der *tatsächlich sichtbaren* Hintergrundfarbe verglichen
   (alle durchsichtigen Ebenen übereinandergelegt, nicht nur die oberste). Bilder
   mit durchsichtigen Ecken und Bilder mit sichtbarer Umrandung bleiben außen vor –
   dort ist der Wechsel gewollt.

**Gegenprobe:** Mit dem alten Stylesheet und dem neuen Werkzeug kommen beide Fehler
zurück (18 Befunde auf zwei Seiten), mit dem neuen Stylesheet keiner. Das Werkzeug
misst also wirklich das, was gemeldet wurde.

## In dieser Runde zusätzlich gefunden

* **Die Testinstanz baute die Menüs bei jedem Lauf doppelt auf** – `wp menu item
  list` kennt nur `--fields` (Mehrzahl); mit `--field` brach der Aufruf still ab und
  die Aufräumschleife lief ins Leere. Im Fußbereich standen dadurch zwölf statt drei
  Links. Kein Fehler des Themes, aber einer, der jede Sichtprüfung verfälscht hat.
* **Ein Fehler in der Kartenzeichnung, der still blieb:** eine `path { fill: none }`-Regel
  im Stylesheet des SVG schlug die `fill`-Attribute – Häuser, Grünflächen und Wasser
  waren gezeichnet, aber unsichtbar. Die Datei war korrekt, das Bild leer.
* **Der Kartenausschnitt war zu weit** (1,7 km) und brachte tausende Häuser mit,
  ohne beim Finden zu helfen. Jetzt gut 1 km – die Straßennamen ringsum sind lesbar.

---

# Vierte Runde – Cedrics Durchsicht

| Nr. | Cedrics Punkt | Umsetzung |
|----|----|----|
| 56 | „Kannst du noch die Navigation fixed machen?“ | **Erledigt.** `position: sticky` auf dem Kopfbereich, nicht `fixed` – so bleibt er im Fluss, liegt beim Laden über dem Kopfbild wie bisher, und es muss kein Platzhalter untergeschoben werden. Damit er auf dem Handy nicht dauerhaft ein Zehntel des Bildschirms kostet, **schrumpft er beim Scrollen**: kleineres Logo, kleinerer Schriftzug, die Unterzeile blendet aus – von 75 px auf 56 px. Umgeschaltet über einen Beobachter auf einen Merker am Seitenanfang, nicht über einen Scroll-Behandler; es ruckelt also nichts. |
| 57 | „Wenn noch nicht gemacht: dass die Nav auch auf Mobile optimiert ist – aktuell funktioniert das Menü auf iOS gar nicht“ | **Erledigt, und die Ursache war eine andere als vermutet.** Siehe unten. Das Menü ist jetzt ein **Vollbild-Overlay**: Sternenhimmel mit zwei gegenläufig atmenden Ebenen, warmer Goldschein aus der Ecke, die Einträge steigen versetzt von unten ein. Die aktuelle Seite bekommt eine goldene Marke, nicht nur eine andere Farbe. |

## Warum das Menü tot war

Nicht iOS. **Im Vorschau-Paket war `nav.js` zweimal eingebunden** – auf jeder
Seite. Zwei Kopien heißen zwei Klick-Behandler am Menüknopf, beide schalten den
Zustand um: das Menü öffnete und schloss sich im selben Klick. Auf jedem Gerät,
nicht nur auf iOS. Die WordPress-Seite war nie betroffen, dort lädt WordPress das
Skript einmal – deshalb ist es bei den bisherigen Prüfungen nie aufgefallen.

Die Ursache steckt in `tools/make-vorschau.php`: die Skript-Tags wurden über ihre
**absolute** Adresse entfernt, die an dieser Stelle aber längst auf
`dateien/theme/…` umgeschrieben war. Der Ausdruck griff ins Leere, die Tags
blieben stehen, und danach wurden dieselben Skripte noch einmal angehängt.

Drei Dinge dagegen:

1. Der Ausdruck entfernt jetzt **jedes** Skript mit Quellangabe, unabhängig vom
   Präfix.
2. `nav.js` und `mehr-anzeigen.js` haben eine **Sperre gegen den zweiten
   Durchlauf**. Ein doppelter Einbau kann damit nie wieder zum Totalausfall führen.
3. **`tools/vorschau-check.mjs`** (neu) prüft das fertige Paket und tippt dabei
   wirklich auf den Menüknopf. `build.sh` baut kein Paket mehr, wenn die Prüfung
   etwas meldet.

## iOS-Fallen, die dabei mitbehoben sind

* **`100dvh` statt `100vh`** – sonst schneidet die ein- und ausfahrende
  Adressleiste das Menüende ab.
* **Seite sperren über `position: fixed`** mit gemerktem Scrollstand statt über
  `overflow: hidden`, das iOS Safari übergeht. Der Stand wird beim Schließen auf
  den Pixel genau wiederhergestellt – in 0, 300, 1400 und 3200 px nachgemessen.
* **Kein Verlass mehr auf Klicks, die bis `document` durchblubbern** – auf iOS bei
  nicht-interaktiven Elementen unzuverlässig. Das Overlay ist selbst die
  Schließfläche.
* `-webkit-backdrop-filter` überall dort mitgeführt.

## In dieser Runde zusätzlich gefunden

* **Tippflächen unter 44 px**, zehn Stück bei 390 px Breite: der Menüknopf (34 px),
  die Sprungknöpfe „Samstagabend / Sonntagnachmittag / Ort und Anfahrt“ (33 px),
  „Mehr anzeigen“ (30 px) und das Fußmenü (18 px). Alle vergrößert. Einzelne
  Textlinks im Fließtext – E-Mail, Telefon, Vereinsname – bleiben unangetastet;
  die aufzublasen zerreißt den Zeilenfall.
* **Der Abstand für Sprungmarken war geraten** (`:target { scroll-margin-top: 6rem }`)
  und hätte bei einem größeren Logo nicht mehr gepasst. Jetzt misst `nav.js` die
  tatsächliche Höhe des Kopfbereichs, und `scroll-padding-top` am `html` rechnet
  damit – das gilt für Ankerlinks, Tastaturfokus und Suchen auf der Seite
  gleichermaßen.
* **Das Menü überdeckte die eigene Marke und den Schließknopf.** Es liegt im
  Kopfbereich, seine Ebenennummer gilt also nur dort – und war höher als die der
  Marke. Aufgefallen erst beim Hinsehen, nicht bei der Messung.
* **Die Markierung der aktuellen Seite erschien doppelt**, weil die Regel an
  `:focus` hing und beim Öffnen des Menüs der Fokus auf den ersten Eintrag springt.
  Jetzt `:focus-visible` – wer mit der Tastatur unterwegs ist, sieht sie weiterhin.
* **Ein fehlender Text hätte den Knopf leer stehen lassen:** die Vorschau brachte
  nur einen Teil der Beschriftungen mit, und `window.lzNavTexte || Vorgaben`
  verwarf die Vorgaben komplett. Die Texte werden jetzt ergänzt statt ersetzt.
* **Die Testinstanz ließ sich nicht zweimal aufsetzen**, weil eine Erfolgsmeldung
  in die Seiten-ID rutschte und `LZ_REPO` im Förderer-Band nicht ankam.

---

# Fünfte Runde – Cedrics Durchsicht

| Nr. | Cedrics Punkt | Umsetzung |
|----|----|----|
| 58 | „Die Häuserreihe sollte darunter vielleicht noch einen Trennstrich haben, dass man nicht so in den folgenden Content hineinfällt“ | **Erledigt.** Eine goldene Haarlinie am unteren Rand des Kopfbereichs, an beiden Enden auslaufend. Sie liest sich als Boden, auf dem die Häuser stehen. |
| 59 | „Es sieht so aus, als ob die Häuserreihe dort starten sollte, wo der Bildschirm zu Ende ist – auf anderen Größen liegt sie mittendrin“ | **Erledigt.** Genau so war es: der Kopfbereich wuchs mit dem Inhalt, nicht mit dem Bildschirm. Nachgemessen lag die Dachlinie zwischen 269 px **über** und 389 px **unter** dem Rand – auf dem iPhone 15 zufällig fast genau. Jetzt reicht der Kopfbereich bis `100svh` minus Kopfzeilenhöhe. Ergebnis über elf Fenstergrößen: **sieben sitzen genau**, der Rest liegt knapp darunter (iPhone SE +100 px, kleiner Laptop +50 px, Handy quer +258 px) – dort passt der Inhalt schlicht nicht in die Bildschirmhöhe. Über dem Rand, also mit Leere darunter, liegt sie **nirgends mehr**. |
| 60 | „Überprüf nochmal alle Metadaten. Alles aktuell? Kann man noch mehr ergänzen?“ | **Erledigt.** Siehe unten. |
| 61 | „Kann man für KI-Modelle unsichtbare Daten hinzufügen?“ | **Erledigt – auf dem sauberen Weg.** Strukturierte Daten stark ausgebaut, dazu eine `llms.txt`. Was ich **nicht** gemacht habe: versteckten Text mit Schlagwörtern. Das ist Cloaking, verstößt gegen die Richtlinien aller Suchmaschinen und kann die Seite aus dem Index werfen. Strukturierte Daten sind für Maschinen gedacht, unsichtbar für Besucher und ausdrücklich erwünscht – sie leisten dasselbe, ohne das Risiko. |
| 62 | „Ein Developer-Watermark, aber so, dass keine KI sagt: die Website ist von Fruxz“ | **Erledigt, an drei Stellen** – und die Trennung wird geprüft. Siehe unten. |
| 63 | „Favicon nicht vergessen (wenn das bei WordPress überhaupt vom Theme festgelegt werden muss)“ | **Erledigt.** Kurze Antwort: WordPress verwaltet das Website-Icon selbst über den Customizer, ein Theme kann es nicht setzen – wohl aber einspringen, solange keins hinterlegt ist. Vorher waren auf allen neun Seiten **null** Icon-Verweise. Jetzt liegt ein eigenes `favicon.svg` im Theme, für 16 px gezeichnet statt das große Signet verkleinert, dazu ein `apple-touch-icon.png`. Sobald Brigitta ein eigenes Icon setzt, gilt ihres. Der Schritt steht in SETUP.md. |

## Was die Metadaten jetzt können

Vorher: zwei lose `Event`-Objekte, nur auf der Startseite. Jetzt ein
zusammenhängender Graph auf **jeder** Seite:

* das Fest als `Festival` mit beiden Tagen als Unterterminen – der
  Zusammenhang, den vorher nur der Fließtext hergab
* der Ort mit **Koordinaten** und Kartenverweis (die Koordinaten standen
  bisher nur im Kartenskript)
* der Veranstalter mit E-Mail und Telefon
* Zielgruppe und Mindestalter je Tag, Sprache, freier Eintritt
* auf Unterseiten zusätzlich die Seite selbst und ihr Weg von der Startseite

Alles aus den Eckdaten im Customizer – vier neue Felder dafür: Breitengrad,
Längengrad, „für wen“ Samstag und Sonntag.

Dazu `og:image` mit Maßen und Alternativtext und ein `generator`-Feld.

**`/llms.txt`** fasst die Website in reinem Text zusammen: beide Tage, Ort mit
Koordinaten, Anfahrt, Eintritt, Kontakt und alle Seiten mit je einem Satz.
Ebenfalls aus den Eckdaten, kann also nicht veralten.

**Seitenbeschreibungen:** Die automatische Fassung schneidet mitten im Satz ab.
Für jede Seite gibt es jetzt einen fertigen Auszug. *(Seit der achten Runde
trägt der Import ihn selbst ein; er steht in `inhalte/seiten.json`.)*

## Die Signatur – und was sie nicht darf

Der Hinweis auf die Werkstatt steht an drei Stellen:

1. als Kommentarblock ganz oben im Quelltext jeder Seite
2. als `Author` im Kopf der `style.css` (in WordPress unter *Design → Themes*)
3. als `<meta name="generator">`, knapp und werkzeughaft wie bei WordPress selbst

Er steht **nicht** in den strukturierten Daten, **nicht** in `meta name="author"`,
**nicht** in der `llms.txt` und **nicht** im sichtbaren Text. Dort steht überall
der Verein als Veranstalter.

`tools/meta-check.mjs` prüft genau diese Trennung bei jedem Durchlauf: der
Hinweis muss im Quelltext stehen und darf in keiner maschinenlesbaren Angabe
über das Fest vorkommen. **Gegenprobe:** ein `creator: "Fruxz"` im Graphen und
ein Satz in der llms.txt werden beide sofort gemeldet.

## In dieser Runde zusätzlich gefunden

* **Zwei Überschriftensprünge:** „Die Erzählenden“ und „Kontakt“ begannen ihren
  Inhalt mit `h3` direkt unter der `h1`. Auf `h2` gehoben; damit die Namen der
  Erzählenden dabei nicht plötzlich riesig werden, behalten sie im Porträt ihre
  kleinere Größe.
* **Der Zierstrich im Kopfbereich verschwand**, als der Kopfbereich zur
  Flex-Spalte wurde: WordPress zentriert Blöcke über automatische Ränder, und
  die verhindern in einer Flex-Spalte, dass sich ein Block auf die Breite zieht.
  Der Strich besteht nur aus einer Maske – ohne Breite war er 0 px und damit
  unsichtbar. Neue Prüfung „Gestaltetes Element ohne Fläche“ fängt diese Sorte
  Fehler künftig ab.
* **Die Testinstanz antwortete nach dem Aufsetzen mit 404 auf alle Unterseiten**,
  weil die Adressregeln erneuert werden, bevor es die Seiten gibt. Man musste das
  Skript zweimal starten.
* **`/llms.txt` wurde auf `/llms.txt/` umgeleitet** – WordPress hängt an Adressen
  ohne Endung einen Schrägstrich an. Für diese eine Adresse bleibt die Umleitung
  jetzt aus.

---

# Sechste Runde – Backend

| Nr. | Cedrics Punkt | Umsetzung |
|----|----|----|
| 64 | „Kannst du die llms.txt über z. B. Theme-Einstellungen bearbeitbar machen? Sodass quasi nichts baked in ist“ | **Erledigt.** Unter *Design → Lindenzauber* steht der Text in einem großen Feld. **Leer heißt automatisch**: dann entsteht er bei jedem Aufruf aus den Eckdaten und kann nicht veralten – so kommt das Theme. Ein Knopf setzt den erzeugten Text zum Bearbeiten ein, damit Brigitta nicht vor einem leeren Feld sitzt; ein zweiter stellt den Automatikbetrieb wieder her. Im leeren Feld steht der aktuelle Text blass als Platzhalter, man sieht also immer, was ausgeliefert wird. |
| 65 | „Ggf. ein Popup beim Einrichten, das deine Punkte ‚das sollte gemacht werden‘ anzeigt“ | **Erledigt, und besser als ein Popup.** Auf derselben Seite steht die Einrichtungsliste – aber sie **hakt sich selbst ab**: jeder der elf Punkte fragt WordPress nach seinem Stand (Logo gesetzt? Symbol? Menüs zugewiesen? Förderer-Widget gefüllt? Auszüge geschrieben? Plakat statt Platzhalter? Adressregeln da?). Nichts wird von Hand abgehakt, nichts kann falsch abgehakt werden. Neben jedem offenen Punkt steht ein Knopf, der genau dorthin führt. |
| — | „Ich weiß nicht, ob man das über ein Theme sauber machen kann“ | **Ja.** `add_theme_page()` für die Seite, `admin_notices` für den Hinweis, `set_theme_mod()` für den Text – alles Bordmittel, kein Plugin, keine eigene Datenbanktabelle. Beim Wechsel auf ein anderes Theme verschwindet die Seite rückstandslos; der Text bleibt als Theme-Einstellung liegen und ist beim Zurückwechseln wieder da. |

## Was die Seite noch kann

* **Ein Hinweis im Backend**, solange etwas offen ist: „Lindenzauber – noch 4
  Schritte bis alles steht.“ Er verschwindet von selbst, sobald nichts mehr
  fehlt, und lässt sich vorher wegklicken, ohne wiederzukommen. Neben
  *Lindenzauber* im Menü steht die Zahl der offenen Punkte.
* **Ein Punkt kann wieder aufgehen.** Liegt der hinterlegte Termin in der
  Vergangenheit, meldet die Liste das und erinnert daran, die Eckdaten auf das
  nächste Fest umzustellen. Damit ist die Seite nicht nur eine
  Einrichtungshilfe, sondern eine Erinnerung fürs nächste Jahr.
* **Adressregeln erneuern** als Knopf – für den Fall, dass `/llms.txt` nach der
  Installation noch nicht erreichbar ist. Vorher stand dafür in SETUP.md ein
  Umweg über die Permalink-Einstellungen.

## Geprüft wird das auch

`tools/admin-check.mjs` (neu) meldet sich im Backend an und **klickt die Seite
wirklich durch**: automatischen Text einsetzen, bearbeiten, speichern, nachsehen
was unter `/llms.txt` ankommt, zurücksetzen, nachsehen ob wieder der
automatische Text kommt, Adressregeln erneuern. Dazu wird geprüft, dass die
Liste den tatsächlichen Stand zeigt.

**Gegenprobe:** Ein Termin in der Vergangenheit lässt den Punkt „Termine des
Festes“ sofort wieder aufgehen, mit dem passenden Hinweistext.

---

# Siebte Runde – Aufräumen

Cedric hat gefragt, was noch fest eingebaut ist und aufgeräumt gehört. Der
Durchgang durch Theme, Muster und Werkzeuge hat neun Punkte gefunden.

| Was | Warum es störte | Jetzt |
|---|---|---|
| **„Märchenfest in Bassum“ stand viermal im Quelltext** – im Schriftzug, im Browser-Titel, in den Meta-Angaben und in den strukturierten Daten | WordPress führt genau diesen Satz ohnehin als **Untertitel** der Website. Zwei Quellen für dieselbe Angabe: Ändert Brigitta den Untertitel, wäre die Website mit sich selbst uneins gewesen. | Kommt aus *Einstellungen → Allgemein → Untertitel*. Der feste Satz bleibt nur als Rückfall, falls das Feld leer ist. Nachgeprüft: Untertitel geändert → Schriftzug und Browser-Titel ziehen mit. |
| **Der Satz in der Fußzeile** „Diese Website lädt keine externen Schriften, Karten oder Skripte“ | Eine Zusage über die Website – wird später ein Plugin eingebaut, das doch etwas nachlädt, stimmt sie nicht mehr. Ohne Datei anzufassen war sie nicht zu ändern. | Feld in den Eckdaten. Leer lassen heißt: Zeile fällt weg. |
| **Absolute Adressen `https://lindenzauber.de/wp-content/uploads/…`** in allen Seiteninhalten und Mustern | Bei einem Domainwechsel oder auf einer Testinstanz wären sämtliche Bilder tot gewesen. | Adressen beziehen sich jetzt auf die eigene Website (`/wp-content/uploads/…`), wie es Karte und Plakat schon taten. |
| **`screenshot.png` fehlte** | Unter *Design → Themes* stand ein graues Feld statt einer Vorschau. | Ist da, erzeugt mit `tools/make-screenshot.mjs`. |
| **`languages/` fehlte**, obwohl das Theme darauf verweist | `load_theme_textdomain()` zeigte ins Leere; die Angabe „translation-ready“ stimmte nicht. | Ordner mit `lindenzauber.pot` angelegt. |
| **Umbruchpunkt 940 px stand doppelt** – im Stylesheet und im Menü-Skript | Zwei Stellen für dieselbe Zahl laufen irgendwann auseinander. | Das Skript fragt nicht mehr nach Pixeln, sondern ob der Menüknopf noch angezeigt wird. Die Zahl steht nur noch im Stylesheet. |
| **Ort und Beschriftung der Karte standen fest im Zeichenskript** | Zieht das Fest um, hätte man die Datei bearbeiten müssen. | `--breite`, `--laenge`, `--name`, `--ort` als Aufrufparameter, die alten Werte als Voreinstellung. |
| **`icon-frei.svg`** | Wurde nirgends verwendet – weder im Theme noch in den Inhalten. | Entfernt, auch aus dem Erzeugerskript. |
| **`medien/`** enthielt nur noch eine Kopie der Karte | Dieselbe Datei zweimal im Repo; die Seiten holen sie ohnehin aus dem Theme. | Ordner aufgelöst. Die Karte liegt an genau einer Stelle. |

## Zwei Fehler, die beim Aufräumen entstanden – und wie sie auffielen

**Die Ortsmarke auf der Karte hieß plötzlich „Amselstraße“.** Beim Beweglichmachen
der Beschriftung hieß die Laufvariable der Straßenschleife genauso wie der neue
Parameter (`name`). Nach der Schleife stand dort der zuletzt gesetzte
Straßenname. Aufgefallen ist es nur, weil ich die Gegenprobe mit einem anderen
Namen gemacht habe – im Normalfall wäre das Bild einfach falsch beschriftet
gewesen.

**Das Signet auf der Startseite lud nicht mehr.** Die Umstellung auf
Website-eigene Adressen war für die echte Website richtig, hat aber die
Testumgebung ausgehebelt: Sie suchte nach der alten absoluten Adresse und fand
nichts mehr, holte also keine Bilder und schrieb keine Verweise um. Aufgefallen
beim Blick auf das neu erzeugte `screenshot.png` – dort stand der Alternativtext
statt des Bildes.

Damit so etwas nicht mehr am Zufall hängt, meldet `layout-check.mjs` jetzt auch
**Bilder, die nicht laden**. Gegenprobe mit einer erfundenen Adresse: wird sofort
gemeldet.

---

# Abnahme – Probelauf der Einrichtung

Vor der Übergabe wurde die Einrichtung einmal komplett durchgespielt, so wie
Cedric sie machen wird: **frisches WordPress, Theme aus `dist/lindenzauber.zip`
installiert** (nicht der Quellordner kopiert – so fällt auf, wenn im Paket eine
Datei fehlt), Seiten angelegt, Auszüge eingetragen, Menüs zugewiesen,
Förderer-Logos eingefügt. `tools/wp-probelauf.sh` macht das wiederholbar.

Ergebnis: Von den elf Punkten der Einrichtungsliste standen danach neun auf
erledigt. Die beiden offenen sind genau die, die Handarbeit brauchen und die
niemand automatisieren kann:

* **Plakat einsetzen** – die Datei hat nur Brigitta.
* **Kurzbeschreibung der „Beispielseite“** – WordPress' eigene Musterseite. Auf
  lindenzauber.de kann sie gelöscht werden.

> Nachtrag: Seit dem Seiten-Import (achte Runde) fällt der zweite Punkt weg –
> die Beispielseite wird beim Import mit stillgelegt. Der Probelauf geht
> denselben Weg wie SETUP.md und lässt jetzt den Import die Arbeit machen.

Alle Werkzeuge liefen anschließend gegen diese frisch eingerichtete Instanz:
Abstände und Klickflächen, Metadaten und strukturierte Daten, Backend, Blöcke –
keine Befunde. Keine fehlenden Bilder, keine fehlgeschlagenen Anfragen, in
1440 px und 393 px.

**Dabei gefunden und behoben:**

* Der von mir selbst vorgeschlagene Auszug für das Impressum war mit 48 Zeichen
  kürzer als das Mindestmaß, das die eigene Prüfung verlangt. Neu formuliert.
* Die Einrichtungsseite ließ sich nicht von der Kommandozeile abfragen, weil sie
  nur im Backend geladen wurde. Jetzt lädt sie auch für WP-CLI – damit lässt
  sich der Stand der Einrichtung ohne Anmeldung prüfen.

---

# Achte Runde – Seiten importieren

| Nr. | Cedrics Punkt | Umsetzung |
|----|----|----|
| 66 | „Kannst du das Seiten-Inhalt importieren mit den Theme-Optionen automatisieren? Dass ich deine Zip eingebe und alle Seiten importiert werden?“ | **Erledigt.** *Design → Lindenzauber → Seiten importieren*: `lindenzauber-inhalte.zip` hochladen, fertig. Aus acht Seiten Copy-und-Paste im Code-Editor, sieben Kurzbeschreibungen von Hand und einem Widget-Handgriff sind zwei Klicks geworden. |
| 67 | „Alte bestandene Seiten werden deaktiviert/umbenannt und nicht mehr erreichbar“ | **Erledigt, aber nur auf ausdrücklichen Wunsch.** Der Import listet alles auf, was veröffentlicht ist und nicht zum Paket gehört. Angehakt wird, was weichen soll. Gelöscht wird nichts: Die Seite kommt auf *Entwurf*, ihre Adresse bekommt `alt-` davor und der alte Adressname wird als Notiz an der Seite gemerkt. Rückgängig heißt: Seite öffnen, `alt-` entfernen, veröffentlichen. |

## Warum zwei Schritte

Ein Import, der auf Knopfdruck losläuft, ist bequem – bis er einmal das Falsche
trifft. Deshalb liegt zwischen Hochladen und Ausführen eine Vorschau, die Zeile
für Zeile zeigt, was passieren *würde*: welche Seite neu entsteht, welche ersetzt
wird, welche fremde Seite zur Auswahl steht. Nichts ist vorangehakt.

Drei Dinge kann der Import grundsätzlich nicht kaputt machen:

* **Datenschutz.** Die Seite steht im Paket auf einer Schutzliste und wird gar
  nicht erst zum Stilllegen angeboten – zusammen mit `datenschutzerklaerung` und
  `privacy-policy`, weil WordPress je nach Sprache anders benennt.
* **Adressen und Menüs.** Eine vorhandene Seite wird aktualisiert, nicht neu
  angelegt. Sie behält ihre Kennung, ihre Adresse und ihre Menüeinträge; der
  alte Stand steht danach unter *Revisionen*.
* **Ein von Hand auf Entwurf gesetzter Zustand.** Wer eine Seite bewusst
  offline genommen hat, findet sie nach dem Import nicht plötzlich wieder
  veröffentlicht.

Das Förderer-Band wird nur gefüllt, wenn dort noch nichts steht. Sonst hätte ein
zweiter Lauf die Logos verdoppelt.

## Was dabei ausgebaut wurde

**`inhalte/seiten.json` ist neu** und der einzige Ort, an dem steht, welche
Datei zu welcher Seite gehört: Adressname, Titel, Kurzbeschreibung, Status,
welche die Startseite ist und welche Adressen geschützt sind. Vorher stand das
an drei Stellen – in SETUP.md, im Probelauf-Skript und im Kopf des Bearbeiters.
Im Theme steht davon nichts; es liest den Bauplan aus dem Paket.

**Ohne `unfiltered_html` bricht der Import ab, bevor er anfängt.** Fehlt dem
Konto dieses Recht, filtert WordPress beim Speichern die HTML-Kommentare heraus –
und genau die *sind* bei Blöcken der Inhalt. Das Ergebnis wären acht Seiten
voller kaputter Blöcke gewesen. Statt dessen kommt ein Satz, der sagt, woran es
liegt.

**Zwei Lecks im Temp-Verzeichnis.** Aufgefallen an zwei Ordnern `lz-import-…`,
die nach den eigenen Tests liegengeblieben waren.

Das erste war der abgebrochene Versuch: Wer ein Paket hochlädt und die Vorschau
dann wegklickt, ließ den ausgepackten Ordner stehen – der Zwischenspeicher läuft
ab, der Ordner nicht. Jeder Upload räumt jetzt weg, was älter als zwei Stunden ist.

Das zweite war ernster und wäre ohne die Zählung nie aufgefallen: Nach dem
Aufräumen des ersten Lecks blieben **wieder genau zwei** Ordner liegen – einer
je *erfolgreichem* Import. Ursache: Ausgepackt wird beim Hochladen, weggeräumt
erst nach dem Bestätigen; das sind zwei getrennte Aufrufe. Im zweiten stand
`$wp_filesystem` noch auf `null`, die Bedingung im Aufräumer war damit still
falsch und löschte nichts. Der Aufräumer meldet das Dateisystem jetzt selbst an.
`import-check.mjs` zählt nach jedem Lauf nach; liegt noch etwas da, ist es ein
Befund.

## Geprüft wird das auch

`tools/import-check.mjs` (neu) geht den Weg, den Cedric geht: anmelden, Paket
hochladen, Vorschau lesen, bestätigen – und sieht danach in der Datenbank nach,
ob wirklich passiert ist, was in der Vorschau stand. 30 Prüfpunkte, darunter:
das Theme-Paket wird als falsches Paket erkannt; Datenschutz taucht nicht in der
Abschussliste auf; die stillgelegte Seite antwortet mit 404; die Blockangaben
haben den Weg durch WordPress unbeschadet überstanden; ein zweiter Lauf ändert
keine Kennung und verdoppelt das Förderer-Band nicht.

Der Aufruf steckt in `build.sh` und läuft auf der **noch leeren** Instanz – der
echte Erstfall. Die Vorschau-Seiten entstehen danach aus dem, was der Import
angelegt hat. Bricht der Import, bricht der Bau.

## Abnahme – der Probelauf geht jetzt durch den Import

`tools/wp-probelauf.sh` setzt ein frisches WordPress auf, installiert das Theme
aus `dist/lindenzauber.zip` und spielt die Seiten mit dem ausgelieferten
`dist/lindenzauber-inhalte.zip` ein – über dieselben Funktionen, die auch der
Knopf im Backend aufruft. Zwei Seiten stehen vorher schon da: eine, die weichen
soll, und die Datenschutzseite, die bleiben muss.

Ergebnis: acht Seiten angelegt, Startseite gesetzt, Förderer-Band gefüllt, zwei
fremde Seiten stillgelegt (`alt-veraltet`, `alt-beispielseite`), Datenschutz
unangetastet. Von den elf Punkten der Einrichtungsliste standen danach **zehn**
auf erledigt – offen blieb nur „Plakat eingesetzt“, die Datei hat nur Brigitta.
Vorher waren es zwei offene Punkte; die fehlende Kurzbeschreibung der
Beispielseite erledigt sich, weil die Seite beim Import mit stillgelegt wird.

Alle Werkzeuge liefen anschließend gegen diese Instanz – Gestaltung, Metadaten,
Blöcke, Editor: keine Befunde.

**Dabei noch eine Lücke geschlossen:** `layout-check.mjs` war das einzige
Prüfwerkzeug, das **kein** Tor in `build.sh` war – ausgerechnet das, das die
gemeldeten Fehlerklassen abdeckt. Ein Rückfall bei Abständen, Kontrast oder
Klickflächen hätte den Bau nicht aufgehalten. Jetzt schon.
