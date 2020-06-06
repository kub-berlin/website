<div vocab="http://schema.org/" typeof="NGO Place" resource="https://kub-berlin.org/#kub">
	<div class="homeRow-about" property="description">
		<?php echo $page['body'] ?>
		<a class="button button--block" href="https://www.betterplace.org/de/projects/20142-fluchtlinge-und-migranten-unterstutzen-und-beraten-spende-fur-die-kub">TPL_KUB_DONATE</a>
	</div>
	<div class="homeRow-map" property="location" typeof="place">
		<a href="https://www.openstreetmap.org/node/874357616" property="hasMap" target="_blank">
			<img src="/templates/kub/images/stadtplan.svg" alt="TPL_KUB_MAP" />
		</a>
		<div property="geo" typeof="GeoCoordinates">
			<meta property="latitude" content="52.5026297" />
			<meta property="longitude" content="13.4133911" />
		</div>
	</div>
	<address class="homeRow-address">
		<strong property="name"><bdi>Kontakt- und Beratungsstelle für Flüchtlinge und Migrant_innen e.V.</bdi></strong>
		<p property="address" typeof="PostalAddress">
			<bdi property="streetAddress">Oranienstr. 159</bdi><br />
			<bdi><span property="postalCode">10969</span> <span property="addressLocality">Berlin-Kreuzberg</span></bdi>
		</p>
		<p>
			TPL_KUB_TELEPHONE: <a href="tel:+49-30-614-94-00" property="telephone"><bdi>030 / 614 94 00</bdi></a><br />
			TPL_KUB_FAX: <bdi property="faxNumber">030 / 615 45 34</bdi><br />
			TPL_KUB_EMAIL:  <a property="email" href="mailto:kontakt@kub-berlin.org">kontakt@kub-berlin.org</a><br />
			<a href="/images/kub-pubkey.asc" target="_blank"><bdi>Public PGP-Key</bdi></a>
		</p>
		<p>
			TPL_KUB_SUBWAY: <bdi>U8 Moritzplatz</bdi><br />
			TPL_KUB_BUS: <bdi>M29 Moritzplatz</bdi>
		</p>
		<div property="openingHoursSpecification" content="Mo,Tu,Th,Fr 09:00-17:00">
			TODO: include opening hours
		</div>
	</address>
</div>
