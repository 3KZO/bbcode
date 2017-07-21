<?php

namespace bbcode\Parser;

use \bbcode\Parser\ParserInterface;
use \bbcode\Syntax\SyntaxInterface;
use \bbcode\HandlerCollection;

use \bbcode\Node;
use \bbcode\TextNode;
use \bbcode\ElementNode;
use \bbcode\Tokenizer\TokenizerInterface;

use \Framework\lang\String;

/**
 *	@author 3kZO
 */
class Parser implements ParserInterface
{
	
	/**
	 *	@var SytnaxInterface
	 */
	private $syntax;
	
	/**
	 *	@var HandlerCollection
	 */
	private $handlers;
	
	/**
	 *	@var string[]
	 */
	private $buf;
	
	public function __construct(SyntaxInterface $syntax, HandlerCollection $handlers) {
		$this->syntax = $syntax;
		$this->handlers = $handlers;
	}
	
	//public function setSyntax(SyntaxInterface $syntax) {
	//	$this->syntax = $syntax;
	//	return $this;
	//}
	
	public function getSyntax() {
		return $this->syntax;
	}
	
	public function createTextNode(Node $parent, $value) {
		$parent->addChildren(new TextNode($value));
		return $this;
	}
	
	public function parse($string, TokenizerInterface $tokenizer) {
		$parent		=	new ElementNode();
		$parent->setHandler(
			$this->handlers->get(null)
		);
		//$tokenizer	=	new Tokenizer($this->syntax);
		$tokenizer->tokenize($string);
		while (!$tokenizer->endOf()) {
			$token = $tokenizer->next();
			if ($this->syntax->getOpeningTag() == $token) {
				if ($tokenizer->endOf()) {
					$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
					break;
				}
				$token = $tokenizer->next();
				while ($this->syntax->getOpeningTag() == $token) {
					$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
					if ($tokenizer->endOf()) {
						$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
						break;
					}
					$token = $tokenizer->next();
				}
				if ($tokenizer->endOf()) {
					$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
					$this->createTextNode(
						$parent,
						$token
					);
					break;
				}
				if ($this->syntax->getClosingTag() == $token) {
					$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
					$this->createTextNode(
						$parent,
						$this->syntax->getClosingTag()
					);
					continue;
				}
				if ($tokenizer->endOf() || $this->syntax->getClosingTag() != $tokenizer->next()) {
					$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
					$this->createTextNode(
						$parent,
						$token
					);
					continue;
				}
				
				$closed = false;
				
				if ('/' === substr($token, -1)) {
					$token	=	substr($token, 0, strlen($token)-1);
					$closed = true;
				}
				
				$attrs = $this->getAttributes($token);
				
				$tag = array_keys($attrs)[0];	//	nulled attribute
				
				if ('' == $tag || $this->syntax->getClosingTagMarker() == $tag) {
					$this->createTextNode(
						$parent,
						$this->syntax->getOpeningTag()
					);
					$this->createTextNode(
						$parent,
						$token
					);
					$this->createTextNode(
						$parent,
						$this->syntax->getClosingTag()
					);
					continue;
				}
				
				if ($this->syntax->getClosingTagMarker() == substr($tag, 0, 1)) {
					
					$tag = substr($tag, 1);
					$el = $parent->find($tag);
					
					if (null === $el) {
						$this->createTextNode(
							$parent,
							$this->syntax->getOpeningTag()
						);
						$this->createTextNode(
							$parent,
							$token
						);
						$this->createTextNode(
							$parent,
							$this->syntax->getClosingTag()
						);
					} else {
						$parent = $el->getParent();
					}
					
					continue;
					
				}
				
				$el = new ElementNode();
				$el->setTag($tag);
				
				foreach ($attrs as $key => $value) {
					$el->setAttr($key, $value);
				}
				
				$el->setHandler(
					$this->handlers->get($tag)
				);
				
				$parent->addChildren($el);
				
				if ($closed) {
					//	skip
					continue;
				}
				
				$prev_parent=	$parent;
				$parent		=	$el;
				
				/*
				if (null !== $prev_parent->find($el->getTag()) || $parent->getTag() == $prev_parent->getTag()) {
					$this->createTextNode(
						$prev_parent,
						$this->syntax->getOpeningTag()
					);
					$this->createTextNode(
						$prev_parent,
						$parent->getTag()
					);
					$this->createTextNode(
						$prev_parent,
						$this->syntax->getClosingTag()
					);
					$prev_parent->removeChildren($el);
					$parent =$prev_parent;
					continue;
				}
				 */
				
				if (false === $this->closingTagCheck($el, clone $tokenizer)) {
					
					$this->createTextNode(
						$prev_parent,
						$this->syntax->getOpeningTag()
					);
					$this->createTextNode(
						$prev_parent,
						$el->getTag()
					);
					$this->createTextNode(
						$prev_parent,
						$this->syntax->getClosingTag()
					);
						
					$prev_parent->removeChildren($el);
					$parent	=	$prev_parent;
					
					continue;
					
				}
				
			} else {
				$parent->addChildren(new TextNode($token));
			}
		}
		
		return $parent;
		
	}
	
