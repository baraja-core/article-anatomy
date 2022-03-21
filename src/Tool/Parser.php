<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy\Tool;


use Baraja\ArticleAnatomy\Structure\Structure;
use Nette\Neon\Neon;

final class Parser
{
	public function parse(string $haystack): Structure
	{
		$haystack = $this->normalize($haystack);
		if (!preg_match('/^(.+)\n===+\n+((?:>\s+[^\n]+\n)+)?((?:.|\n){1,20})/', $haystack, $fastParser) === 1) {
			throw new \InvalidArgumentException(
				'Invalid article format, please use ArticleAnatomy::validate() for debug your content.',
			);
		}
		[$matched, $title, $metaString, $contentStart] = $fastParser;
		$content = $contentStart . str_replace($matched, '', $haystack);
		if (preg_match('/^>\s[a-zA-Z0-9_-]+:/', $metaString) !== 1) {
			$content = $metaString . $content;
			$metaString = '';
		}

		$meta = $this->parseMeta(trim($metaString));
		if ($meta === null) {
			$meta = [];
			$content = $metaString . $content;
		}
		$perex = null;

		return new Structure(
			original: $haystack,
			title: trim($title),
			perex: $perex,
			meta: $meta,
			content: trim($content),
		);
	}


	/**
	 * @return array<string, string>|null
	 */
	private function parseMeta(string $meta): ?array
	{
		if ($meta === '') {
			return [];
		}

		$decoded = Neon::decode(
			implode("\n", array_map(
				static fn(string $item): string => ltrim($item, '> '),
				explode("\n", $meta),
			)),
		);

		return is_array($decoded) ? $decoded : null;
	}


	/**
	 * Removes control characters, normalizes line breaks to `\n`, removes leading and trailing blank lines,
	 * trims end spaces on lines, normalizes UTF-8 to the normal form of NFC.
	 */
	private function normalize(string $s): string
	{
		$s = trim($s);
		// convert to compressed normal form (NFC)
		if (class_exists('Normalizer', false) && ($n = \Normalizer::normalize($s, \Normalizer::FORM_C)) !== false) {
			$s = (string) $n;
		}

		$s = str_replace(["\r\n", "\r"], "\n", $s);

		// remove control characters; leave \t + \n
		$s = (string) preg_replace('#[\x00-\x08\x0B-\x1F\x7F-\x9F]+#u', '', $s);

		// right trim
		$s = (string) preg_replace('#[\t ]+$#m', '', $s);

		// leading and trailing blank lines
		return trim($s, "\n");
	}
}
