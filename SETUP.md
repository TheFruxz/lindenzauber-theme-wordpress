# Einrichtung – Schritt für Schritt

Für Cedric. Einmal durcharbeiten, danach macht Brigitta alles im visuellen Editor.
Rechne mit etwa 15 Minuten.

> **Vorher:** Bitte eine Sicherung der bestehenden Website machen (Datenbank und
> `wp-content`). Es wird nichts gelöscht, aber Seiteninhalte werden ersetzt.

---

## 1. Theme installieren

1. In WordPress: *Design → Themes → Theme hochladen*
2. `dist/lindenzauber.zip` auswählen, *Jetzt installieren*, dann *Aktivieren*

Das alte Theme kann liegen bleiben; es wird nur nicht mehr benutzt.

> **Danach steht die Liste im Backend.** Unter *Design → Lindenzauber* hakt sich
> jeder Schritt dieser Anleitung von selbst ab, sobald er erledigt ist – die
> Seite fragt WordPress, ob Logo, Menüs, Widget und so weiter stehen. Solange
> etwas fehlt, weist ein Hinweis oben im Backend darauf hin und die Zahl neben
> *Lindenzauber* zeigt, wie viel noch offen ist. Diese Datei hier und die Seite
> im Backend sagen dasselbe; im Zweifel gilt die Seite, denn die sieht nach.

## 2. Seiten importieren

*Design → Lindenzauber → Seiten importieren*

1. `dist/lindenzauber-inhalte.zip` auswählen und auf **Paket lesen** klicken
2. Die Vorschau durchsehen – hier steht Zeile für Zeile, was passieren würde:
   welche Seiten neu angelegt und welche ersetzt werden
3. Darunter stehen die Seiten, die **nicht** zum Paket gehören. Angehakt wird,
   was verschwinden soll
4. **Jetzt importieren**

Damit sind acht Seiten angelegt, ihre Kurzbeschreibungen eingetragen, die
Startseite festgelegt und das Förderer-Band im Fußbereich gefüllt.

| | |
|---|---|
| **Startseite** (wird als Startseite gesetzt) | Programm |
| Die Erzählenden | Über Lindenzauber |
| Förderer | Kontakt |
| Impressum | Fotogalerie *(bleibt Entwurf)* |

Was der Import **nicht** anfasst:

* **Datenschutz** bleibt unverändert. Der bestehende Text ist juristisch erzeugt
  und wird nur neu gestaltet – da ist nichts zu tun. Die Seite steht auf einer
  Schutzliste und lässt sich gar nicht erst zum Stilllegen anhaken.
* **Fremde Seiten** bleiben, solange sie nicht angehakt werden. Angehakte werden
  auch nicht gelöscht: sie kommen auf *Entwurf*, ihr Adressname bekommt ein
  `alt-` davor und wird gemerkt. Rückgängig machen heißt also: Seite öffnen,
  Adressname und Status zurückstellen.
* **Adressen und Menüeinträge** vorhandener Seiten. Wird eine Seite ersetzt,
  behält sie ihre Kennung; der alte Stand liegt danach unter *Revisionen*.
* **Bilder.** Die Inhalte verweisen auf die Dateien, die schon in der Mediathek
  liegen.

> **Achtung, das ist wichtig:** Auf der Seite `/kontakt/` steht derzeit das
> **Impressum**, und auf `/impressum/` stehen Brigittas Kontaktdaten. Die beiden
> sind vertauscht. Der Import stellt das richtig.

Der Import lässt sich beliebig oft wiederholen – beim zweiten Mal steht bei
jeder Seite „wird ersetzt“ statt „neu“, sonst ändert sich nichts.

<details>
<summary><strong>Falls der Import nicht geht</strong> – von Hand einfügen</summary>

Der Import braucht ein Administratorkonto (genauer: das Recht, rohes HTML zu
speichern). Ohne das sagt die Seite Bescheid, statt kaputte Blöcke anzulegen.
Dann geht es so – für jede Seite einmal:

1. Seite öffnen: *Seiten → die Seite → Bearbeiten*
2. Oben rechts auf **⋮** (die drei Punkte) → **Code-Editor**
3. Alles markieren und löschen
4. Den kompletten Inhalt der passenden Datei aus `dist/lindenzauber-inhalte.zip`
   (entpackt; dieselben Dateien liegen im Repository unter `inhalte/`) einfügen
