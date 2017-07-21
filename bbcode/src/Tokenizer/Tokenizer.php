<?php

namespace bbcode\Tokenizer;

use \bbcode\Syntax\SyntaxInterface;

/**
 *	@author 3kZO
 */
class Tokenizer implements TokenizerInterface
{
	
	private $syntax;
	
	/**
	 *	@var string[]
	 */
	private $tokens = [];
	
	/**
	 *	@var int
	 */
	private $offset = -1;
	
	public function __construct(SyntaxInterface $syntax) {
		$this->syntax = $syntax;
	}
	
	public function tokenize($string) {
		
		$len = @mb_strlen($string->length());
		$pos = 0;
		
		while ($pos < $len) {
			
			$c = 0;
			
			if (preg_match('/^[^' . preg_quote($this->syntax->getOpeningTag() . $this->syntax->getClosingTag()) . ']+/u', @mb_substr($string, $pos), $matches)) {
				$c = @mb_strlen($matches[0]);
			}
			
			if (0 == $c) {
				$this->tokens[] = @mb_substr($string, $pos, 1);
				$pos++;
			} else {
				$this->tokens[] = @mb_substr($string, $pos,$c);
				$pos+=	$c;
			}
			
		}
		
		return $this;
		
	}
	
	public function setOffset($offset) {
		$this->offset = $offset;
		return $this;
	}
	
	public function getOffset() {
		return $this->offset;
	}
	
	public function all() {
		return $this->tokens;
	}
	
	public function exists($offset) {
		return isset($this->tokens[$offset]);
	}
	
	public function endOf() {
		return !$this->exists(1 + $this->offset);
	}
	
	public function get($offset) {
		return $this->exists($offset) ? $this->tokens[$offset] : null;
	}
	
	public function next() {
		$this->offset++;
		return $this->get($this->offset);
	}
	
	public function prev() {
		$this->offset--;
		return $this->get($this->offset);
	}
	
}