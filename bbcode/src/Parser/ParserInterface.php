<?php

namespace bbcode\Parser;

use \bbcode\Tokenizer\TokenizerInterface;

/**
 *	@author 3kZO
 */
interface ParserInterface
{
	
	public function parse($string, TokenizerInterface $tokenizer);
	
}