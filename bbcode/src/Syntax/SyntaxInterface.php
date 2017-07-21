<?php

namespace bbcode\Syntax;

/**
 *	@author 3kZO
 */
interface SyntaxInterface
{
	
	public function getOpeningTag();
	public function getClosingTag();
	public function getClosingTagMarker();
	public function getAttrValueSeparator();
	public function getAttrValueDelimiter();
	
}