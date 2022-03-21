<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy\Tracy;


use Baraja\DiffGenerator\Diff;

final class ValidatorException extends \InvalidArgumentException
{
	public function __construct(
		string $message = '',
		private ?Diff $diff = null,
		?\Throwable $previous = null,
	) {
		parent::__construct($message, 500, $previous);
	}


	public function getDiff(): ?Diff
	{
		return $this->diff;
	}
}