5. Über **⋮** zurück auf **Visueller Editor**
6. *Aktualisieren*

| Seite | Datei |
|---|---|
| Startseite | `01-startseite.html` |
| Programm | `02-programm.html` |
| Die Erzählenden | `03-die-erzaehlenden.html` |
| Über Lindenzauber | `04-ueber-lindenzauber.html` |
| Förderer | `05-foerderer.html` |
| Kontakt | `06-kontakt.html` |
| Impressum | `07-impressum.html` |
| Fotogalerie *(neu anlegen, als Entwurf)* | `08-fotogalerie.html` |

Die Kurzbeschreibungen stehen dann noch aus. Sie erscheinen bei Google unter dem
Seitentitel; ohne sie nimmt die Website die ersten Sätze der Seite, die enden
mitten im Satz. Je Seite: *Bearbeiten* → rechte Leiste → Reiter **Seite** → Feld
**Auszug**. Die Texte stehen in `inhalte/seiten.json` unter `auszug`.

Auch das Förderer-Band muss dann von Hand gesetzt werden – siehe Schritt 9.

**Ganz ohne Copy-und-Paste:** Bei einer leeren Seite bietet WordPress unten
„Muster wählen“ an. Dort liegen dieselben Seiten unter *Lindenzauber* bereit
(„Seite: Startseite“, „Seite: Programm“ …). Ein Klick genügt.

</details>

## 3. Logo setzen

*Design → Customizer → Website-Informationen → Logo* → das Lindenblatt-Logo aus
der Mediathek wählen (die bestehende Datei `cropped-cropped-Logo-freigestellt.png`).

Es erscheint dann links oben im Kopfbereich neben dem Schriftzug.

## 4. Symbol für den Browser-Tab

*Design → Customizer → Website-Informationen → Website-Icon*

Ein quadratisches Bild ab 512 × 512 Pixel – am besten das Lindenblatt-Signet.
Es erscheint im Browser-Tab und als Symbol, wenn jemand die Seite auf den
Startbildschirm legt.

> Solange dort nichts hinterlegt ist, zeigt das Theme ein eigenes Blatt in
> Gold auf Nachtblau. Es sieht nicht kaputt aus – aber Brigittas eigenes
> Signet ist schöner.

## 5. Eckdaten eintragen

*Design → Customizer → Lindenzauber → Eckdaten des Festes*

Alles ist mit den aktuellen Angaben vorbelegt – bitte einmal durchsehen. Wichtig
sind besonders die Felder **Beginn** und **Ende** im Format `2026-09-26 19:00`:
Daraus entstehen die Angaben, die Google für die Veranstaltung anzeigt.

Diese Angaben versorgen gleichzeitig die Fußzeile. Nächstes Jahr müssen nur diese
Felder geändert werden.

## 6. Vorschaubild fürs Teilen

*Design → Customizer → Lindenzauber → Vorschaubild beim Teilen* → am besten das
Plakat. Dieses Bild erscheint, wenn jemand einen Link in WhatsApp, Signal oder
Facebook einfügt.

## 7. Plakat einsetzen

Auf der Startseite steht im Abschnitt „Das Plakat zum Lindenzauber“ zunächst ein
gestalteter goldener Rahmen als Platzhalter.

1. Plakat in die Mediathek laden (*Medien → Datei hinzufügen*)
2. Startseite bearbeiten, den Platzhalter anklicken
3. In der Werkzeugleiste über dem Bild auf **Ersetzen → Mediathek öffnen**
4. Das Plakat auswählen, *Aktualisieren*

## 8. Menüs zuweisen

*Design → Menüs*

**Hauptmenü (oben)** – vorhandenes Menü übernehmen oder neu anlegen:
Start · Programm · Die Erzählenden · Über Lindenzauber · Förderer

**Fußzeile: Kontakt, Impressum, Datenschutz** – zweites Menü:
Kontakt · Impressum · Datenschutz

Beide Menüs unter *Positionen des Menüs* dem passenden Platz zuweisen.

> Wenn kein Menü zugewiesen ist, zeigt das Theme automatisch eine sinnvolle
> Linkliste. Es sieht also nie kaputt aus.

## 9. Förderer im Fußbereich

Das hat der Import in Schritt 2 schon erledigt: unter *Design → Widgets →
Förderer (Fußbereich)* stehen die vier Logos. Zum Austauschen genügt ein Klick
auf das jeweilige Logo → *Ersetzen*.

