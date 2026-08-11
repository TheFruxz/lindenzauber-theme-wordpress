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
