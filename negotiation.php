<?php declare(strict_types=1);

function parse_accept_language($accept_language)
{
	$result = array();
	$parts = array_map('trim', explode(',', $accept_language));
	foreach (array_reverse($parts) as $part) {
		if (str_contains($part, ';q=')) {
			[$lang, $q] = explode(';q=', $part, 2);
			$result[$lang] = floatval($q);
		} else {
			$result[$part] = 1;
		}
	}
	return $result;
}

function match_language($accept, $candidates, $fallback)
{
	$best = $fallback;
	$best_q = 0;
	foreach ($candidates as $lang) {
		foreach ($accept as $l => $q) {
			if (str_starts_with($lang, $l) && $q > $best_q) {
				$best = $lang;
				$best_q = $q;
			}
		}
	}
	return $best;
}

function get_preferred_language()
{
	$langs = ['de', 'en', 'fr', 'es', 'ar', 'fa', 'ru'];
	$accept = parse_accept_language($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
	return match_language($accept, $langs, $langs[0]);
}

$lang = get_preferred_language();
header("Location: /$lang/${_GET['path']}");
