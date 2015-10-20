<?php

namespace Kinglozzer\SilverStripeStrictTemplateParser\View;

use Config;
use Parser as PegParser;
use SSTemplateParseException;
use TemplateParser;

require_once THIRDPARTY_PATH . '/php-peg/Parser.php';

class Parser extends PegParser implements TemplateParser
{
    /**
     * @var bool
     * @config
     */
    private static $strict_braces_mode = false;

    /**
     * @var bool
     */
    protected $includeDebuggingComments = false;

    /**
     * @var array
     */
    protected $closedBlocks = array();

    /**
     * @var array
     */
    protected $openBlocks = array();

    /**
     * Allow the injection of new closed & open block callables
     * @param array $closedBlocks
     * @param array $openBlocks
     */
    public function __construct($closedBlocks = array(), $openBlocks = array())
    {
        $this->setClosedBlocks($closedBlocks);
        $this->setOpenBlocks($openBlocks);
    }

    /**
     * Override the function that constructs the result arrays to also prepare a 'php' item in the array
     */
    public function construct($matchrule, $name, $arguments = null)
    {
        $res = parent::construct($matchrule, $name, $arguments);

        if (!isset($res['php'])) {
            $res['php'] = '';
        }

        return $res;
    }

    /**
     * Set the closed blocks that the template parser should use
     *
     * This method will delete any existing closed blocks, please use addClosedBlock if you don't
     * want to overwrite
     * @param array $closedBlocks
     * @throws InvalidArgumentException
     */
    public function setClosedBlocks($closedBlocks)
    {
        $this->closedBlocks = array();
        foreach ((array) $closedBlocks as $name => $callable) {
            $this->addClosedBlock($name, $callable);
        }
    }

    /**
     * Set the open blocks that the template parser should use
     *
     * This method will delete any existing open blocks, please use addOpenBlock if you don't
     * want to overwrite
     * @param array $openBlocks
     * @throws InvalidArgumentException
     */
    public function setOpenBlocks($openBlocks)
    {
        $this->openBlocks = array();
        foreach ((array) $openBlocks as $name => $callable) {
            $this->addOpenBlock($name, $callable);
        }
    }

    /**
     * Add a closed block callable to allow <% name %><% end_name %> syntax
     * @param string $name The name of the token to be used in the syntax <% name %><% end_name %>
     * @param callable $callable The function that modifies the generation of template code
     * @throws InvalidArgumentException
     */
    public function addClosedBlock($name, $callable)
    {
        $this->validateExtensionBlock($name, $callable, 'Closed block');
        $this->closedBlocks[$name] = $callable;
    }

    /**
     * Add a closed block callable to allow <% name %> syntax
     * @param string $name The name of the token to be used in the syntax <% name %>
     * @param callable $callable The function that modifies the generation of template code
     * @throws InvalidArgumentException
     */
    public function addOpenBlock($name, $callable)
    {
        $this->validateExtensionBlock($name, $callable, 'Open block');
        $this->openBlocks[$name] = $callable;
    }

