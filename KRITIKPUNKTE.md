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
| 19 | „Dahinter platzhalter mit dem Flyer ausfüllen“ | **Erledigt.** Direkt nach den beiden Karten kommt der Abschnitt „Das Plakat zum Lindenzauber“. Solange das Plakat fehlt, steht dort ein gestalteter goldener Rahmen statt eines kaputten Bildes – siehe SETUP.md, Schritt 6. |
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