	public function closingTagCheck(Node $el, TokenizerInterface $tokenizer) {
		
		$prevprev	=	$tokenizer->next();
		$prev		=	$tokenizer->next();
		$curr		=	$tokenizer->next();
		
		while ($this->syntax->getOpeningTag() != $prevprev || $this->syntax->getClosingTagMarker() . $el->getTag() != $prev || $this->syntax->getClosingTag() != $curr) {
			
			$prevprev	=	$prev;
			$prev		=	$curr;
			
			if ($tokenizer->endOf()) {
				return false;
			}
			
			$curr	=	$tokenizer->next();
			
		}
		
		return true;
		
	}
	
	public function write($char) {
		$this->buf[] = $char;
		return $this;
	}
	
	public function flush() {
		
		$output = implode('', $this->buf);
		$this->buf = [];
		
		return $output;
	}
	
	public function getAttributes($token) {
		
		$token = new String($token);
		$len = $token->length();
		
		//$this->flush();
		
		$keys = [];
		$values = [];
		
		$offset = 0;
				$mode = 1;

		while ($offset <= $len) {
			$char = $token[ $offset ];
			
			switch ($mode) {
				
				//	find tag
				case 1 :
				
					switch ($char) {
						
						default :
						$this->write($char);
						break;
						
						case $this->syntax->getAttrValueSeparator() :
						$keys[] = $this->flush();
						$mode = 2;
						
						break;
						
						case ' ' :
						$keys[] = $this->flush();
						$mode = 0;
						break;
						
						case null :
						$keys[] = $this->flush();
						break;
						
					}
					
				break;
				
				case 0 :
					switch ($char) {
						
						default	:
						case ' ':
						$this->write($char);
						$mode = 4;
						break;
						
					}
				break;
				
				case 2	:
					
					switch ($char) {
						
						default	:
						$this->write($char);
						break;
						
						case $this->syntax->getAttrValueDelimiter() :
						$mode = 3;
						break;
						
						case null	:
						case ' '	:
						
						$values[] = $this->flush();
						$mode = 4;
						break;
						
						
						
					}
					
				break;
				
				case 3	:
					
					switch ($char) {
						
						case null	:
						case $this->syntax->getAttrValueDelimiter() :
						$values[] = $this->flush();
						$mode = 4;
						break;
						
						default	:
						case ' ' :
						$this->write($char);
						break;
						
					}
					
				break;
				
				//	
				case 4	:
				
					switch ($char) {
						
						default	:
						$this->write($char);
						break;
						
						case ' '	:
						//	skip
						break;
						
						case $this->syntax->getAttrValueSeparator() :
						$keys[] = $this->flush();
						$mode = 2;
						break;
						
					}
					
				break;
			}
			$offset++;
		}
		
		$attrs = [];
		if (count($keys) == count($values) + 1) {
			//	prepend
			array_unshift($values, null);
		}
		
		$attrs = array_combine($keys, $values);
		
		return $attrs;
		
	}
	
}