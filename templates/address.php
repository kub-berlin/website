<address class="homeRow-address">
	<strong property="name"><bdi>Kontakt- und Beratungsstelle für Flüchtlinge und Migrant_innen e.V.</bdi></strong>
	<p property="address" typeof="PostalAddress">
		<bdi property="streetAddress">Oranienstr. 159</bdi><br />
		<bdi><span property="postalCode">10969</span> <span property="addressLocality">Berlin-Kreuzberg</span></bdi>
	</p>
	<p>
		<?php e($lang['telephone']) ?>: <a href="tel:+49-30-614-94-00" property="telephone"><bdi>030 / 614 94 00</bdi></a><br />
		<?php e($lang['fax']) ?>: <bdi property="faxNumber">030 / 615 45 34</bdi><br />
		<?php e($lang['email']) ?>:  <a property="email" href="mailto:kontakt@kub-berlin.org">kontakt@kub-berlin.org</a><br />
		<a href="/images/kub-pubkey.asc" target="_blank"><bdi>Public PGP-Key</bdi></a>
	</p>
	<p>
		<?php e($lang['subway']) ?>: <bdi>U8 Moritzplatz</bdi><br />
		<?php e($lang['bus']) ?>: <bdi>M29 Moritzplatz</bdi>
	</p>
	<div property="openingHoursSpecification" content="Mo 09:00-13:00, Mo 14:00-17:00, Tu 09:00-13:00, Th 09:00-13:00, Th 14:00-17:00, Fr 09:00-13:00">
		<?php echo get_module('opening-hours')['body'] ?>
	</div>
</address>