Die Logos erscheinen als erste Zeile im Fußbereich – auf allen Seiten **außer
der Startseite**. Dort stehen die Förderer weiter oben groß und präsent;
zweimal untereinander wäre doppelt. Ist der Bereich leer, wird das weiße Band
einfach nicht angezeigt – es entsteht kein halb fertiger Streifen.

> Der Import füllt den Bereich nur, wenn dort noch nichts steht. Sind schon
> Logos drin, bleiben sie unangetastet. Sie lassen sich jederzeit nachträglich
> einfügen: auf **+** klicken, oben nach *Lindenzauber* suchen und das Muster
> **„Förderer-Band (für die Fußzeile)“** wählen.

## 10. Fotogalerie vorbereiten

Die Seite „Fotogalerie“ bleibt auf **Entwurf**, bis es Fotos gibt. Sie enthält
bereits Einleitung, Galerie-Raster und Abschluss mit drei Platzhalterbildern.

Wenn die Fotos da sind: Galerie anklicken → Bilder ersetzen bzw. ergänzen → Seite
veröffentlichen → im Menü verlinken.

## 11. Zum Schluss durchsehen

- [ ] Startseite: Logo sichtbar, Plakat eingesetzt
- [ ] Programm: Karte wird angezeigt, die drei Sprungmarken funktionieren
- [ ] Die Erzählenden: Bild wechselt die Seite, „Mehr anzeigen“ erscheint bei langen Texten
- [ ] Kontakt zeigt Kontaktdaten (nicht das Impressum)
- [ ] Impressum zeigt die Pflichtangaben
- [ ] Fußzeile: Kontakt, Impressum, Datenschutz vorhanden
- [ ] Alte Seiten, die stillgelegt wurden, sind nicht mehr aufrufbar
- [ ] Auf dem Handy prüfen: nichts lässt sich seitlich verschieben, das Menü öffnet sich
- [ ] Einen Link in WhatsApp einfügen: Vorschaubild und Text stimmen
- [ ] Symbol im Browser-Tab ist da
- [ ] *Design → Lindenzauber*: keine offenen Schritte mehr
- [ ] `lindenzauber.de/llms.txt` im Browser aufrufen – es muss eine Textseite
      mit Terminen, Ort und Kontakt erscheinen. Kommt stattdessen „Seite nicht
      gefunden", auf *Design → Lindenzauber* den Knopf **Adressregeln erneuern**
      drücken.

---

## Wenn etwas nicht stimmt

**Der Import sagt „In dem Paket fehlt die Datei seiten.json“.** Dann war es das
Theme-Paket. Gemeint ist `lindenzauber-inhalte.zip`.

**Der Import sagt „Dieses Benutzerkonto darf kein rohes HTML speichern“.**
WordPress würde beim Speichern die Blockangaben herausfiltern und alle Seiten
unbrauchbar machen – deshalb bricht der Import lieber vorher ab. Zwei Ursachen
kommen infrage: In einem Mehrfach-Netzwerk (Multisite) hat nur die
Netzwerk-Verwaltung dieses Recht; sonst entzieht es meist ein Sicherheitsmodul.
Lässt sich das nicht ändern, gehen die Seiten von Hand – siehe den aufklappbaren
Abschnitt in Schritt 2.

**Eine Seite ist beim Import versehentlich stillgelegt worden.** Nichts wurde
gelöscht. *Seiten → Entwürfe* → die Seite öffnen, unter *Permalink* das `alt-`
aus der Adresse entfernen und wieder veröffentlichen.

**Ein Abschnitt sieht falsch aus.** Meist ist ein Block-Stil verrutscht. Block
anklicken, rechts unter *Stile* den passenden wieder auswählen.

**Eine Seite ist zu schmal oder zu breit.** Die äußerste Gruppe braucht die
Ausrichtung *Volle Breite*. Gruppe anklicken → Ausrichtungs-Symbol in der
Werkzeugleiste → *Volle Breite*.

**Der Editor zeigt „Dieser Block enthält unerwarteten oder ungültigen Inhalt“.**
Dann ist beim Einfügen etwas abgeschnitten worden. Am einfachsten: den Import
noch einmal laufen lassen.

**Die Schwungschrift erscheint nicht.** Einmal den Browser-Cache leeren. Falls ein
Caching-Plugin läuft, dort ebenfalls den Cache löschen.
