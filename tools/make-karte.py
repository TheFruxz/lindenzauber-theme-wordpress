#!/usr/bin/env python3
"""Zeichnet die Anfahrtskarte als SVG aus OpenStreetMap-Daten.

Warum selbst zeichnen und kein fertiges Kartenbild:

* Ein Rasterausschnitt bringt seine eigenen Farben mit. Getönt sah er
  immer noch nach Straßenkarte aus und nicht nach dieser Website.
* 450 KB PNG gegen ein paar KB SVG – und das SVG ist auf jedem Bildschirm
  scharf, auch auf dem Handy mit dreifacher Pixeldichte.
* Es wird nichts nachgeladen. Die Karte liegt im Theme, es geht keine
  Anfrage an einen Kartendienst, wenn jemand die Seite aufruft.

Die Overpass-Abfrage läuft einmal beim Bauen, nicht zur Laufzeit.

    python3 tools/make-karte.py [--cache pfad/zu/osm.json]

Ergebnis: theme/lindenzauber/assets/img/anfahrt-kinderreich.svg

Daten: © OpenStreetMap-Mitwirkende, ODbL. Die Namensnennung steht im Bild.
"""

import argparse
import json
import math
import os
import subprocess
import sys
import time

# Der Kindergarten KinderReich, Bassum. Dieselben Werte stehen im
# Customizer unter "Ort: Breitengrad/Längengrad" – wer den Ort ändert,
# muss beides umstellen und die Karte einmal neu zeichnen lassen.
# Über --breite/--laenge/--name geht das ohne Eingriff in diese Datei.
ZIEL_LAT = 52.8517566
ZIEL_LON = 8.7355695
ZIEL_NAME = "Kindergarten KinderReich"
ZIEL_ORT = "Bassum"

# Kartenausschnitt. Etwas breiter als hoch, damit er über dem Text liegen
# kann, ohne die Seite auseinanderzuziehen.
BREITE = 940
HOEHE = 560

# Wie viel Umgebung. 0.0058° sind hier rund 650 m hoch und knapp 1,1 km
# breit: die Straßen rundherum bleiben lesbar. Ein weiterer Ausschnitt
# zeigte zwar mehr, aber nichts davon half beim Finden – und brachte
# tausende Häuser mit, die das Bild nur schwer machen.
SPANNE_LAT = 0.0058

REPO = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
ZIEL_DATEI = os.path.join(REPO, "theme/lindenzauber/assets/img/anfahrt-kinderreich.svg")

SPIEGEL = [
	"https://overpass-api.de/api/interpreter",
	"https://overpass.kumi.systems/api/interpreter",
	"https://overpass.private.coffee/api/interpreter",
]

# Straßen nach Wichtigkeit. Reihenfolge = Zeichenreihenfolge: das
# Unwichtige zuerst, damit die Hauptachsen oben liegen.
STRASSEN = [
	# (highway-Werte,           Breite, Farbe,     Deckkraft)
	(("service", "track", "footway", "path", "cycleway", "pedestrian",
	  "living_street", "bridleway", "steps"), 0.9, "#5b6a8c", 0.42),
	(("residential", "unclassified"), 1.7, "#8ba0c8", 0.60),
	(("tertiary", "tertiary_link"), 2.4, "#c9a961", 0.72),
	(("secondary", "secondary_link"), 3.2, "#e0c07e", 0.82),
	(("primary", "primary_link", "trunk", "trunk_link",
	  "motorway", "motorway_link"), 4.2, "#f0c868", 0.95),
]


def abfrage(sued, west, nord, ost):
	"""Overpass-Abfrage für den Ausschnitt."""
	kasten = "%.6f,%.6f,%.6f,%.6f" % (sued, west, nord, ost)

	return (
		"[out:json][timeout:90];("
		'way["highway"](%s);'
		'way["railway"~"^(rail|light_rail|tram|narrow_gauge)$"](%s);'
		'way["building"](%s);'
		'way["waterway"~"^(river|stream|canal|ditch)$"](%s);'
		'way["natural"="water"](%s);'
		'way["landuse"](%s);'
		'way["leisure"~"^(park|pitch|garden|playground|sports_centre)$"](%s);'
		");out body;>;out skel qt;" % ((kasten,) * 7)
	)


