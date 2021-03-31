<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy;


use Baraja\ArticleAnatomy\Structure\Structure;
use Baraja\ArticleAnatomy\Tool\Builder;
use Baraja\ArticleAnatomy\Tool\Parser;
use Baraja\ArticleAnatomy\Tool\Validator;

final class ArticleAnatomy
{
	public function parse(string $markdown): Structure
	{
		return (new Parser)->parse($markdown);
	}


	public function build(Structure $structure): string
	{
		return (new Builder)->build($structure);
	}


	public function validate(string $markdown, bool $strict = false): void
	{
		(new Validator)->analyze($markdown, $strict);
	}


	public function fixCodingStandard(string $markdown): string
	{
		return $this->build($this->parse($markdown));
	}
}
