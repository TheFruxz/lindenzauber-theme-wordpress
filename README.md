# Lindenzauber – WordPress-Theme

Theme und Seiteninhalte für **lindenzauber.de**, das Märchenfest in Bassum am
26. und 27. September 2026. Gestaltung nach dem Plakat: Nachthimmel, Laternenlicht,
Lindenbaum und Gold.

Das Wichtigste in einem Satz: **Alles auf der Website ist mit dem normalen
WordPress-Editor bearbeitbar** – ohne Code, ohne Plugins, ohne Umwege.

---

## Was hier drin liegt

| Ordner / Datei | Wofür |
|---|---|
| `dist/lindenzauber.zip` | **Das Theme.** In WordPress unter *Design → Themes → Theme hochladen* installieren. |
| `dist/lindenzauber-vorschau.zip` | **Die Vorschau.** Entpacken, `index.html` doppelklicken – die ganze Website ohne WordPress anschauen. |
| `dist/lindenzauber-inhalte.zip` | **Die Seiteninhalte.** Dieselben Dateien wie in `inhalte/`, als ein Download. |
| `inhalte/` | Der Blockcode für jede Seite, zum einmaligen Einfügen. Siehe [SETUP.md](SETUP.md). |
| `theme/lindenzauber/` | Der Quellcode des Themes. |
| `tools/` | Hilfsskripte zum Bauen und Prüfen. Werden für den Betrieb nicht gebraucht. |
| [SETUP.md](SETUP.md) | Einrichtung, Schritt für Schritt. Für Cedric. |
| [HANDBUCH.md](HANDBUCH.md) | Bedienung ohne Code. Für Brigitta. |
| [KRITIKPUNKTE.md](KRITIKPUNKTE.md) | Jeder Punkt aus Brigittas Craft-Dokument und was daraus geworden ist. |

## Loslegen

1. `dist/lindenzauber-vorschau.zip` entpacken und `index.html` öffnen – so sieht es aus.
2. Passt es? Dann weiter mit [SETUP.md](SETUP.md).

---

## Was das Theme mitbringt

**Gestaltung nach dem Plakat.** Zwei Schriften liegen im Theme: *Great Vibes* für
die Schwungschrift („Lindenzauber“, „Märchen“, „Familien“) und *Lato* für alles
andere. Beide werden vom eigenen Server geladen – es geht keine Anfrage an Google
oder einen anderen Dienst. Nachthimmel, Sternenfeld, Laternenschein, Lindenbaum
und Dorfsilhouette sind als CSS und SVG nachgebaut, nicht als große Bilddateien.

**Bearbeitbar bleiben.** Drei Dinge greifen ineinander:

* **`theme.json`** legt Farben, Schriften, Abstände und Blockvorgaben fest. Was
  Brigitta mit einem gewöhnlichen Absatz oder einer Überschrift einfügt, sieht
  sofort richtig aus.
* **Block-Stile** erscheinen im Editor rechts als anklickbare Karten: „Info-Karte“,
  „Festtag-Karte“, „Förderer-Kachel“, „Banderole“, „Zeiten als Chips“ und so
  weiter. Keine Klassennamen, keine Kürzel.
* **Muster** unter *Einfügen → Lindenzauber*: ganze Abschnitte auf einen Klick.

**Kein einziger Roh-HTML-Block.** Automatisch geprüft mit `tools/block-check.mjs`:
342 Blöcke, alle gültige Kernblöcke.

**Zwei Dinge laufen von selbst**, damit im Editor nichts eingestellt werden muss:
Bei den Porträts wechselt das Bild abwechselnd die Seite, und lange Porträttexte
werden ab etwa acht Zeilen weich ausgeblendet und bekommen „Mehr anzeigen“. Ohne
JavaScript steht schlicht der vollständige Text da.

**Termine an einer Stelle.** Datum, Uhrzeit, Ort und Kontakt stehen unter
*Design → Customizer → Lindenzauber*. Von dort speisen sie die Fußzeile, die
Angaben für Google **und** die `llms.txt`. Im nächsten Jahr genügt es, dort die
Daten zu ändern.

**Eine Einrichtungsseite, die sich selbst abhakt.** Unter *Design → Lindenzauber*
steht, was noch fehlt – Logo, Symbol, Menüs, Förderer-Logos, Kurzbeschreibungen,
Plakat. Jeder Punkt fragt WordPress nach seinem Stand, es wird nichts von Hand
abgehakt. Solange etwas offen ist, weist ein Hinweis im Backend darauf hin. Auf
derselben Seite lässt sich der Text für `/llms.txt` bearbeiten.

---

## Datenschutz

Die Website lädt beim Aufruf nichts von fremden Servern:

* Schriften liegen im Theme.
* Die Anfahrtskarte ist eine Zeichnung im Theme, einmalig aus OpenStreetMap-Daten
  erzeugt (© OpenStreetMap-Mitwirkende, ODbL) – kein Kartendienst, keine Kacheln
  vom fremden Server. Der Knopf „Route planen“ öffnet OpenStreetMap erst, wenn
  jemand ihn anklickt.
