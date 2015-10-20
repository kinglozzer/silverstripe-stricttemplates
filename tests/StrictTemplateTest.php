<?php

use Kinglozzer\SilverStripeStrictTemplateParser\View\Parser;

class StrictTemplateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Parser
	 */
	protected $parser;

	public function setUp() {
		parent::setUp();

		$this->parser = new Parser();
	}

	/**
	 * Test injections. If strict braces mode is enabled, dollar-marked lookups must be wrapped in
	 * braces or they will simply be treated as text
	 */
	public function testInjections() {
		// Braced injection
		$template = '{$Foo}';
		$expected = <<<'PHP'
$val .= $scope->locally()->XML_val('Foo', null, true);
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Mixed argument types - strings/booleans/integers/constants etc
		$template = '{$Foo("string", true, 123, SOME_CONST, SomeClass::CONST)}';
		$expected = <<<'PHP'
$val .= $scope->locally()->XML_val('Foo', array('string', true, 123, SOME_CONST, SomeClass::CONST), true);
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Concatenation with strings and constants
		$template = '{$Foo("string" . "morestring" . SOME_CONST)}';
		$expected = <<<'PHP'
$val .= $scope->locally()->XML_val('Foo', array('string'.'morestring'.SOME_CONST), true);
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Concatenation with string + lookup
		$template = '{$Foo("string" . $Bar)}';
		$expected = <<<'PHP'
$val .= $scope->locally()->XML_val('Foo', array('string'.$scope->locally()->XML_val('Bar', null, true)), true);
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Injection with another lookup as an argument
		$template = '{$Foo($Lookup)}';
		$expected = <<<'PHP'
$val .= $scope->locally()->XML_val('Foo', array($scope->locally()->XML_val('Lookup', null, true)), true);
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// If strict braces mode is disabled, injections not wrapped in braces should be treated the
		// same as if they were braced
		Config::inst()->update(get_class($this->parser), 'strict_braces_mode', false);
		$template = '$Foo';
		$expected = <<<'PHP'
$val .= $scope->locally()->XML_val('Foo', null, true);
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// If strict braces mode is enabled, injections not wrapped in braces should be treated as text
		Config::inst()->update(get_class($this->parser), 'strict_braces_mode', true);
		$template = '$Foo';
		$expected = <<<'PHP'
$val .= '$Foo';
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));
	}

	/**
	 * Test comparisons. Lookup arguments must be dollar prefixed as unquoted strings will be
	 * interpreted as raw PHP values (keywords, constants etc)
	 */
	public function testComparisons() {
		// String arg
		$template = '<% if "string" = "otherstring" %><% end_if %>';
		$expected = <<<'PHP'
if ('string'=='otherstring') {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// String arg
		$template = '<% if "string" = "otherstring" %><% end_if %>';
		$expected = <<<'PHP'
if ('string'=='otherstring') {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Concatenated string arg
		$template = '<% if \'other\' . "string" = "otherstring" %><% end_if %>';
		$expected = <<<'PHP'
if ('other'.'string'=='otherstring') {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Unquoted string arg (PHP literal)
		$template = '<% if SOME_CONST > 123 %><% end_if %>';
		$expected = <<<'PHP'
if (SOME_CONST>123) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Dollar-prefixed lookup
		$template = '<% if $Foo >= SomeClass::CONST %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->XML_val('Foo', null, true)>=SomeClass::CONST) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Dollar-prefixed lookup with mixed argument types
		$template = '<% if $Foo("string") < $Bar %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->XML_val('Foo', array('string'), true)<$scope->locally()->XML_val('Bar', null, true)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Dollar-prefixed lookup with dollar-prefixed lookup argument
		$template = '<% if $Foo($Lookup) > "string" %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->XML_val('Foo', array($scope->locally()->XML_val('Lookup', null, true)), true)>'string') {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Negated presence check
		$template = '<% if $Foo != $Bar %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->XML_val('Foo', null, true)!=$scope->locally()->XML_val('Bar', null, true)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));
	}

	/**
	 * Test presence checks. Lookup arguments must be dollar prefixed as unquoted strings will be
	 * interpreted as raw PHP values (keywords, constants etc)
	 */
	public function testPresenceChecks() {
		// String arg
		$template = '<% if "string" %><% end_if %>';
		$expected = <<<'PHP'
if (((bool)'string')) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Unquoted string arg (PHP literal)
		$template = '<% if SOME_CONST %><% end_if %>';
		$expected = <<<'PHP'
if (((bool)SOME_CONST)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Dollar-prefixed lookup
		$template = '<% if $Foo %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->hasValue('Foo', null, true)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Dollar-prefixed lookup with mixed argument types
		$template = '<% if $Foo(\'string\', "string", SOME_CONST, SomeClass::CONST) %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->hasValue('Foo', array('string', 'string', SOME_CONST, SomeClass::CONST), true)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Dollar-prefixed lookup with dollar-prefixed lookup argument
		$template = '<% if $Foo($Lookup) %><% end_if %>';
		$expected = <<<'PHP'
if ($scope->locally()->hasValue('Foo', array($scope->locally()->XML_val('Lookup', null, true)), true)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Negated presence check
		$template = '<% if not $Foo %><% end_if %>';
		$expected = <<<'PHP'
if (!$scope->locally()->hasValue('Foo', null, true)) {

}
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));
	}

	/**
	 * Test calls to <% require %> methods
	 */
	public function testRequireCalls() {
		// String arg
		$template = '<% require javascript("path/to/some.js") %>';
		$expected = <<<'PHP'
Requirements::javascript('path/to/some.js');
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Lookup arg
		$template = '<% require javascript($Foo) %>';
		$expected = <<<'PHP'
Requirements::javascript($scope->locally()->XML_val('Foo', null, true));
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));

		// Concatenation
		$template = '<% require javascript(SOME_CONST . "js/file.js") %>';
		$expected = <<<'PHP'
Requirements::javascript(SOME_CONST . "js/file.js");
PHP;
		$this->assertContains($expected, $this->parser->compileString($template));
	}

}
