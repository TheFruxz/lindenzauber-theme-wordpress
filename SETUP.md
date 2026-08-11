# Einrichtung – Schritt für Schritt

Für Cedric. Einmal durcharbeiten, danach macht Brigitta alles im visuellen Editor.
Rechne mit etwa 20 Minuten.

> **Vorher:** Bitte eine Sicherung der bestehenden Website machen (Datenbank und
> `wp-content`). Es wird nichts gelöscht, aber Seiteninhalte werden ersetzt.

---

## 1. Theme installieren

1. In WordPress: *Design → Themes → Theme hochladen*
2. `dist/lindenzauber.zip` auswählen, *Jetzt installieren*, dann *Aktivieren*

Das alte Theme kann liegen bleiben; es wird nur nicht mehr benutzt.

## 2. Logo setzen

*Design → Customizer → Website-Informationen → Logo* → das Lindenblatt-Logo aus
der Mediathek wählen (die bestehende Datei `cropped-cropped-Logo-freigestellt.png`).

Es erscheint dann links oben im Kopfbereich neben dem Schriftzug.

## 3. Eckdaten eintragen

*Design → Customizer → Lindenzauber → Eckdaten des Festes*

Alles ist mit den aktuellen Angaben vorbelegt – bitte einmal durchsehen. Wichtig
sind besonders die Felder **Beginn** und **Ende** im Format `2026-09-26 19:00`:
Daraus entstehen die Angaben, die Google für die Veranstaltung anzeigt.

Diese Angaben versorgen gleichzeitig die Fußzeile. Nächstes Jahr müssen nur diese
Felder geändert werden.

## 4. Vorschaubild fürs Teilen

*Design → Customizer → Lindenzauber → Vorschaubild beim Teilen* → am besten das
Plakat. Dieses Bild erscheint, wenn jemand einen Link in WhatsApp, Signal oder
Facebook einfügt.

## 5. Seiteninhalte einfügen

Für jede Seite einmal:

1. Seite öffnen: *Seiten → die Seite → Bearbeiten*
2. Oben rechts auf **⋮** (die drei Punkte) → **Code-Editor**
3. Alles markieren und löschen
4. Den kompletten Inhalt der passenden Datei aus `inhalte/` einfügen
5. Über **⋮** zurück auf **Visueller Editor**
6. *Aktualisieren*

| Seite | Datei |
|---|---|
| Startseite | `inhalte/01-startseite.html` |
| Programm | `inhalte/02-programm.html` |
| Die Erzählenden | `inhalte/03-die-erzaehlenden.html` |
| Über Lindenzauber | `inhalte/04-ueber-lindenzauber.html` |
| Förderer | `inhalte/05-foerderer.html` |
| Kontakt | `inhalte/06-kontakt.html` |
| Impressum | `inhalte/07-impressum.html` |
| Fotogalerie *(neu anlegen, als Entwurf)* | `inhalte/08-fotogalerie.html` |

**Datenschutz bleibt unverändert.** Der bestehende Text ist juristisch erzeugt und
wird nur neu gestaltet – da ist nichts zu tun.

> **Achtung, das ist wichtig:** Auf der Seite `/kontakt/` steht derzeit das
> **Impressum**, und auf `/impressum/` stehen Brigittas Kontaktdaten. Die beiden
> sind vertauscht. Mit den Dateien oben wird das richtiggestellt – bitte nicht
> verwechseln.

**Alternative ohne Copy-und-Paste:** Bei einer leeren Seite bietet WordPress unten
„Muster wählen“ an. Dort liegen dieselben Seiten unter *Lindenzauber* bereit
(„Seite: Startseite“, „Seite: Programm“ …). Ein Klick genügt.

## 5a. Kurzbeschreibungen eintragen

Diese Texte erscheinen bei Google unter dem Seitentitel und in der Vorschau,
wenn jemand einen Link teilt. Ohne sie nimmt die Website die ersten Sätze der
Seite – die enden dann mitten im Satz.

Je Seite: *Bearbeiten* → rechte Leiste → Reiter **Seite** → Feld **Auszug**.

| Seite | Auszug |
|---|---|
| Startseite | Märchenfest in Bassum am 26. und 27. September 2026: Märchenabend für Erwachsene, Märchentag für Familien. Eintritt frei, keine Anmeldung nötig. |
| Programm | Samstagabend für Erwachsene, Sonntagnachmittag für Familien – alle Zeiten, der Ablauf mit den vier Erzählräumen und die Anfahrt zum Kindergarten KinderReich. |
| Die Erzählenden | Sechs Erzählerinnen und Erzähler, sechs Geschichtenwelten: wer beim Lindenzauber erzählt, wie sie erzählen und wann Sie wen hören. |
| Über Lindenzauber | Wie aus einer Idee ein Märchenfest wurde: die Menschen dahinter, die beiden Festtage und alles Wichtige in Kürze. |
| Förderer | Vier Förderer machen den Lindenzauber möglich – deshalb ist der Eintritt an beiden Tagen frei. |
| Kontakt | Fragen zum Lindenzauber? Brigitta Wortmann ist per E-Mail, Telefon und WhatsApp erreichbar. |
| Impressum | Pflichtangaben nach § 5 DDG für lindenzauber.de. |

