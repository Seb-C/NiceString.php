<?php

class NiceString implements Serializable, Iterator, ArrayAccess {
	private $s;
	
	public function __construct($string) {
		$this->s = (string) $string;
	}
	
	public function __toString() {
		return $this->s;
	}
	
	public function __get($n) {
		return new self($this->s.$n);
	}
	
	public function __invoke($a, $b) {
		return new self(substr($this->s, $a, $a + $b));
	}
	
	// Implementing Serializable
	public function serialize() {
		return serialize($this->s);
	}
	public function unserialize($data) {
		$this->s = unserialize($data);
	}
	
	// Implementing Iterator
	private $itPos = 0;
	function rewind() {
		$this->itPos = 0;
	}
	function current() {
		return new self($this->s[$this->itPos]);
	}
	function key() {
		return $this->itPos;
	}
	function next() {
		$this->itPos++;
	}
	function valid() {
		return $this->itPos < strlen($this->s);
	}
	
	// Implementing ArrayAccess
	public function offsetSet($k, $value) {
		throw new LogicException("NiceString is immutable.");
	}
	public function offsetExists($k) {
		return is_int($k) && $k >= 0 && $k < strlen($this->s);
	}
	public function offsetUnset($k) {
		throw new LogicException("NiceString is immutable.");
	}
	public function offsetGet($k) {
		if(!$this->offsetExists($k)) {
			throw new OutOfRangeException("Invalid index: $k");
		}
		return new self($this->s[$k]);
	}
	
	// Methods ...
	
	public function length() {
		return strlen($this->s);
	}
	
	public function concat(/* ... */) { // TODO splat operator (PHP 5.6) ?
		return new self($this->s.implode('', func_get_args()));
	}
	
	public function split($delimiter) {
		return explode($delimiter, $this->s);
	}
	
	public function trim($mask = null) {
		return new self(trim($this->s));
	}
	
	public function substring($s, $length = null) {
		if($length == null) {
			return new self(substr($this->s, $s));
		} else {
			return new self(substr($this->s, $s, $length));
		}
	}
	
	public function toLowerCase() {
		return new self(strtolower($this->s));
	}
	
	public function toUpperCase() {
		return new self(strtoupper($this->s));
	}
	
	public function reverse() {
		return new self(strrev($this->s));
	}
	
	public function repeat($n) {
		return new self(str_repeat($this->s, $n));
	}
	
	public function replace($search, $replace, $count = null) {
		if($count == null) {
			return new self(str_replace($search, $replace, $this->s));
		} else {
			return new self(str_replace($search, $replace, $this->s, $count));
		}
	}
	
	public function contains($str) {
		return strpos($this->s, $str) !== false;
	}
	
	public function indexOf($str, $startIndex = 0) {
		$result = strpos($this->s, $str, $startIndex);
		if($result === false) return null;
		return new self($result);
	}
	
	public function firstIndexOf($str) {
		return $this->indexOf($str);
	}
	
	public function lastIndexOf($str) {
		$result = strrpos($this->s, $str);
		if($result === false) return null;
		return new self($result);
	}
	
	public function startsWith($str) {
		return substr($this->s, 0, strlen($str)) === $str;
	}
	
	public function endsWith($str) {
		$length = strlen($str);
		if($length == 0) return true;
		return substr($this->s, -$length) === $str;
	}
	
}

// Example

$people = "World";
const MY_NAME = 'FooBar';
$age    = 42;

$hello = new NiceString("Hello ");

echo $hello->$people->{", my name is "}->{MY_NAME}->{" and I'm "}->$age->{" years old.\n"};
