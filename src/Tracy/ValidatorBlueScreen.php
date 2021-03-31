<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy\Tracy;


use Baraja\DiffGenerator\SimpleDiff;

final class ValidatorBlueScreen
{
	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . static::class . ' is static and cannot be instantiated.');
	}


	/**
	 * @return string[]|null
	 */
	public static function render(?\Throwable $e): ?array
	{
		if ($e === null || !$e instanceof ValidatorException) {
			return null;
		}
		if (($diff = $e->getDiff()) !== null) {
			return [
				'tab' => 'Invalid article structure',
				'panel' => '<p>Please correct the formatting of the article according to the '
					. 'proposed structure from the codestyle test.</p>'
					. '<p>The validator requires the following lines to be changed: '
					. '<b>' . implode(', ', $diff->getChangedLines()) . '</b>.</p>'
					. (new SimpleDiff)->renderDiff($diff),
			];
		}

		return null;
	}
}