    /**
     * Ensures that the arguments to addOpenBlock and addClosedBlock are valid
     * @param $name
     * @param $callable
     * @param $type
     * @throws InvalidArgumentException
     */
    protected function validateExtensionBlock($name, $callable, $type)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Name argument for %s must be a string",
                    $type
                )
            );
        } elseif (!is_callable($callable)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Callable %s argument named '%s' is not callable",
                    $type,
                    $name
                )
            );
        }
    }

    /*!* Parser

    # Template is any structurally-complete portion of template (a full nested level in other words). It's the
    # primary matcher, and is used by all enclosing blocks, as well as a base for the top level.
    # Any new template elements need to be included in this list, if they are to work.
     
    Template: (Comment | Translate | If | Require | CacheBlock | UncachedBlock | Include | ClosedBlock | OpenBlock |
        MalformedBlock | Injection | Text)+
    */
    public function Template_STR(&$res, $sub)
    {
        $res['php'] .= $sub['php'] . PHP_EOL ;
    }

    /*!*
    
    Word: / [A-Za-z_] [A-Za-z0-9_]* /
    Number: / [0-9]+ /
    Value: / [A-Za-z0-9_]+ /

    # CallArguments is a list of one or more comma seperated "arguments" (lookups or strings) as passed to a Call
    # within brackets
     
    CallArguments: :Argument ( < "," < :Argument )*
    */

    /**
     * Values are bare words in templates, but strings in PHP. We rely on PHP's type conversion to back-convert
     * strings to numbers when needed.
     */
    public function CallArguments_Argument(&$res, $sub)
    {
        if (!empty($res['php'])) {
            $res['php'] .= ', ';
        }

        if ($sub['ArgumentMode'] == 'default') {
            $res['php'] .= $sub['string_php'];
        } else {
            $res['php'] .= str_replace('$$FINAL', 'XML_val', $sub['php']);
        }
    }

    /*!*
    
    # Call is a php-style function call, e.g. Method(Argument, ...). Unlike PHP, the brackets are optional if no
    # arguments are passed
     
    Call: Method:Word ( "(" < :CallArguments? > ")" )?

    # A lookup is a lookup of a value on the current scope object. It's a sequence of calls seperated by "."
    # characters. This final call in the sequence needs handling specially, as different structures need different
    # sorts of values, which require a different final method to be called to get the right return value
     
    LookupStep: :Call &"."
    LastLookupStep: :Call

    Lookup: LookupStep ("." LookupStep)* "." LastLookupStep | LastLookupStep
    */
    
    public function Lookup__construct(&$res)
    {
        $res['php'] = '$scope->locally()';
        $res['LookupSteps'] = array();
    }

    /**
     * The basic generated PHP of LookupStep and LastLookupStep is the same, except that LookupStep calls 'obj' to
     * get the next ViewableData in the sequence, and LastLookupStep calls different methods (XML_val, hasValue, obj)
     * depending on the context the lookup is used in.
     */
    public function Lookup_AddLookupStep(&$res, $sub, $method)
    {
        $res['LookupSteps'][] = $sub;
        
        $property = $sub['Call']['Method']['text'];
        
        if (isset($sub['Call']['CallArguments']) && $arguments = $sub['Call']['CallArguments']['php']) {
            $res['php'] .= "->$method('$property', array($arguments), true)";
        } else {
            $res['php'] .= "->$method('$property', null, true)";
        }
    }

    public function Lookup_LookupStep(&$res, $sub)
    {
        $this->Lookup_AddLookupStep($res, $sub, 'obj');
    }

    public function Lookup_LastLookupStep(&$res, $sub)
    {
        $this->Lookup_AddLookupStep($res, $sub, '$$FINAL');
    }

    /*!*

    # New Translatable Syntax
    # <%t Entity DefaultString is Context name1=string name2=$functionCall
    # (This is a new way to call translatable strings. The parser transforms this into a call to the _t() method)

    Translate: "<%t" < Entity < (Default:QuotedString)? < (!("is" "=") < "is" < Context:QuotedString)? <
        (InjectionVariables)? > "%>"
    InjectionVariables: (< InjectionName:Word "=" Argument)+
    Entity: / [A-Za-z_] [\w\.]* /
    */

    public function Translate__construct(&$res)
    {
        $res['php'] = '$val .= _t(';
    }

    public function Translate_Entity(&$res, $sub)
    {
        $res['php'] .= "'$sub[text]'";
    }

    public function Translate_Default(&$res, $sub)
    {
        $res['php'] .= ",$sub[text]";
    }

    public function Translate_Context(&$res, $sub)
    {
        $res['php'] .= ",$sub[text]";
    }

    public function Translate_InjectionVariables(&$res, $sub)
    {
        $res['php'] .= ",$sub[php]";
    }

    public function Translate__finalise(&$res)
    {
        $res['php'] .= ');';
    }

    public function InjectionVariables__construct(&$res)
    {
        $res['php'] = "array(";
    }

    public function InjectionVariables_InjectionName(&$res, $sub)
    {
        $res['php'] .= "'$sub[text]'=>";
    }

    public function InjectionVariables_Argument(&$res, $sub)
    {
        $res['php'] .= str_replace('$$FINAL', 'XML_val', $sub['php']) . ',';
    }

    public function InjectionVariables__finalise(&$res)
    {
        // Remove trailing comma from the array
        if (substr($res['php'], -1) === ',') {
            $res['php'] = substr($res['php'], 0, -1);
        }

        $res['php'] .= ')';
    }

    /*!*
    
    # Injections are where, outside of a block, a value needs to be inserted into the output. Lookup values
    # must be wrapped in braces, i.e. {$Foo}, if strict braces mode is enabled.
    
    SimpleInjection: '$' :Lookup
    BracedInjection: '{$' :Lookup "}"
    Injection: BracedInjection | SimpleInjection
    */
    public function Injection_SimpleInjection(&$res, $sub)
    {
        if (Config::inst()->get(get_class($this), 'strict_braces_mode')) {
            $res['php'] .= '$val .= \'' . $res['text'] . '\';' . PHP_EOL;
        } else {
            $res['php'] = '$val .= ' . str_replace('$$FINAL', 'XML_val', $sub['Lookup']['php']) . ';';
        }
    }

    public function Injection_BracedInjection(&$res, $sub)
    {
        $res['php'] = '$val .= ' . str_replace('$$FINAL', 'XML_val', $sub['Lookup']['php']) . ';';
    }

    /*!*
     
    # Inside a block's arguments you can still use the same format as an injection, minus the braces (e.g. $Foo).
    # In this case it marks the argument as being a lookup, not a string (if it was bare it might still be used as
    # a lookup, but that depends on where it's used, a la 2.4)
    
    SimpleArgumentInjection: '$' :Lookup
    DollarMarkedLookup: SimpleArgumentInjection
    */
    public function DollarMarkedLookup_STR(&$res, $sub)
    {
        $res['Lookup'] = $sub['Lookup'];
    }

    /*!*
    
    # Inside a block's arguments you can explictly mark a string by surrounding it with quotes (single or double,
    # but they must be matching). If you do, inside the quote you can escape any character, but the only character
    # that _needs_ escaping is the matching closing quote
     
    QuotedString: q:/['"]/   String:/ (\\\\ | \\. | [^$q\\])* /   '$q'
    
    # Anything that's not a quoted string or a lookup is treated as if it were php - e.g. boolean/null values,
    # constants, integers etc
     
    PHPLiteral: /[^,)%!=><|&]+/

    # Arguments can be concatenated, e.g. "$Foo . 'string' . CONSTANT". We blindly join them all together in the
    # assumption that they will form something valid, but we have no guarantees

    Concatenatable:
        :DollarMarkedLookup |
        :QuotedString |
        :PHPLiteral

    Concatenated: :Concatenatable ( < "." < :Concatenatable )+
    
    # An argument - either a marked value, or a bare value, prefering lookup matching on the bare value over
    # freestring matching as long as that would give a successful parse
     
    Argument:
        :Concatenated |
        :DollarMarkedLookup |
        :QuotedString |
        :PHPLiteral
    */
    
    public function Argument_DollarMarkedLookup(&$res, $sub)
    {
        $res['ArgumentMode'] = 'lookup';
        $res['php'] = $this->Argument_Handle_DollarMarkedLookup($sub);
    }

    public function Argument_QuotedString(&$res, $sub)
    {
        $res['ArgumentMode'] = 'string';
        $res['php'] = $this->Argument_Handle_QuotedString($sub);
    }

    public function Argument_PHPLiteral(&$res, $sub)
    {
        $res['ArgumentMode'] = 'string';
        $res['php'] = $this->Argument_Handle_PHPLiteral($sub);
    }

    public function Argument_Concatenated(&$res, $sub)
    {
        $res['ArgumentMode'] = 'string';

        $pieces = array();
        // Recurse through each concatenatable piece of data
        foreach ($sub['Concatenatable'] as $data) {
            // As concatenatable data could be any type, we have to recurse further and search for
            // how it was matched as "concatenatable". This is stored as an array with a key that
            // matches something we know how to look up (e.g. 'QuotedString' => array(...))
            foreach ($data as $key => $value) {
                $method = 'Argument_Handle_' . $key;

                if (method_exists($this, $method)) {
                    // We can't switch argument mode with this approach like we do for the above
                    // methods, but it seems to work anyway... ho hum
                    $pieces[] = $this->$method($data[$key]);
                }
            }
        }

        $res['php'] = implode('.', $pieces);
    }

    public function Argument_Handle_DollarMarkedLookup($sub)
    {
        return $sub['Lookup']['php'];
    }

    public function Argument_Handle_QuotedString($sub)
    {
        return "'" . str_replace("'", "\\'", $sub['String']['text']) . "'";
    }

    public function Argument_Handle_PHPLiteral($sub)
    {
        return trim($sub['text']);
    }

    /*!*
     
    # if and else_if blocks allow basic comparisons between arguments
     
    ComparisonOperator: "!=" | "==" | ">=" | ">" | "<=" | "<" | "="

    Comparison: Argument < ComparisonOperator > Argument
    */
    public function Comparison_Argument(&$res, $sub)
    {
        if ($sub['ArgumentMode'] == 'default') {
            if (!empty($res['php'])) {
                $res['php'] .= $sub['string_php'];
            } else {
                $res['php'] = str_replace('$$FINAL', 'XML_val', $sub['lookup_php']);
            }
        } else {
            $res['php'] .= str_replace('$$FINAL', 'XML_val', $sub['php']);
        }
    }

    public function Comparison_ComparisonOperator(&$res, $sub)
    {
        // '=' is assignment in PHP, so convert it to a comparison
        if ($sub['text'] == '=') {
            $res['php'] .= '==';
        } else {
            $res['php'] .= $sub['text'];
        }
    }

    /*!*
     
    # If a comparison operator is not used in an if or else_if block, then the statement is a 'presence check',
    # which checks if the argument given is present or not. For string mode arguments (such as constants) this
    # falls back to simple truthiness check
     
    PresenceCheck: (Not:'not' <)? Argument
    */
    public function PresenceCheck_Not(&$res, $sub)
    {
        $res['php'] = '!';
    }
    
    public function PresenceCheck_Argument(&$res, $sub)
    {
        if ($sub['ArgumentMode'] == 'string') {
            $res['php'] .= '((bool)'.$sub['php'].')';
        } else {
            if ($sub['ArgumentMode'] == 'default') {
                $res['php'] .= str_replace('$$FINAL', 'hasValue', $sub['lookup_php']);
            } else {
                $res['php'] .= str_replace('$$FINAL', 'hasValue', $sub['php']);
            }
        }
    }

    /*!*
     
    # if and else_if arguments are a series of presence checks and comparisons, optionally seperated by boolean
    # operators
     
    IfArgumentPortion: Comparison | PresenceCheck
    */
    public function IfArgumentPortion_STR(&$res, $sub)
    {
        $res['php'] = $sub['php'];
    }

    /*!*
     
    # if and else_if arguments can be combined via these two boolean operators. No precendence overriding is
    # supported
         
    BooleanOperator: "||" | "&&"
    
    # This is the combination of the previous if and else_if argument portions
     
    IfArgument: :IfArgumentPortion ( < :BooleanOperator < :IfArgumentPortion )*
    */
    public function IfArgument_IfArgumentPortion(&$res, $sub)
    {
        $res['php'] .= $sub['php'];
    }

    public function IfArgument_BooleanOperator(&$res, $sub)
    {
        $res['php'] .= $sub['text'];
    }

    /*!*
     
    # ifs are handled seperately from other closed block tags, because (A) their structure is different - they
    # can have else_if and else tags in between the if tag and the end_if tag, and (B) they have a different
    # argument structure to every other block
     
    IfPart: '<%' < 'if' [ :IfArgument > '%>' Template:$TemplateMatcher?
    ElseIfPart: '<%' < 'else_if' [ :IfArgument > '%>' Template:$TemplateMatcher?
    ElsePart: '<%' < 'else' > '%>' Template:$TemplateMatcher?

    If: IfPart ElseIfPart* ElsePart? '<%' < 'end_if' > '%>'
    */
    public function If_IfPart(&$res, $sub)
    {
        $ifArgument = $sub['IfArgument']['php'];
        $template = (isset($sub['Template']) ? $sub['Template']['php'] : '');

        $res['php'] = <<<PHP
if ($ifArgument) {
$template
}
PHP;
    }

    public function If_ElseIfPart(&$res, $sub)
    {
        $ifArgument = $sub['IfArgument']['php'];
        $template = (isset($sub['Template']) ? $sub['Template']['php'] : '');

        $res['php'] .= <<<PHP
else if ($ifArgument) {
$template
}
PHP;
    }

    public function If_ElsePart(&$res, $sub)
    {
        $template = (isset($sub['Template']) ? $sub['Template']['php'] : '');

        $res['php'] .= <<<PHP
else {
$template
}
PHP;
    }

    /*!*
    
    # The require block is handled seperately to the other open blocks as the argument syntax is different
    # - must have one call style argument, must pass arguments to that call style argument
     
    Require: '<%' < 'require' [ Call:(Method:Word "(" < :CallArguments  > ")") > '%>'
    */
    public function Require_Call(&$res, $sub)
    {
        $res['php'] = "Requirements::".$sub['Method']['text'].'('.$sub['CallArguments']['php'].');';
    }

    /*!*
    
    # Cache block arguments don't support free strings
    
    CacheBlockArgument:
       !( "if " | "unless " )
        (
            :DollarMarkedLookup |
            :QuotedString |
            :Lookup
        )
    */
    public function CacheBlockArgument_DollarMarkedLookup(&$res, $sub)
    {
        $res['php'] = $sub['Lookup']['php'];
    }
    
    public function CacheBlockArgument_QuotedString(&$res, $sub)
    {
        $res['php'] = "'" . str_replace("'", "\\'", $sub['String']['text']) . "'";
    }
    
    public function CacheBlockArgument_Lookup(&$res, $sub)
    {
        $res['php'] = $sub['php'];
    }

    /*!*
    
    # Collects the arguments passed in to be part of the key of a cacheblock
     
    CacheBlockArguments: CacheBlockArgument ( < "," < CacheBlockArgument )*
    */
    public function CacheBlockArguments_CacheBlockArgument(&$res, $sub)
    {
        if (!empty($res['php'])) {
            $res['php'] .= ".'_'.";
        } else {
            $res['php'] = '';
        }
        
        $res['php'] .= str_replace('$$FINAL', 'XML_val', $sub['php']);
    }

    /*!*
    # CacheBlockTemplate is the same as Template, but doesn't include cache blocks (because they're handled seperately)
     
    CacheBlockTemplate extends Template (TemplateMatcher = CacheRestrictedTemplate); CacheBlock | UncachedBlock | => ''
    */
        
    /*!*
      
    UncachedBlock:
        '<%' < "uncached" < CacheBlockArguments? ( < Conditional:("if"|"unless") > Condition:IfArgument )? > '%>'
            Template:$TemplateMatcher?
            '<%' < 'end_' ("uncached"|"cached"|"cacheblock") > '%>'
    */
    public function UncachedBlock_Template(&$res, $sub)
    {
        $res['php'] = $sub['php'];
    }

    /*!*
    
    # CacheRestrictedTemplate is the same as Template, but doesn't allow cache blocks
     
    CacheRestrictedTemplate extends Template
    */
    public function CacheRestrictedTemplate_CacheBlock(&$res, $sub)
    {
        throw new SSTemplateParseException('You cant have cache blocks nested within with, loop or control blocks ' .
            'that are within cache blocks', $this);
    }
    
    public function CacheRestrictedTemplate_UncachedBlock(&$res, $sub)
    {
        throw new SSTemplateParseException('You cant have uncache blocks nested within with, loop or control blocks ' .
            'that are within cache blocks', $this);
    }

    /*!*
    # The partial caching block
     
    CacheBlock:
        '<%' < CacheTag:("cached"|"cacheblock") < (CacheBlockArguments)? ( < Conditional:("if"|"unless") >
        Condition:IfArgument )? > '%>'
            (CacheBlock | UncachedBlock | CacheBlockTemplate)*
        '<%' < 'end_' ("cached"|"uncached"|"cacheblock") > '%>'
    */
    public function CacheBlock__construct(&$res)
    {
        $res['subblocks'] = 0;
    }
    
    public function CacheBlock_CacheBlockArguments(&$res, $sub)
    {
        $res['key'] = !empty($sub['php']) ? $sub['php'] : '';
    }
    
    public function CacheBlock_Condition(&$res, $sub)
    {
        $php = $sub['php'];

        if ($res['Conditional']['text'] === 'if') {
            $res['condition'] = "({$php})";
        } else {
            $res['condition'] = "!({$php})";
        }
    }
    
    public function CacheBlock_CacheBlock(&$res, $sub)
    {
        $res['php'] .= $sub['php'];
    }
    
    public function CacheBlock_UncachedBlock(&$res, $sub)
    {
        $res['php'] .= $sub['php'];
    }
    
    public function CacheBlock_CacheBlockTemplate(&$res, $sub)
    {
        // Get the block counter
        $block = ++$res['subblocks'];
        // Build the key for this block from the global key (evaluated in a closure within the template),
        // the passed cache key, the block index, and the sha hash of the template.
        $res['php'] .= <<<'PHP'
$keyExpression = function() use ($scope, $cache) {
$val = '';
PHP;

        if ($globalKey = Config::inst()->get('SSViewer', 'global_key')) {
            // Embed the code necessary to evaluate the globalKey directly into the template,
            // so that SSTemplateParser only needs to be called during template regeneration.
            // Warning: If the global key is changed, it's necessary to flush the template cache.
            $parser = Injector::inst()->get('SSTemplateParser', false);
            $result = $parser->compileString($globalKey, '', false, false);
            if (!$result) {
                throw new SSTemplateParseException('Unexpected problem parsing template', $parser);
            }
            $res['php'] .= $result . PHP_EOL;
        }

        $res['php'] .= <<<'PHP'
return $val;
}
PHP;

        $templateHash = sha1($sub['php']);
        $keyHash = (isset($res['key']) && $res['key']) ? "' . sha1(".$res['key'].") . '" : '';

        $keyExpression = <<<PHP
sha1(\$keyExpression()) . '_{$templateHash}_{$keyHash}_{$block}'
PHP;

        // If we've got a condition, we only want to read from + write to the cache if it matches,
        // so construct those statements here
        if (isset($res['condition'])) {
            $condition = $res['condition'];

            $ifCondition = $condition . ' && ($partial = $cache->load(' . $keyExpression . '))';
            $saveStatement = <<<PHP
if ({$condition}) {
	\$cache->save(\$val);
}
PHP;
        } else {
            $ifCondition = '$partial = $cache->load(' . $keyExpression . ')';
            $saveStatement = '$cache->save($val);';
        }

        // Get the PHP used to calculate the cache value
        $cacheCalculationStatement = $sub['php'];

        // Build the PHP result
        $res['php'] .= <<<PHP
if ({$ifCondition}) {
	\$val .= \$partial;
} else {
	\$oldval = \$val; \$val = "";
	{$cacheCalculationStatement}
	{$saveStatement}
	\$val = \$oldval . \$val;
}
PHP;
    }

    /*!*

    # An argument that can be passed through to an included template

    NamedArgument: Name:Word "=" Value:Argument
    */
    public function NamedArgument_Name(&$res, $sub)
    {
        $res['php'] = "'" . $sub['text'] . "' => ";
    }

    public function NamedArgument_Value(&$res, $sub)
    {
        switch ($sub['ArgumentMode']) {
            case 'string':
                $res['php'] .= $sub['php'];
                break;

            case 'default':
                $res['php'] .= $sub['string_php'];
                break;

            default:
                $res['php'] .= str_replace('$$FINAL', 'obj', $sub['php']) . '->self()';
                break;
        }
    }

    /*!*

    # The include tag

    Include: "<%" < "include" < Template:Word < (NamedArgument ( < "," < NamedArgument )*)? > "%>"

    */
    public function Include__construct(&$res)
    {
        $res['arguments'] = array();
    }

    public function Include_Template(&$res, $sub)
    {
        $res['template'] = "'" . $sub['text'] . "'";
    }

    public function Include_NamedArgument(&$res, $sub)
    {
        $res['arguments'][] = $sub['php'];
    }

    public function Include__finalise(&$res)
    {
        $template = $res['template'];
        $arguments = implode(',', $res['arguments']);

        $php = <<<PHP
\$val .= SSViewer::execute_template({$template}, \$scope->getItem(), array({$arguments}), \$scope);
PHP;

        // Add include filename comments if requested
        if ($this->includeDebuggingComments) {
            $template = addslashes($template);
            $res['php'] = <<<PHP
\$val .= '<!-- include {$template} -->';
$php
\$val .= '<!-- end include {$template} -->';
PHP;
        } else {
            $res['php'] = $php;
        }
    }

    /*!*
    
    # To make the block support reasonably extendable, we don't explicitly define each closed block and it's structure,
    # but instead match against a generic <% block_name argument, ... %> pattern. Each argument is left as per the
    # output of the Argument matcher, and the handler (see the PHPDoc block later for more on this) is responsible
    # for pulling out the info required
    # @todo fix caching?
     
    BlockArguments: :Argument ( < "," < :Argument)*
    
    # NotBlockTag matches against any word that might come after a "<%" that the generic open and closed block handlers
    # shouldn't attempt to match against, because they're handled by more explicit matchers
     
    NotBlockTag: "end_" | (("if" | "else_if" | "else" | "require" | "cached" | "uncached" | "cacheblock" | "include")])
    
    # Match against closed blocks - blocks with an opening and a closing tag that surround some internal portion of
    # template
     
    ClosedBlock: '<%' < !NotBlockTag BlockName:Word ( [ :BlockArguments ] )? > Zap:'%>' Template:$TemplateMatcher?
        '<%' < 'end_' '$BlockName' > '%>'
    */
    
    /**
     * As mentioned in the parser comment, block handling is kept fairly generic for extensibility. The match rule
     * builds up two important elements in the match result array:
     *   'ArgumentCount' - how many arguments were passed in the opening tag
     *   'Arguments' an array of the Argument match rule result arrays
     *
     * Once a block has successfully been matched against, it will then look for the actual handler, which should
     * be on this class (either defined or extended on) as ClosedBlock_Handler_Name(&$res), where Name is the
     * tag name, first letter captialized (i.e Control, Loop, With, etc).
     *
     * This function will be called with the match rule result array as it's first argument. It should return
     * the php result of this block as it's return value, or throw an error if incorrect arguments were passed.
     */
    
    public function ClosedBlock__construct(&$res)
    {
        $res['ArgumentCount'] = 0;
    }
    
    public function ClosedBlock_BlockArguments(&$res, $sub)
    {
        if (isset($sub['Argument']['ArgumentMode'])) {
            $res['Arguments'] = array($sub['Argument']);
            $res['ArgumentCount'] = 1;
        } else {
            $res['Arguments'] = $sub['Argument'];
            $res['ArgumentCount'] = count($res['Arguments']);
        }
    }

    public function ClosedBlock__finalise(&$res)
    {
        $blockname = $res['BlockName']['text'];
        $method = "ClosedBlock_Handle_{$blockname}";

        if (method_exists($this, $method)) {
            $res['php'] = $this->$method($res);
        } elseif (isset($this->closedBlocks[$blockname])) {
            $res['php'] = call_user_func($this->closedBlocks[$blockname], $res);
        } else {
            throw new SSTemplateParseException('Unknown closed block "'.$blockname.'" encountered. Perhaps you are ' .
            'not supposed to close this block, or have mis-spelled it?', $this);
        }
    }

    /**
     * This is an example of a block handler function. This one handles the loop tag.
     */
    public function ClosedBlock_Handle_Loop(&$res)
    {
        if ($res['ArgumentCount'] > 1) {
            throw new SSTemplateParseException('Either no or too many arguments in control block. Must be one ' .
                'argument only.', $this);
        }

        if ($res['ArgumentCount'] == 0) {
            // Loop without arguments loops on the current scope
            $on = '$scope->obj(\'Up\', null, true)->obj(\'Foo\', null, true)';
        } else {
            // Loop in the normal way
            $arg = $res['Arguments'][0];

            if ($arg['ArgumentMode'] === 'string') {
                throw new SSTemplateParseException('Control block cant take string as argument.', $this);
            }

            $argument = ($arg['ArgumentMode'] === 'default') ? $arg['lookup_php'] : $arg['php'];
            $on = str_replace('$$FINAL', 'obj', $argument);
        }

        $php = $res['Template']['php'];
        return <<<PHP
{$on};
\$scope->pushScope(); while ((\$key = \$scope->next()) !== false) {
{$php}
} \$scope->popScope();
PHP;
    }
    
    /**
     * The closed block handler for with blocks
     */
    public function ClosedBlock_Handle_With(&$res)
    {
        if ($res['ArgumentCount'] != 1) {
            throw new SSTemplateParseException('Either no or too many arguments in with block. Must be one ' .
                'argument only.', $this);
        }
        
        $arg = $res['Arguments'][0];
        if ($arg['ArgumentMode'] == 'string') {
            throw new SSTemplateParseException('Control block cant take string as argument.', $this);
        }
        
        $argument = ($arg['ArgumentMode'] == 'default') ? $arg['lookup_php'] : $arg['php'];
        $on = str_replace('$$FINAL', 'obj', $argument);
        $php = $res['Template']['php'];

        return <<<PHP
{$on};
\$scope->pushScope();
{$php}
\$scope->popScope();
PHP;
    }

    /*!*
     
    # Open blocks are handled in the same generic manner as closed blocks. There is no need to define which blocks
    # are which - closed is tried first, and if no matching end tag is found, open is tried next
      
    OpenBlock: '<%' < !NotBlockTag BlockName:Word ( [ :BlockArguments ] )? > '%>'
    */
    public function OpenBlock__construct(&$res)
    {
        $res['ArgumentCount'] = 0;
    }
    
    public function OpenBlock_BlockArguments(&$res, $sub)
    {
        if (isset($sub['Argument']['ArgumentMode'])) {
            $res['Arguments'] = array($sub['Argument']);
            $res['ArgumentCount'] = 1;
        } else {
            $res['Arguments'] = $sub['Argument'];
            $res['ArgumentCount'] = count($res['Arguments']);
        }
    }

    public function OpenBlock__finalise(&$res)
    {
        $blockname = $res['BlockName']['text'];
        $method = "OpenBlock_Handle_{$blockname}";

        if (method_exists($this, $method)) {
            $res['php'] = $this->$method($res);
        } elseif (isset($this->openBlocks[$blockname])) {
            $res['php'] = call_user_func($this->openBlocks[$blockname], $res);
        } else {
            throw new SSTemplateParseException('Unknown open block "'.$blockname.'" encountered. Perhaps you missed ' .
            ' the closing tag or have mis-spelled it?', $this);
        }
    }

    /**
     * This is an open block handler, for the <% debug %> utility tag
     */
    public function OpenBlock_Handle_Debug(&$res)
    {
        if ($res['ArgumentCount'] == 0) {
            return <<<'PHP'
$scope->debug();
PHP;
        } elseif ($res['ArgumentCount'] == 1) {
            $arg = $res['Arguments'][0];
            
            if ($arg['ArgumentMode'] == 'string') {
                $php = $arg['php'];
                return <<<PHP
Debug::show({$php});
PHP;
            }
            
            $php = ($arg['ArgumentMode'] == 'default') ? $arg['lookup_php'] : $arg['php'];
            $php = str_replace('FINALGET!', 'cachedCall', $php);
            return <<<PHP
\$val .= Debug::show({$php});
PHP;
        } else {
            throw new SSTemplateParseException('Debug takes 0 or 1 argument only.', $this);
        }
    }

    /**
     * This is an open block handler, for the <% base_tag %> tag
     */
    public function OpenBlock_Handle_Base_tag(&$res)
    {
        if ($res['ArgumentCount'] != 0) {
            throw new SSTemplateParseException('Base_tag takes no arguments', $this);
        }

        return <<<'PHP'
$val .= SSViewer::get_base_tag($val);
PHP;
    }

    /**
     * This is an open block handler, for the <% current_page %> tag
     */
    public function OpenBlock_Handle_Current_page(&$res)
    {
        if ($res['ArgumentCount'] != 0) {
            throw new SSTemplateParseException('Current_page takes no arguments', $this);
        }

        return <<<'PHP'
$val .= $_SERVER[SCRIPT_URL];
PHP;
    }
    
    /*!*
    
    # This is used to detect when we have a mismatched closing tag (i.e., one with no equivilent opening tag)
    # Because of parser limitations, this can only be used at the top nesting level of a template. Other mismatched
    # closing tags are detected as an invalid open tag
     
    MismatchedEndBlock: '<%' < 'end_' :Word > '%>'
    */
    public function MismatchedEndBlock__finalise(&$res)
    {
        $blockname = $res['Word']['text'];
        throw new SSTemplateParseException('Unexpected close tag end_' . $blockname .
            ' encountered. Perhaps you have mis-nested blocks, or have mis-spelled a tag?', $this);
    }

    /*!*
     
    # This is used to detect a malformed opening tag - one where the tag is opened with the "<%" characters, but
    # the tag is not structured properly
     
    MalformedOpenTag: '<%' < !NotBlockTag Tag:Word  !( ( [ :BlockArguments ] )? > '%>' )
    */
    public function MalformedOpenTag__finalise(&$res)
    {
        $tag = $res['Tag']['text'];
        throw new SSTemplateParseException("Malformed opening block tag $tag. Perhaps you have tried to use operators?", $this);
    }
    
    /*!*
     
    # This is used to detect a malformed end tag - one where the tag is opened with the "<%" characters, but
    # the tag is not structured properly
     
    MalformedCloseTag: '<%' < Tag:('end_' :Word ) !( > '%>' )
    */
    public function MalformedCloseTag__finalise(&$res)
    {
        $tag = $res['Tag']['text'];
        throw new SSTemplateParseException("Malformed closing block tag $tag. Perhaps you have tried to pass an " .
            "argument to one?", $this);
    }

    /*!*
     
    # This is used to detect a malformed tag. It's mostly to keep the Template match rule a bit shorter
     
    MalformedBlock: MalformedOpenTag | MalformedCloseTag
    */

    /*!*
     
    # This is used to remove template comments
     
    Comment: "<%--" (!"--%>" /(?s)./)+ "--%>"
    */
    public function Comment__construct(&$res)
    {
        $res['php'] = '';
    }

    /*!*
    
    # TopTemplate is the same as Template, but should only be used at the top level (not nested), as it includes
    # MismatchedEndBlock detection, which only works at the top level
     
    TopTemplate extends Template (TemplateMatcher = Template); MalformedBlock => MalformedBlock | MismatchedEndBlock
    */
    
    /**
     * The TopTemplate also includes the opening stanza to start off the template
     */
    public function TopTemplate__construct(&$res)
    {
        $res['php'] = <<<PHP
<?php


PHP;
    }

    /*!*
    
    # Text matches anything that isn't a template command (not an injection, block of any kind or comment)
     
    Text: (
            # Any set of characters that aren't potentially a control mark or an escaped character
            / [^<${\\]+ / |
            # An escaped character
            / (\\.) / |
            # A '<' that isn't the start of a block tag
            '<' !'%' |
            # A '$' that isn't enclosed in braces
            !'{' '$' |
            # A '{' that isn't the start of an injection
            '{' !'$' |
            # A '{$' that isn't the start of an injection
            '{$' !(/[A-Za-z_]/)
        )+
    */

    public function Text__finalise(&$res)
    {
        $text = $res['text'];
        
        // Unescape any escaped characters in the text, then put back escapes for any single quotes and backslashes
        $text = stripslashes($text);
        $text = addcslashes($text, '\'\\');

        // TODO: This is pretty ugly & gets applied on all files not just html. I wonder if we can make this
        // non-dynamically calculated
        $text = preg_replace(
            '/(<a[^>]+href *= *)"#/i',
            '\\1"\' . (Config::inst()->get(\'SSViewer\', \'rewrite_hash_links\') ?' .
            ' Convert::raw2att( $_SERVER[\'REQUEST_URI\'] ) : "") .
				\'#',
            $text
        );

        $res['php'] .= <<<PHP
\$val .= '{$text}';

PHP;
    }
        
    /******************
     * Here ends the parser itself. Below are utility methods to use the parser
     */

    /**
     * Compiles some passed template source code into the php code that will execute as per the template source.
     *
     * @throws SSTemplateParseException
     * @param  $string The source of the template
     * @param string $templateName The name of the template, normally the filename the template source was loaded from
     * @param bool $includeDebuggingComments True is debugging comments should be included in the output
     * @param bool $topTemplate True if this is a top template, false if it's just a template
     * @return mixed|string The php that, when executed (via include or exec) will behave as per the template source
     * @todo tidy
     */
    public function compileString($string, $templateName = '', $includeDebuggingComments = false, $topTemplate = true)
    {
        if (!trim($string)) {
            $code = '';
        } else {
            parent::__construct($string);
            
            $this->includeDebuggingComments = $includeDebuggingComments;
            
            // Match the source against the parser
            if ($topTemplate) {
                $result = $this->match_TopTemplate();
            } else {
                $result = $this->match_Template();
            }

            if (!$result) {
                throw new SSTemplateParseException('Unexpected problem parsing template', $this);
            }
    
            // Get the result
            $code = $result['php'];
        }

        // Include top level debugging comments if desired
        if ($includeDebuggingComments && $templateName && stripos($code, "<?xml") === false) {
            $code = $this->includeDebuggingComments($code, $templateName);
        }
        
        return $code;
    }

    /**
     * @todo compileFile etc
     */
}
