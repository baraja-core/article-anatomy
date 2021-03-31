<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy\Tool;


use Baraja\ArticleAnatomy\Structure\Structure;
use Nette\Neon\Neon;

final class Builder
{
	public function build(Structure $structure): string
	{
		return $this->title($structure->getTitle())
			. ($structure->getMeta() ? "\n\n" . $this->meta($structure->getMeta()) : '')
			. "\n\n" . $structure->getContent() . "\n";
	}


	private function title(string $title): string
	{
		return $title . "\n" . str_repeat('=', mb_strlen($title, 'UTF-8'));
	}


	/**
	 * @param mixed[] $meta
	 */
	private function meta(array $meta): string
	{
		$return = [];
		foreach (explode("\n", trim(Neon::encode($meta, Neon::BLOCK))) as $line) {
			$return[] = '> ' . $line;
		}

		return implode("\n", $return);
	}
}
