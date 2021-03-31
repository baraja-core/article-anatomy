<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy\Tool;


use Baraja\ArticleAnatomy\Tracy\ValidatorBlueScreen;
use Baraja\ArticleAnatomy\Tracy\ValidatorException;
use Baraja\DiffGenerator\SimpleDiff;
use Tracy\Debugger;

final class Validator
{
	public const SCHEME =
		'My article title' . "\n"
		. '================' . "\n\n"
		. '> key: value' . "\n"
		. '> supportedFormat: Meta data are json or NEON structure with all features.' . "\n"
		. '> isValid: true' . "\n\n"
		. 'Here is a full article content in markdown format.';


	public function __construct()
	{
		static $blueScreenRegistered = false;
		if ($blueScreenRegistered === false && \class_exists(Debugger::class) === true) {
			Debugger::getBlueScreen()->addPanel([ValidatorBlueScreen::class, 'render']);
			$blueScreenRegistered = true;
		}
	}


	public function analyze(string $haystack, bool $strict): void
	{
		try {
			$structure = (new Parser)->parse($haystack);
		} catch (\InvalidArgumentException $e) {
			throw new ValidatorException(
				'Haystack is not valid article. Did you mean this required scheme?'
				. "\n\n" . self::SCHEME
				. "\n\n" . 'Original parse message: ' . $e->getMessage(),
				previous: $e,
			);
		}

		$prettyArticle = (new Builder)->build($structure);
		$prettyArticle = str_replace(["\r\n", "\r"], "\n", trim($prettyArticle)) . "\n";
		$haystack = str_replace(["\r\n", "\r"], "\n", $strict ? $haystack : trim($haystack));
		if ($prettyArticle !== $haystack) { // files are not identical
			$diff = (new SimpleDiff)->compare($haystack, $prettyArticle);
			$changedLines = $diff->getChangedLines();
			if ($changedLines !== []) {
				throw new ValidatorException(
					'The validator requires fix a coding standard.'
					. "\n" . 'Comments found on lines: ' . implode(', ', $changedLines) . '.',
					$diff,
				);
			}
		}
	}
}
