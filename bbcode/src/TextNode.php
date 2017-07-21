<?php

namespace bbcode;

/**
 *	@author 3kZO
 */
class TextNode extends Node
{
	
	/**
	 *	@var string
	 */
	private $value;
	
	public function __construct($value) {
		$this->value = $value;
	}
	
	public function isTextNode() {
		return true;
	}
	
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
	public function getValue() {
		return $this->value;
	}
	
}