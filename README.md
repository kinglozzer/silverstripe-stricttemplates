# SilverStripe Strict Templates

A modified implementation of SilverStripe’s template parser with a stricter syntax, enabling new features.

Not for use in production!

### What’s different?

String arguments must be quoted:
```
Template code	|	Standard output		| StrictTemplates output
-----------------------------------------------------------------------------------
$SayHi(John)	|	Hi John				| Notice: Use of undefined constant John
$SayHi('John')	|	Hi John				| Hi John
```

PHP-like arguments will actually be treated as PHP:
```
function Dump($arg) {
	return var_dump($arg);
}

Template code	|	Standard output		| StrictTemplates output
-----------------------------------------------------------------------------------
$Dump(true)		|	string(4) "true"	| bool(true)
$Dump(123)		|	string(3) "123"		| int(123)
```

You can access constants as part of template lookups. Assuming `CONST` is defined as `'value'` and `SomeClass::CONST` is defined as `'value'`
```
function Dump($arg) {
	return var_dump($arg);
}

Template code	  		| Standard output				| StrictTemplates output
-----------------------------------------------------------------------------------
$Dump(CONST)	   		| string(5) "CONST"				| string(5) "value"
$Dump(SomeClass::CONST) | string(16) "SomeClass::CONST"	| string(5) "value"
```

### Strict braces mode

It’s possible to force users to enclose dollar-marked lookups like `$Foo` in braces: `{$Foo}` (this doesn’t apply to dollar-marked lookups inside blocks: `<% with $Foo %>` is still valid).

```
strict_braces_mode disabled:
Hi $FirstName		-> outputs ->		Hi John
Hi {$FirstName}		-> outputs ->		Hi John

strict_braces_mode enabled:
Hi $FirstName		-> outputs ->		Hi $FirstName
Hi {$FirstName}		-> outputs ->		Hi John

```

### String concatenation

It’s now also possible to concatenate strings in lookups. The following examples will now work:

- `{$Foo('string' . 'anotherstring')}`
- `{$Foo('string' . SOME_CONSTANT)}`
- `{$Foo($Bar . 'anotherstring')}`
- `<% require javascript(MY_MODULE_DIR . 'js/file.js') %>`

## Why?

I’m sure purists will say that this blurs the line between PHP and template code, but this is only a proof of concept. I wanted an excuse to learn a bit more about the template parser, this was it!

## Todo

- Think of a more snazzy name
- More test coverage
- Clearly define syntax & limitations
- Port old i18n syntax across? I don't even know if this works in the CMS
