<?php

declare(strict_types=1);

namespace Baraja\ArticleAnatomy\Structure;


final class Structure
{
	/**
	 * @param array<string, string> $meta
	 */
	public function __construct(
		private string $original,
		private string $title,
		private ?string $perex,
		private array $meta,
		private string $content,
	) {
	}


	public function getOriginal(): string
	{
		return $this->original;
	}


	public function getTitle(): string
	{
		return $this->title;
	}


	public function getPerex(): ?string
	{
		return $this->perex;
	}


	/**
	 * @return array<string, string>
	 */
	public function getMeta(): array
	{
		return $this->meta;
	}


	public function getContent(): string
	{
		return $this->content;
	}
}
