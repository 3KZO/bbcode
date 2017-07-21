<?php

namespace bbcode\Syntax;

/**
 *	@author 3kZO
 */
class Syntax implements SyntaxInterface
{
	
	/**
	 *	@var string
	 */
	private $openingTag;
	
	/**
	 *	@var string
	 */
	private $closingTag;
	
	/**
	 *	@var string
	 */
	private $closingTagMarker;
	
	/**
	 *	@var string
	 */
	private $attrValueSeparator;
	
	/**
	 *	@var string
	 */
	private $attrValueDelimiter;
	
	public function __construct(
		$openingTag = null,
		$closingTag = null,
		$closingTagMarker = null,
		$attrValueSeparator = null,
		$attrValueDelimiter = null
	) {
		$this->openingTag = $openingTag;
		$this->closingTag = $closingTag;
		$this->closingTagMarker = $closingTagMarker;
		$this->attrValueSeparator = $attrValueSeparator;
		$this->attrValueDelimiter = $attrValueDelimiter;
	}
	
	public function getOpeningTag() {
		return null === $this->openingTag ? '[' : $this->openingTag;
	}
	
	public function getClosingTag() {
		return null === $this->closingTag ? ']' : $this->closingTag;
	}
	
	public function getClosingTagMarker() {
		return null === $this->closingTagMarker ? '/' : $this->closingTagMarker;
	}
	
	public function getAttrValueSeparator() {
		return null === $this->attrValueSeparator ? '=' : $this->attrValueSeparator;
	}
	
	public function getAttrValueDelimiter() {
		return null === $this->attrValueDelimiter ? '"' : $this->attrValueDelimiter;
	}
	
}