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
| `inhalte/` | Der Blockcode für jede Seite, zum einmaligen Einfügen. Siehe [SETUP.md](SETUP.md). |
| `medien/` | Die Anfahrtskarte als SVG (liegt auch schon im Theme). |
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
*Design → Customizer → Lindenzauber*. Von dort speisen sie die Fußzeile **und**
die Angaben für Google. Im nächsten Jahr genügt es, dort die Daten zu ändern.

---

## Datenschutz

Die Website lädt beim Aufruf nichts von fremden Servern:

* Schriften liegen im Theme.
* Die Anfahrtskarte ist eine Zeichnung im Theme, einmalig aus OpenStreetMap-Daten
  erzeugt (© OpenStreetMap-Mitwirkende, ODbL) – kein Kartendienst, keine Kacheln
  vom fremden Server. Der Knopf „Route planen“ öffnet OpenStreetMap erst, wenn
  jemand ihn anklickt.
* Keine Analysewerkzeuge, keine Einbettungen, keine Cookies vom Theme.

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
node tools/block-check.mjs  <url> <benutzer> <passwort> inhalte/*.html   # gültige Kernblöcke?
node tools/editor-check.mjs <url> <benutzer> <passwort> <seiten-id...>   # Warnungen im Editor?
node tools/layout-check.mjs <url>                                       # Abstände, Klickflächen, Kontrast
node tools/shot.mjs         <url> <ziel.png> [breite] [full]             # Screenshot
```

`layout-check.mjs` geht alle Seiten in 390 px, 768 px und 1440 px durch und meldet
fehlende Abstände zwischen Blöcken, außermittige Abschnitte, Flächen die klickbar
aussehen aber keine sind, seitlichen Überlauf, zu schwachen Kontrast, Leerraum
hinter dem Fußbereich und Bilder, deren eigener Grund nicht zur Kachel darunter
passt (Eckpixel gegen die tatsächlich sichtbare Hintergrundfarbe). Nach jeder
Änderung an der Gestaltung einmal laufen lassen – es muss „Keine Befunde“ herauskommen.

Die Karte wird nur neu erzeugt, wenn sich der Ausschnitt ändern soll:

```bash
python3 tools/make-karte.py     # fragt OpenStreetMap ab und zeichnet das SVG
```

---

## Lizenzen

* Theme: GPL v2 oder später.
* Schriften *Lato* und *Great Vibes*: SIL Open Font License 1.1 – siehe
  `theme/lindenzauber/assets/fonts/LIZENZ.md`.
* Kartenausschnitt: © OpenStreetMap-Mitwirkende, ODbL.
* Logos der Förderer und Porträtfotos: bei den jeweiligen Rechteinhabern.
