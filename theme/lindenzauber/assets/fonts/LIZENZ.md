# Schriften in diesem Theme

Beide Schriften liegen als Datei im Theme und werden vom eigenen Server geladen.
Es geht **keine** Anfrage an Google Fonts oder einen anderen Dienst – das ist
Absicht und spart die sonst nötige Einwilligung nach DSGVO.

## Lato

* Entwurf: Łukasz Dziedzic
* Lizenz: SIL Open Font License 1.1
* <https://fonts.google.com/specimen/Lato>

Enthaltene Schnitte: 300, 400, 400 kursiv, 700 – jeweils als Teilmenge
`latin` und `latin-ext`.

Wird für Fließtext, Überschriften, Menü und alle Auszeichnungen verwendet.
Das Plakat setzt durchgehend eine humanistische serifenlose Schrift ein; Lato
kommt ihr sehr nahe.

## Great Vibes

* Entwurf: Robert Leuschke (TypeSETit)
* Lizenz: SIL Open Font License 1.1
* <https://fonts.google.com/specimen/Great+Vibes>

Enthaltener Schnitt: 400 – als Teilmenge `latin` und `latin-ext`.

Wird nur für die Schwungschrift verwendet: den Titel „Lindenzauber“ sowie einzelne
kursiv gesetzte Wörter in Überschriften („Märchen“, „Familien“).

## Die Originalschrift des Plakats einsetzen

Falls die Schriftdatei vorliegt, mit der das Plakat gesetzt wurde, und sie für die
Verwendung im Web lizenziert ist:

1. Die `.woff2`-Datei in diesen Ordner legen.
2. In `theme.json` unter `settings.typography.fontFamilies` beim Eintrag `schwung`
   den `fontFace`-Block auf die neue Datei zeigen lassen und `fontFamily` anpassen.

Mehr ist nicht nötig – alle Stellen im Theme greifen auf diesen einen Eintrag zu.

## Lizenztext

Der vollständige Text der SIL Open Font License 1.1 steht unter
<https://openfontlicense.org/>.

Die Kurzfassung: Die Schriften dürfen frei verwendet, weitergegeben und verändert
werden, auch kommerziell. Sie dürfen nicht für sich allein verkauft werden, und
veränderte Fassungen dürfen den Originalnamen nicht tragen.