## 5b. Symbol für den Browser-Tab

*Design → Customizer → Website-Informationen → Website-Icon*

Ein quadratisches Bild ab 512 × 512 Pixel – am besten das Lindenblatt-Signet.
Es erscheint im Browser-Tab und als Symbol, wenn jemand die Seite auf den
Startbildschirm legt.

> Solange dort nichts hinterlegt ist, zeigt das Theme ein eigenes Blatt in
> Gold auf Nachtblau. Es sieht nicht kaputt aus – aber Brigittas eigenes
> Signet ist schöner.

## 6. Plakat einsetzen

Auf der Startseite steht im Abschnitt „Das Plakat zum Lindenzauber“ zunächst ein
gestalteter goldener Rahmen als Platzhalter.

1. Plakat in die Mediathek laden (*Medien → Datei hinzufügen*)
2. Startseite bearbeiten, den Platzhalter anklicken
3. In der Werkzeugleiste über dem Bild auf **Ersetzen → Mediathek öffnen**
4. Das Plakat auswählen, *Aktualisieren*

## 7. Menüs zuweisen

*Design → Menüs*

**Hauptmenü (oben)** – vorhandenes Menü übernehmen oder neu anlegen:
Start · Programm · Die Erzählenden · Über Lindenzauber · Förderer

**Fußzeile: Kontakt, Impressum, Datenschutz** – zweites Menü:
Kontakt · Impressum · Datenschutz

Beide Menüs unter *Positionen des Menüs* dem passenden Platz zuweisen.

> Wenn kein Menü zugewiesen ist, zeigt das Theme automatisch eine sinnvolle
> Linkliste. Es sieht also nie kaputt aus.

## 8. Förderer im Fußbereich

*Design → Widgets → Förderer (Fußbereich)*

Der bequemste Weg: auf **+** klicken, oben nach *Lindenzauber* suchen und das Muster
**„Förderer-Band (für die Fußzeile)“** einfügen. Fertig.

Alternativ den Inhalt von `inhalte/09-foerderband-widget.html` kopieren, in den
leeren Widget-Bereich klicken und mit `Strg+V` einfügen – WordPress erkennt den
Blockcode und macht daraus vier Bild-Blöcke.

Die Logos zeigen zunächst auf die vorhandenen Dateien in der Mediathek. Zum
Austauschen genügt ein Klick auf das jeweilige Logo → *Ersetzen*.

Die Logos erscheinen dann auf hellen Kacheln als erste Zeile im Fußbereich –
auf allen Seiten **außer der Startseite**. Dort stehen die Förderer weiter oben
groß und präsent; zweimal untereinander wäre doppelt.

Ist der Bereich leer, wird das weiße Band einfach nicht angezeigt – es entsteht
kein halb fertiger Streifen.

## 9. Fotogalerie vorbereiten

Die Seite „Fotogalerie“ bleibt auf **Entwurf**, bis es Fotos gibt. Sie enthält
bereits Einleitung, Galerie-Raster und Abschluss mit drei Platzhalterbildern.

Wenn die Fotos da sind: Galerie anklicken → Bilder ersetzen bzw. ergänzen → Seite
veröffentlichen → im Menü verlinken.

## 10. Zum Schluss durchsehen

- [ ] Startseite: Logo sichtbar, Plakat eingesetzt
- [ ] Programm: Karte wird angezeigt, die drei Sprungmarken funktionieren
- [ ] Die Erzählenden: Bild wechselt die Seite, „Mehr anzeigen“ erscheint bei langen Texten
- [ ] Kontakt zeigt Kontaktdaten (nicht das Impressum)
- [ ] Impressum zeigt die Pflichtangaben
- [ ] Fußzeile: Kontakt, Impressum, Datenschutz vorhanden
- [ ] Auf dem Handy prüfen: nichts lässt sich seitlich verschieben, das Menü öffnet sich
- [ ] Einen Link in WhatsApp einfügen: Vorschaubild und Text stimmen
- [ ] Symbol im Browser-Tab ist da
- [ ] `lindenzauber.de/llms.txt` im Browser aufrufen – es muss eine Textseite
      mit Terminen, Ort und Kontakt erscheinen. Kommt stattdessen „Seite nicht
      gefunden", einmal *Einstellungen → Permalinks → Änderungen speichern*
      klicken; das erneuert die Adressregeln.

---

## Wenn etwas nicht stimmt

**Ein Abschnitt sieht falsch aus.** Meist ist ein Block-Stil verrutscht. Block
anklicken, rechts unter *Stile* den passenden wieder auswählen.

**Eine Seite ist zu schmal oder zu breit.** Die äußerste Gruppe braucht die
Ausrichtung *Volle Breite*. Gruppe anklicken → Ausrichtungs-Symbol in der
Werkzeugleiste → *Volle Breite*.

**Der Editor zeigt „Dieser Block enthält unerwarteten oder ungültigen Inhalt“.**
Dann ist beim Einfügen etwas abgeschnitten worden. Am einfachsten: Seiteninhalt
noch einmal komplett aus der Datei in `inhalte/` einfügen.

**Die Schwungschrift erscheint nicht.** Einmal den Browser-Cache leeren. Falls ein
Caching-Plugin läuft, dort ebenfalls den Cache löschen.