* Keine Analysewerkzeuge, keine Einbettungen, keine Cookies vom Theme.
* Das Symbol im Browser-Tab liegt im Theme, solange keins eingestellt ist.

---

## Neu bauen

```bash
bash build.sh                                  # nur das Theme-Paket
LZ_WORK=/pfad/zum/arbeitsordner bash build.sh  # Theme und Vorschau
```

Für die Vorschau werden im Arbeitsordner `wordpress.zip`, `sqlite.zip` und
`wp-cli.phar` erwartet. Der Build setzt daraus eine echte WordPress-Instanz auf,
legt alle Seiten an und holt sich die fertigen Seiten von dort. Dadurch können
Vorschau und spätere Website nicht auseinanderlaufen.

Prüfwerkzeuge:

```bash
node tools/block-check.mjs    <url> <benutzer> <passwort> inhalte/*.html # gültige Kernblöcke?
node tools/editor-check.mjs   <url> <benutzer> <passwort> <seiten-id...> # Warnungen im Editor?
node tools/layout-check.mjs   <url>                                     # Abstände, Klickflächen, Kontrast
node tools/vorschau-check.mjs <ordner>                                  # das Vorschau-Paket
node tools/meta-check.mjs     <url>                                     # Metadaten, strukturierte Daten, llms.txt
node tools/admin-check.mjs    <url> <benutzer> <passwort>               # die Seite Design → Lindenzauber
node tools/make-screenshot.mjs <url>                                    # Vorschaubild des Themes erneuern

# Probelauf der Einrichtung: frisches WordPress, Theme aus dem fertigen ZIP,
# SETUP.md Schritt für Schritt. Findet auch Dateien, die im Paket fehlen.
LZ_WORK=/pfad/zum/arbeitsordner bash tools/wp-probelauf.sh
node tools/shot.mjs           <url> <ziel.png> [breite] [full]          # Screenshot
```

`layout-check.mjs` geht alle Seiten in 390 px, 768 px und 1440 px durch und meldet
fehlende Abstände zwischen Blöcken, außermittige Abschnitte, Flächen die klickbar
aussehen aber keine sind, seitlichen Überlauf, zu schwachen Kontrast, Leerraum
hinter dem Fußbereich, Bilder deren eigener Grund nicht zur Kachel darunter passt
(Eckpixel gegen die tatsächlich sichtbare Hintergrundfarbe), Sprungziele die unter
dem festen Kopfbereich landen und Bedienelemente unter 44 × 44 px. Nach jeder
Änderung an der Gestaltung einmal laufen lassen – es muss „Keine Befunde“ herauskommen.

`vorschau-check.mjs` prüft das fertige Vorschau-Paket: jedes Skript genau einmal
eingebunden, alle Dateien da, und – wichtig – der Menüknopf öffnet das Menü
wirklich. Der Aufruf steckt in `build.sh`; bei Befunden wird kein Paket gebaut.

Die Karte wird nur neu erzeugt, wenn sich der Ort oder der Ausschnitt ändern
soll. Ort und Beschriftung stehen als Voreinstellung im Skript und lassen sich
überschreiben – sie müssen zu den Koordinaten im Customizer passen:

```bash
python3 tools/make-karte.py
python3 tools/make-karte.py --breite 52.85 --laenge 8.73 --name "Neuer Ort" --ort Bassum
```

`screenshot.png` – das Bild unter *Design → Themes* – wird ebenfalls von Hand
erneuert, wenn sich die Gestaltung deutlich ändert.

---

## Lizenzen

* Theme: GPL v2 oder später.
* Schriften *Lato* und *Great Vibes*: SIL Open Font License 1.1 – siehe
  `theme/lindenzauber/assets/fonts/LIZENZ.md`.
* Kartenausschnitt: © OpenStreetMap-Mitwirkende, ODbL.
* Logos der Förderer und Porträtfotos: bei den jeweiligen Rechteinhabern.

---

## Für Suchmaschinen und Sprachmodelle

Jede Seite trägt einen zusammenhängenden Datensatz nach schema.org: das Fest
mit beiden Tagen als Unterterminen, der Ort mit Koordinaten und Kartenverweis,
der Veranstalter mit Kontakt, dazu Zielgruppe, Sprache und der freie Eintritt.
Unterseiten bringen zusätzlich sich selbst und ihren Weg von der Startseite
mit. Alles kommt aus den Eckdaten im Customizer – es steht nichts davon im
Quelltext fest.

Dazu liegt unter `/llms.txt` eine Kurzfassung in reinem Text: worum es geht,
beide Tage, Ort, Anfahrt, Eintritt, Kontakt und alle Seiten mit je einem Satz.
Sie entsteht aus den Eckdaten und kann deshalb nicht veralten – lässt sich unter
*Design → Lindenzauber* aber auch von Hand schreiben. Nichts davon steht im
Quelltext fest.

`tools/meta-check.mjs` hält beides nach.