def hole(daten_abfrage, versuche=6):
	"""Fragt Overpass ab. Die öffentlichen Server antworten unter Last gern
	mit 504 oder brechen die Verbindung ab – deshalb mehrere Anläufe über
	mehrere Spiegel, mit wachsender Pause."""
	letzter = ""

	for versuch in range(versuche):
		server = SPIEGEL[versuch % len(SPIEGEL)]
		sys.stderr.write("  Abfrage %d/%d an %s … " % (versuch + 1, versuche, server))
		sys.stderr.flush()

		try:
			fertig = subprocess.run(
				["curl", "-sS", "-m", "120", "-G", server,
				 "--data-urlencode", "data=" + daten_abfrage],
				capture_output=True, timeout=140,
			)
			text = fertig.stdout.decode("utf-8", "replace")

			if text.lstrip().startswith("{") and '"elements"' in text:
				sys.stderr.write("ok (%d KB)\n" % (len(text) // 1024))
				return json.loads(text)

			letzter = (text[:160] or fertig.stderr.decode("utf-8", "replace")[:160]).strip()
		except Exception as fehler:  # noqa: BLE001 – jeder Fehler heißt: nochmal
			letzter = str(fehler)[:160]

		sys.stderr.write("fehlgeschlagen\n")
		time.sleep(4 * (versuch + 1))

	raise SystemExit("Overpass antwortet nicht. Zuletzt: %s" % letzter)


class Projektion:
	"""Web-Mercator, auf den Ausschnitt zugeschnitten."""

	def __init__(self, mitte_lat, mitte_lon, spanne_lat, breite, hoehe):
		self.breite = breite
		self.hoehe = hoehe
		self.mitte_lon = mitte_lon
		self.mitte_lat = mitte_lat
		self.mitte_y = self._y(mitte_lat)
		# Höhe des Ausschnitts in Mercator-Einheiten. Nach Norden wächst y,
		# also gehört der Nordrand nach vorn – andersherum stünde die Karte
		# spiegelverkehrt und der abgefragte Kasten auf dem Kopf.
		self.spanne_y = self._y(mitte_lat + spanne_lat / 2) - self._y(mitte_lat - spanne_lat / 2)
		self.massstab = hoehe / self.spanne_y
		self.spanne_lon = (breite / self.massstab) / (math.pi / 180.0)

	@staticmethod
	def _y(lat):
		lat = max(min(lat, 85.0), -85.0)
		return math.log(math.tan(math.pi / 4 + math.radians(lat) / 2))

	def __call__(self, lat, lon):
		x = self.breite / 2 + math.radians(lon - self.mitte_lon) * self.massstab
		y = self.hoehe / 2 - (self._y(lat) - self.mitte_y) * self.massstab
		return x, y

	def kasten(self, rand=1.35):
		"""Etwas größer abfragen als gezeichnet wird – sonst enden Straßen
		sichtbar an der Bildkante, statt hinauszulaufen."""
		# In Mercator-Einheiten gilt dy/dlat = 1/cos(lat); zurück in Grad
		# also mal cos(lat), sonst holt man auf dieser Breite zwei Drittel
		# mehr Daten als nötig.
		d_lat = math.degrees(self.spanne_y / 2 * rand * math.cos(math.radians(self.mitte_lat)))
		d_lon = self.spanne_lon / 2 * rand

		return (self.mitte_lat - d_lat, self.mitte_lon - d_lon,
		        self.mitte_lat + d_lat, self.mitte_lon + d_lon)


def punkte(weg, knoten, proj):
	folge = []

	for kid in weg.get("nodes", []):
		k = knoten.get(kid)
		if k:
			folge.append(proj(k["lat"], k["lon"]))

	return folge


def sichtbar(folge, breite, hoehe, rand=80):
	"""Wege, die den Ausschnitt gar nicht berühren, fliegen raus – das
	spart im fertigen SVG gut die Hälfte."""
	for x, y in folge:
		if -rand <= x <= breite + rand and -rand <= y <= hoehe + rand:
			return True
	return False


def vereinfachen(folge, toleranz=0.6):
	"""Douglas-Peucker. OSM-Wege haben oft ein Vielfaches der Punkte, die
	bei 940 px Bildbreite noch einen Unterschied machen; ohne diesen
	Schritt wird das SVG ein Vielfaches so groß, ohne besser auszusehen."""
	if len(folge) < 3:
		return folge

	behalten = [False] * len(folge)
	behalten[0] = behalten[-1] = True
	stapel = [(0, len(folge) - 1)]

	while stapel:
		anfang, ende = stapel.pop()
		ax, ay = folge[anfang]
		bx, by = folge[ende]
		dx, dy = bx - ax, by - ay
		laenge = math.hypot(dx, dy)
		weiteste, abstand = -1, 0.0

		for i in range(anfang + 1, ende):
			px, py = folge[i]

			if laenge < 1e-9:
				d = math.hypot(px - ax, py - ay)
			else:
				d = abs(dy * px - dx * py + bx * ay - by * ax) / laenge

			if d > abstand:
				weiteste, abstand = i, d

		if weiteste > 0 and abstand > toleranz:
			behalten[weiteste] = True
			stapel.append((anfang, weiteste))
			stapel.append((weiteste, ende))

	return [p for p, ja in zip(folge, behalten) if ja]


def pfad(folge, geschlossen=False, toleranz=0.6):
	folge = vereinfachen(folge, toleranz)

	if len(folge) < 2:
		return ""

	teile = ["M%.1f %.1f" % folge[0]]
	teile += ["L%.1f %.1f" % p for p in folge[1:]]

	if geschlossen:
		teile.append("Z")

	return "".join(teile)


def bauen(daten, proj):
	knoten = {e["id"]: e for e in daten["elements"] if e["type"] == "node"}
	wege = [e for e in daten["elements"] if e["type"] == "way"]

	gruen, wasser, gebaeude, bahn = [], [], [], []
	strassen = [[] for _ in STRASSEN]
	beschriftung = {}

	gruen_werte = {"forest", "wood", "meadow", "grass", "village_green",
	               "recreation_ground", "allotments", "orchard", "farmland",
	               "park", "pitch", "garden", "playground", "sports_centre",
	               "cemetery"}

	for weg in wege:
		tags = weg.get("tags", {})
		folge = punkte(weg, knoten, proj)

		if len(folge) < 2 or not sichtbar(folge, proj.breite, proj.hoehe):
			continue

		zu = folge[0] == folge[-1]

		if "building" in tags:
			# Was kleiner als vier Pixel ist, sieht man ohnehin nicht –
			# es macht die Datei nur schwer.
			xs = [p[0] for p in folge]
			ys = [p[1] for p in folge]

			if max(xs) - min(xs) >= 4 and max(ys) - min(ys) >= 4:
				gebaeude.append(pfad(folge, True, 0.9))

			continue

		if tags.get("natural") == "water" or tags.get("waterway"):
			(wasser if not zu else wasser).append((pfad(folge, zu), zu))
			continue

		if tags.get("landuse") in gruen_werte or tags.get("leisure") in gruen_werte:
			gruen.append(pfad(folge, True))
			continue

		if "railway" in tags:
			bahn.append(pfad(folge))
			continue

		art = tags.get("highway")

		if not art:
			continue

		for i, (werte, _b, _f, _d) in enumerate(STRASSEN):
			if art in werte:
				strassen[i].append(pfad(folge))

				# Straßennamen merken – der längste Abschnitt gewinnt, der
				# hat die beste Stelle für die Schrift. Wohnstraßen zählen
				# mit, aber die Hauptachsen kommen zuerst dran.
				name = tags.get("name")

				if name and i >= 1:
					laenge = sum(
						math.dist(folge[j], folge[j + 1]) for j in range(len(folge) - 1)
					)
					vorher = beschriftung.get(name)

					if vorher is None or laenge > vorher[1]:
						beschriftung[name] = (i, laenge, folge)
				break

	return gruen, wasser, gebaeude, bahn, strassen, beschriftung


def schriftzug(name, folge, proj, belegt, wichtig, ziel):
	"""Setzt den Straßennamen auf das längste gerade Stück des Weges, in
	dessen Richtung gedreht. Übersprungen wird, was am Bildrand liegt, zu
	nah an der Ortsmarke steht oder eine schon gesetzte Schrift berührt –
	übereinanderliegende Namen wären schlimmer als gar keine."""
	if len(folge) < 2:
		return None

	# Das längste Teilstück trägt die Schrift am ruhigsten.
	a, b, laengste = None, None, 0.0

	for i in range(len(folge) - 1):
		strecke = math.dist(folge[i], folge[i + 1])

		if strecke > laengste:
			a, b, laengste = folge[i], folge[i + 1], strecke

	breite_text = len(name) * 6.1 + 8

	if a is None or laengste < breite_text * 0.75:
		return None

	x, y = (a[0] + b[0]) / 2, (a[1] + b[1]) / 2
	rand = 46

	if not (rand < x < proj.breite - rand and rand < y < proj.hoehe - rand):
		return None

	# Der Ortsmarke ihren Platz lassen.
	zx, zy = proj(*ziel)

	if math.hypot(x - zx, y - zy) < 96:
		return None

	winkel = math.degrees(math.atan2(b[1] - a[1], b[0] - a[0]))

	# Nie auf dem Kopf stehen.
	if winkel > 90:
		winkel -= 180
	elif winkel < -90:
		winkel += 180

	# Grobe Hüllbox der gedrehten Schrift.
	bogen = math.radians(winkel)
	hx = (abs(math.cos(bogen)) * breite_text + abs(math.sin(bogen)) * 15) / 2
	hy = (abs(math.sin(bogen)) * breite_text + abs(math.cos(bogen)) * 15) / 2
	kasten = (x - hx, y - hy, x + hx, y + hy)

	for anderer in belegt:
		if (kasten[0] < anderer[2] and kasten[2] > anderer[0]
				and kasten[1] < anderer[3] and kasten[3] > anderer[1]):
			return None

	belegt.append(kasten)

	return (
		'<text x="%.1f" y="%.1f" transform="rotate(%.1f %.1f %.1f)" '
		'class="%s">%s</text>'
		% (x, y - 4, winkel, x, y - 4, "n h" if wichtig else "n", xml(name))
	)


def xml(text):
	return (text.replace("&", "&amp;").replace("<", "&lt;")
	            .replace(">", "&gt;").replace('"', "&quot;"))


def zeichnen(daten, proj, ziel=(ZIEL_LAT, ZIEL_LON), name=ZIEL_NAME, ort=ZIEL_ORT):
	gruen, wasser, gebaeude, bahn, strassen, beschriftung = bauen(daten, proj)
	b, h = proj.breite, proj.hoehe
	zx, zy = proj(*ziel)

	teile = []
	a = teile.append

	a('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" '
	  'width="%d" height="%d" role="img" '
	  'aria-label="%s">' % (b, h, b, h, xml('Lageplan: %s in %s' % (name, ort))))

	a("<defs>")
	# Nachthimmel-Grund, oben etwas heller wie auf der Seite.
	a('<linearGradient id="grund" x1="0" y1="0" x2="0" y2="1">'
	  '<stop offset="0" stop-color="#16233e"/>'
	  '<stop offset="1" stop-color="#0b1428"/></linearGradient>')
	# Vignette: zu den Rändern hin dunkler, damit die Karte nicht als
	# Rechteck endet, sondern in die Seite hineinläuft.
	a('<radialGradient id="vignette" cx="0.5" cy="0.5" r="0.78">'
	  '<stop offset="0.32" stop-color="#070d1c" stop-opacity="0"/>'
	  '<stop offset="0.72" stop-color="#070d1c" stop-opacity="0.42"/>'
	  '<stop offset="1" stop-color="#050914" stop-opacity="0.92"/></radialGradient>')
	# Der Schein um die Ortsmarke.
	a('<radialGradient id="schein" cx="0.5" cy="0.5" r="0.5">'
	  '<stop offset="0" stop-color="#f0c868" stop-opacity="0.6"/>'
	  '<stop offset="0.42" stop-color="#f0c868" stop-opacity="0.18"/>'
	  '<stop offset="1" stop-color="#f0c868" stop-opacity="0"/></radialGradient>')
	a("</defs>")

	a('<style>'
	  'text{font-family:Lato,system-ui,sans-serif}'
	  '.n{fill:#93a4c4;font-size:10.5px;letter-spacing:.06em;text-anchor:middle;'
	  'paint-order:stroke;stroke:#0b1428;stroke-width:3.5px;stroke-linejoin:round}'
	  '.n.h{fill:#d8c088;font-size:11.5px}'
	  '.q{fill:#75839f;font-size:10px;letter-spacing:.04em}'
	  '.m{fill:#f6e7bd;font-size:13.5px;font-weight:700;letter-spacing:.05em;'
	  'text-anchor:middle;paint-order:stroke;stroke:#0b1428;stroke-width:4.5px;'
	  'stroke-linejoin:round}'
	  # Hier steht bewusst kein "fill" – eine Regel im Stylesheet schlägt
	  # das fill-Attribut, und die gefüllten Flächen (Häuser, Grün, Wasser)
	  # wären unsichtbar. Die Linien bringen ihr fill="none" selbst mit.
	  'path{stroke-linecap:round;stroke-linejoin:round}'
	  '</style>')

	a('<rect width="%d" height="%d" fill="url(#grund)"/>' % (b, h))

	if gruen:
		a('<path d="%s" fill="#16302c" fill-opacity="0.55" stroke="none"/>'
		  % "".join(gruen))

	if wasser:
		flaechen = "".join(p for p, zu in wasser if zu)
		linien = "".join(p for p, zu in wasser if not zu)

		if flaechen:
			a('<path d="%s" fill="#1b3350" fill-opacity="0.8" stroke="none"/>' % flaechen)
		if linien:
			a('<path d="%s" fill="none" stroke="#2a4a70" stroke-width="1.6" stroke-opacity="0.8"/>' % linien)

	# Häuser als ruhige Blöcke, ohne Umriss: sie sollen den Ort spürbar
	# machen, nicht mitgelesen werden.
	if gebaeude:
		a('<path d="%s" fill="#22355c" fill-opacity="0.72" stroke="none"/>'
		  % "".join(gebaeude))

	if bahn:
		a('<path d="%s" fill="none" stroke="#6b7a9c" stroke-width="1.6" stroke-opacity="0.6" '
		  'stroke-dasharray="7 6"/>' % "".join(bahn))

	# Unter den Hauptachsen liegt ein breiter, weicher Schein – dadurch
	# haben sie Tiefe und führen das Auge, statt nur Striche zu sein.
	for (_werte, breite, farbe, deckung), stuecke in zip(STRASSEN, strassen):
		if stuecke and breite >= 2.4:
			a('<path d="%s" fill="none" stroke="%s" stroke-width="%.1f" stroke-opacity="0.1"/>'
			  % ("".join(stuecke), farbe, breite * 3.4))

	for (_werte, breite, farbe, deckung), stuecke in zip(STRASSEN, strassen):
		if stuecke:
			a('<path d="%s" fill="none" stroke="%s" stroke-width="%.1f" stroke-opacity="%.2f"/>'
			  % ("".join(stuecke), farbe, breite, deckung))

	# Straßennamen: erst die wichtigen Achsen, dann die längsten
	# Wohnstraßen – und höchstens zehn, sonst wird es unruhig.
	belegt = []
	gesetzt = 0

	# Die Laufvariable heißt bewusst nicht "name": so hieß auch der
	# Parameter für die Ortsmarke, und nach der Schleife stand dort der
	# zuletzt gesetzte Straßenname. Die Marke hieß dann "Amselstraße".
	for strasse, (rang, _laenge, folge) in sorted(
		beschriftung.items(), key=lambda p: (-p[1][0], -p[1][1])
	):
		zug = schriftzug(strasse, folge, proj, belegt, rang >= 3, ziel)

		if zug:
			a(zug)
			gesetzt += 1

		if gesetzt >= 10:
			break

	a('<rect width="%d" height="%d" fill="url(#vignette)"/>' % (b, h))

	# Die Ortsmarke: Schein, Ring, Kern – wie ein Laternchen im Dunkeln.
	a('<circle cx="%.1f" cy="%.1f" r="96" fill="url(#schein)"/>' % (zx, zy))
	a('<circle cx="%.1f" cy="%.1f" r="15" fill="none" stroke="#f0c868" '
	  'stroke-width="1.2" stroke-opacity="0.55"/>' % (zx, zy))
	a('<circle cx="%.1f" cy="%.1f" r="7.5" fill="#f0c868"/>' % (zx, zy))
	a('<circle cx="%.1f" cy="%.1f" r="2.6" fill="#0b1428" fill-opacity="0.55"/>' % (zx, zy))
	a('<text x="%.1f" y="%.1f" class="m">%s</text>' % (zx, zy - 26, xml(name)))

	a('<text x="14" y="%d" class="q">© OpenStreetMap-Mitwirkende</text>' % (h - 12))
	# Kein Rahmen im Bild: den zeichnet das Theme um den Bildblock herum.
	a("</svg>")

	return "".join(teile)


def main():
	zerteiler = argparse.ArgumentParser(description=__doc__)
	zerteiler.add_argument("--cache", help="Vorhandene Overpass-Antwort verwenden")
	zerteiler.add_argument("--ziel", default=ZIEL_DATEI)
	zerteiler.add_argument("--breite", type=float, default=ZIEL_LAT, help="Breitengrad des Ortes")
	zerteiler.add_argument("--laenge", type=float, default=ZIEL_LON, help="Längengrad des Ortes")
	zerteiler.add_argument("--name", default=ZIEL_NAME, help="Beschriftung der Ortsmarke")
	zerteiler.add_argument("--ort", default=ZIEL_ORT, help="Stadt, für den Alternativtext")
	argumente = zerteiler.parse_args()

	ziel = (argumente.breite, argumente.laenge)
	proj = Projektion(ziel[0], ziel[1], SPANNE_LAT, BREITE, HOEHE)

	if argumente.cache and os.path.exists(argumente.cache):
		sys.stderr.write("Karte: benutze %s\n" % argumente.cache)
		with open(argumente.cache, encoding="utf-8") as datei:
			daten = json.load(datei)
	else:
		sued, west, nord, ost = proj.kasten()
		sys.stderr.write("Karte: hole OSM-Daten für %.4f,%.4f – %.4f,%.4f\n"
		                 % (sued, west, nord, ost))
		daten = hole(abfrage(sued, west, nord, ost))

		if argumente.cache:
			with open(argumente.cache, "w", encoding="utf-8") as datei:
				json.dump(daten, datei)

	svg = zeichnen(daten, proj, ziel, argumente.name, argumente.ort)

	with open(argumente.ziel, "w", encoding="utf-8") as datei:
		datei.write(svg)

	sys.stderr.write("Karte: %s (%d KB)\n"
	                 % (argumente.ziel, len(svg.encode("utf-8")) // 1024))


if __name__ == "__main__":
	main()
