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

    /* Template: (Comment | Translate | If | Require | CacheBlock | UncachedBlock | Include | ClosedBlock | OpenBlock |
    MalformedBlock | Injection | Text)+ */
    protected $match_Template_typestack = array('Template');
    function match_Template ($stack = array()) {
    	$matchrule = "Template"; $result = $this->construct($matchrule, $matchrule, null);
    	$count = 0;
    	while (true) {
    		$res_46 = $result;
    		$pos_46 = $this->pos;
    		$_45 = NULL;
    		do {
    			$_43 = NULL;
    			do {
    				$res_0 = $result;
    				$pos_0 = $this->pos;
    				$matcher = 'match_'.'Comment'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres );
    					$_43 = TRUE; break;
    				}
    				$result = $res_0;
    				$this->pos = $pos_0;
    				$_41 = NULL;
    				do {
    					$res_2 = $result;
    					$pos_2 = $this->pos;
    					$matcher = 'match_'.'Translate'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    						$_41 = TRUE; break;
    					}
    					$result = $res_2;
    					$this->pos = $pos_2;
    					$_39 = NULL;
    					do {
    						$res_4 = $result;
    						$pos_4 = $this->pos;
    						$matcher = 'match_'.'If'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres );
    							$_39 = TRUE; break;
    						}
    						$result = $res_4;
    						$this->pos = $pos_4;
    						$_37 = NULL;
    						do {
    							$res_6 = $result;
    							$pos_6 = $this->pos;
    							$matcher = 'match_'.'Require'; $key = $matcher; $pos = $this->pos;
    							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    							if ($subres !== FALSE) {
    								$this->store( $result, $subres );
    								$_37 = TRUE; break;
    							}
    							$result = $res_6;
    							$this->pos = $pos_6;
    							$_35 = NULL;
    							do {
    								$res_8 = $result;
    								$pos_8 = $this->pos;
    								$matcher = 'match_'.'CacheBlock'; $key = $matcher; $pos = $this->pos;
    								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    								if ($subres !== FALSE) {
    									$this->store( $result, $subres );
    									$_35 = TRUE; break;
    								}
    								$result = $res_8;
    								$this->pos = $pos_8;
    								$_33 = NULL;
    								do {
    									$res_10 = $result;
    									$pos_10 = $this->pos;
    									$matcher = 'match_'.'UncachedBlock'; $key = $matcher; $pos = $this->pos;
    									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    									if ($subres !== FALSE) {
    										$this->store( $result, $subres );
    										$_33 = TRUE; break;
    									}
    									$result = $res_10;
    									$this->pos = $pos_10;
    									$_31 = NULL;
    									do {
    										$res_12 = $result;
    										$pos_12 = $this->pos;
    										$matcher = 'match_'.'Include'; $key = $matcher; $pos = $this->pos;
    										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    										if ($subres !== FALSE) {
    											$this->store( $result, $subres );
    											$_31 = TRUE; break;
    										}
    										$result = $res_12;
    										$this->pos = $pos_12;
    										$_29 = NULL;
    										do {
    											$res_14 = $result;
    											$pos_14 = $this->pos;
    											$matcher = 'match_'.'ClosedBlock'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres );
    												$_29 = TRUE; break;
    											}
    											$result = $res_14;
    											$this->pos = $pos_14;
    											$_27 = NULL;
    											do {
    												$res_16 = $result;
    												$pos_16 = $this->pos;
    												$matcher = 'match_'.'OpenBlock'; $key = $matcher; $pos = $this->pos;
    												$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    												if ($subres !== FALSE) {
    													$this->store( $result, $subres );
    													$_27 = TRUE; break;
    												}
    												$result = $res_16;
    												$this->pos = $pos_16;
    												$_25 = NULL;
    												do {
    													$res_18 = $result;
    													$pos_18 = $this->pos;
    													$matcher = 'match_'.'MalformedBlock'; $key = $matcher; $pos = $this->pos;
    													$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    													if ($subres !== FALSE) {
    														$this->store( $result, $subres );
    														$_25 = TRUE; break;
    													}
    													$result = $res_18;
    													$this->pos = $pos_18;
    													$_23 = NULL;
    													do {
    														$res_20 = $result;
    														$pos_20 = $this->pos;
    														$matcher = 'match_'.'Injection'; $key = $matcher; $pos = $this->pos;
    														$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    														if ($subres !== FALSE) {
    															$this->store( $result, $subres );
    															$_23 = TRUE; break;
    														}
    														$result = $res_20;
    														$this->pos = $pos_20;
    														$matcher = 'match_'.'Text'; $key = $matcher; $pos = $this->pos;
    														$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    														if ($subres !== FALSE) {
    															$this->store( $result, $subres );
    															$_23 = TRUE; break;
    														}
    														$result = $res_20;
    														$this->pos = $pos_20;
    														$_23 = FALSE; break;
    													}
    													while(0);
    													if( $_23 === TRUE ) { $_25 = TRUE; break; }
    													$result = $res_18;
    													$this->pos = $pos_18;
    													$_25 = FALSE; break;
    												}
    												while(0);
    												if( $_25 === TRUE ) { $_27 = TRUE; break; }
    												$result = $res_16;
    												$this->pos = $pos_16;
    												$_27 = FALSE; break;
    											}
    											while(0);
    											if( $_27 === TRUE ) { $_29 = TRUE; break; }
    											$result = $res_14;
    											$this->pos = $pos_14;
    											$_29 = FALSE; break;
    										}
    										while(0);
    										if( $_29 === TRUE ) { $_31 = TRUE; break; }
    										$result = $res_12;
    										$this->pos = $pos_12;
    										$_31 = FALSE; break;
    									}
    									while(0);
    									if( $_31 === TRUE ) { $_33 = TRUE; break; }
    									$result = $res_10;
    									$this->pos = $pos_10;
    									$_33 = FALSE; break;
    								}
    								while(0);
    								if( $_33 === TRUE ) { $_35 = TRUE; break; }
    								$result = $res_8;
    								$this->pos = $pos_8;
    								$_35 = FALSE; break;
    							}
    							while(0);
    							if( $_35 === TRUE ) { $_37 = TRUE; break; }
    							$result = $res_6;
    							$this->pos = $pos_6;
    							$_37 = FALSE; break;
    						}
    						while(0);
    						if( $_37 === TRUE ) { $_39 = TRUE; break; }
    						$result = $res_4;
    						$this->pos = $pos_4;
    						$_39 = FALSE; break;
    					}
    					while(0);
    					if( $_39 === TRUE ) { $_41 = TRUE; break; }
    					$result = $res_2;
    					$this->pos = $pos_2;
    					$_41 = FALSE; break;
    				}
    				while(0);
    				if( $_41 === TRUE ) { $_43 = TRUE; break; }
    				$result = $res_0;
    				$this->pos = $pos_0;
    				$_43 = FALSE; break;
    			}
    			while(0);
    			if( $_43 === FALSE) { $_45 = FALSE; break; }
    			$_45 = TRUE; break;
    		}
    		while(0);
    		if( $_45 === FALSE) {
    			$result = $res_46;
    			$this->pos = $pos_46;
    			unset( $res_46 );
    			unset( $pos_46 );
    			break;
    		}
    		$count += 1;
    	}
    	if ($count > 0) { return $this->finalise($result); }
    	else { return FALSE; }
    }



    public function Template_STR(&$res, $sub)
    {
        $res['php'] .= $sub['php'] . PHP_EOL ;
    }

    /* Word: / [A-Za-z_] [A-Za-z0-9_]* / */
    protected $match_Word_typestack = array('Word');
    function match_Word ($stack = array()) {
    	$matchrule = "Word"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/ [A-Za-z_] [A-Za-z0-9_]* /' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* Number: / [0-9]+ / */
    protected $match_Number_typestack = array('Number');
    function match_Number ($stack = array()) {
    	$matchrule = "Number"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/ [0-9]+ /' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* Value: / [A-Za-z0-9_]+ / */
    protected $match_Value_typestack = array('Value');
    function match_Value ($stack = array()) {
    	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/ [A-Za-z0-9_]+ /' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* CallArguments: :Argument ( < "," < :Argument )* */
    protected $match_CallArguments_typestack = array('CallArguments');
    function match_CallArguments ($stack = array()) {
    	$matchrule = "CallArguments"; $result = $this->construct($matchrule, $matchrule, null);
    	$_57 = NULL;
    	do {
    		$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Argument" );
    		}
    		else { $_57 = FALSE; break; }
    		while (true) {
    			$res_56 = $result;
    			$pos_56 = $this->pos;
    			$_55 = NULL;
    			do {
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				if (substr($this->string,$this->pos,1) == ',') {
    					$this->pos += 1;
    					$result["text"] .= ',';
    				}
    				else { $_55 = FALSE; break; }
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "Argument" );
    				}
    				else { $_55 = FALSE; break; }
    				$_55 = TRUE; break;
    			}
    			while(0);
    			if( $_55 === FALSE) {
    				$result = $res_56;
    				$this->pos = $pos_56;
    				unset( $res_56 );
    				unset( $pos_56 );
    				break;
    			}
    		}
    		$_57 = TRUE; break;
    	}
    	while(0);
    	if( $_57 === TRUE ) { return $this->finalise($result); }
    	if( $_57 === FALSE) { return FALSE; }
    }




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

    /* Call: Method:Word ( "(" < :CallArguments? > ")" )? */
    protected $match_Call_typestack = array('Call');
    function match_Call ($stack = array()) {
    	$matchrule = "Call"; $result = $this->construct($matchrule, $matchrule, null);
    	$_67 = NULL;
    	do {
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Method" );
    		}
    		else { $_67 = FALSE; break; }
    		$res_66 = $result;
    		$pos_66 = $this->pos;
    		$_65 = NULL;
    		do {
    			if (substr($this->string,$this->pos,1) == '(') {
    				$this->pos += 1;
    				$result["text"] .= '(';
    			}
    			else { $_65 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$res_62 = $result;
    			$pos_62 = $this->pos;
    			$matcher = 'match_'.'CallArguments'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "CallArguments" );
    			}
    			else {
    				$result = $res_62;
    				$this->pos = $pos_62;
    				unset( $res_62 );
    				unset( $pos_62 );
    			}
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			if (substr($this->string,$this->pos,1) == ')') {
    				$this->pos += 1;
    				$result["text"] .= ')';
    			}
    			else { $_65 = FALSE; break; }
    			$_65 = TRUE; break;
    		}
    		while(0);
    		if( $_65 === FALSE) {
    			$result = $res_66;
    			$this->pos = $pos_66;
    			unset( $res_66 );
    			unset( $pos_66 );
    		}
    		$_67 = TRUE; break;
    	}
    	while(0);
    	if( $_67 === TRUE ) { return $this->finalise($result); }
    	if( $_67 === FALSE) { return FALSE; }
    }


    /* LookupStep: :Call &"." */
    protected $match_LookupStep_typestack = array('LookupStep');
    function match_LookupStep ($stack = array()) {
    	$matchrule = "LookupStep"; $result = $this->construct($matchrule, $matchrule, null);
    	$_71 = NULL;
    	do {
    		$matcher = 'match_'.'Call'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Call" );
    		}
    		else { $_71 = FALSE; break; }
    		$res_70 = $result;
    		$pos_70 = $this->pos;
    		if (substr($this->string,$this->pos,1) == '.') {
    			$this->pos += 1;
    			$result["text"] .= '.';
    			$result = $res_70;
    			$this->pos = $pos_70;
    		}
    		else {
    			$result = $res_70;
    			$this->pos = $pos_70;
    			$_71 = FALSE; break;
    		}
    		$_71 = TRUE; break;
    	}
    	while(0);
    	if( $_71 === TRUE ) { return $this->finalise($result); }
    	if( $_71 === FALSE) { return FALSE; }
    }


    /* LastLookupStep: :Call */
    protected $match_LastLookupStep_typestack = array('LastLookupStep');
    function match_LastLookupStep ($stack = array()) {
    	$matchrule = "LastLookupStep"; $result = $this->construct($matchrule, $matchrule, null);
    	$matcher = 'match_'.'Call'; $key = $matcher; $pos = $this->pos;
    	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    	if ($subres !== FALSE) {
    		$this->store( $result, $subres, "Call" );
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* Lookup: LookupStep ("." LookupStep)* "." LastLookupStep | LastLookupStep */
    protected $match_Lookup_typestack = array('Lookup');
    function match_Lookup ($stack = array()) {
    	$matchrule = "Lookup"; $result = $this->construct($matchrule, $matchrule, null);
    	$_85 = NULL;
    	do {
    		$res_74 = $result;
    		$pos_74 = $this->pos;
    		$_82 = NULL;
    		do {
    			$matcher = 'match_'.'LookupStep'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else { $_82 = FALSE; break; }
    			while (true) {
    				$res_79 = $result;
    				$pos_79 = $this->pos;
    				$_78 = NULL;
    				do {
    					if (substr($this->string,$this->pos,1) == '.') {
    						$this->pos += 1;
    						$result["text"] .= '.';
    					}
    					else { $_78 = FALSE; break; }
    					$matcher = 'match_'.'LookupStep'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    					}
    					else { $_78 = FALSE; break; }
    					$_78 = TRUE; break;
    				}
    				while(0);
    				if( $_78 === FALSE) {
    					$result = $res_79;
    					$this->pos = $pos_79;
    					unset( $res_79 );
    					unset( $pos_79 );
    					break;
    				}
    			}
    			if (substr($this->string,$this->pos,1) == '.') {
    				$this->pos += 1;
    				$result["text"] .= '.';
    			}
    			else { $_82 = FALSE; break; }
    			$matcher = 'match_'.'LastLookupStep'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else { $_82 = FALSE; break; }
    			$_82 = TRUE; break;
    		}
    		while(0);
    		if( $_82 === TRUE ) { $_85 = TRUE; break; }
    		$result = $res_74;
    		$this->pos = $pos_74;
    		$matcher = 'match_'.'LastLookupStep'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_85 = TRUE; break;
    		}
    		$result = $res_74;
    		$this->pos = $pos_74;
    		$_85 = FALSE; break;
    	}
    	while(0);
    	if( $_85 === TRUE ) { return $this->finalise($result); }
    	if( $_85 === FALSE) { return FALSE; }
    }



    
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

    /* Translate: "<%t" < Entity < (Default:QuotedString)? < (!("is" "=") < "is" < Context:QuotedString)? <
    (InjectionVariables)? > "%>" */
    protected $match_Translate_typestack = array('Translate');
    function match_Translate ($stack = array()) {
    	$matchrule = "Translate"; $result = $this->construct($matchrule, $matchrule, null);
    	$_111 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%t' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_111 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$matcher = 'match_'.'Entity'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_111 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_93 = $result;
    		$pos_93 = $this->pos;
    		$_92 = NULL;
    		do {
    			$matcher = 'match_'.'QuotedString'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "Default" );
    			}
    			else { $_92 = FALSE; break; }
    			$_92 = TRUE; break;
    		}
    		while(0);
    		if( $_92 === FALSE) {
    			$result = $res_93;
    			$this->pos = $pos_93;
    			unset( $res_93 );
    			unset( $pos_93 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_104 = $result;
    		$pos_104 = $this->pos;
    		$_103 = NULL;
    		do {
    			$res_98 = $result;
    			$pos_98 = $this->pos;
    			$_97 = NULL;
    			do {
    				if (( $subres = $this->literal( 'is' ) ) !== FALSE) { $result["text"] .= $subres; }
    				else { $_97 = FALSE; break; }
    				if (substr($this->string,$this->pos,1) == '=') {
    					$this->pos += 1;
    					$result["text"] .= '=';
    				}
    				else { $_97 = FALSE; break; }
    				$_97 = TRUE; break;
    			}
    			while(0);
    			if( $_97 === TRUE ) {
    				$result = $res_98;
    				$this->pos = $pos_98;
    				$_103 = FALSE; break;
    			}
    			if( $_97 === FALSE) {
    				$result = $res_98;
    				$this->pos = $pos_98;
    			}
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			if (( $subres = $this->literal( 'is' ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_103 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$matcher = 'match_'.'QuotedString'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "Context" );
    			}
    			else { $_103 = FALSE; break; }
    			$_103 = TRUE; break;
    		}
    		while(0);
    		if( $_103 === FALSE) {
    			$result = $res_104;
    			$this->pos = $pos_104;
    			unset( $res_104 );
    			unset( $pos_104 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_108 = $result;
    		$pos_108 = $this->pos;
    		$_107 = NULL;
    		do {
    			$matcher = 'match_'.'InjectionVariables'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else { $_107 = FALSE; break; }
    			$_107 = TRUE; break;
    		}
    		while(0);
    		if( $_107 === FALSE) {
    			$result = $res_108;
    			$this->pos = $pos_108;
    			unset( $res_108 );
    			unset( $pos_108 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_111 = FALSE; break; }
    		$_111 = TRUE; break;
    	}
    	while(0);
    	if( $_111 === TRUE ) { return $this->finalise($result); }
    	if( $_111 === FALSE) { return FALSE; }
    }


    /* InjectionVariables: (< InjectionName:Word "=" Argument)+ */
    protected $match_InjectionVariables_typestack = array('InjectionVariables');
    function match_InjectionVariables ($stack = array()) {
    	$matchrule = "InjectionVariables"; $result = $this->construct($matchrule, $matchrule, null);
    	$count = 0;
    	while (true) {
    		$res_118 = $result;
    		$pos_118 = $this->pos;
    		$_117 = NULL;
    		do {
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "InjectionName" );
    			}
    			else { $_117 = FALSE; break; }
    			if (substr($this->string,$this->pos,1) == '=') {
    				$this->pos += 1;
    				$result["text"] .= '=';
    			}
    			else { $_117 = FALSE; break; }
    			$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else { $_117 = FALSE; break; }
    			$_117 = TRUE; break;
    		}
    		while(0);
    		if( $_117 === FALSE) {
    			$result = $res_118;
    			$this->pos = $pos_118;
    			unset( $res_118 );
    			unset( $pos_118 );
    			break;
    		}
    		$count += 1;
    	}
    	if ($count > 0) { return $this->finalise($result); }
    	else { return FALSE; }
    }


    /* Entity: / [A-Za-z_] [\w\.]* / */
    protected $match_Entity_typestack = array('Entity');
    function match_Entity ($stack = array()) {
    	$matchrule = "Entity"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/ [A-Za-z_] [\w\.]* /' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }




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

    /* SimpleInjection: '$' :Lookup */
    protected $match_SimpleInjection_typestack = array('SimpleInjection');
    function match_SimpleInjection ($stack = array()) {
    	$matchrule = "SimpleInjection"; $result = $this->construct($matchrule, $matchrule, null);
    	$_122 = NULL;
    	do {
    		if (substr($this->string,$this->pos,1) == '$') {
    			$this->pos += 1;
    			$result["text"] .= '$';
    		}
    		else { $_122 = FALSE; break; }
    		$matcher = 'match_'.'Lookup'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Lookup" );
    		}
    		else { $_122 = FALSE; break; }
    		$_122 = TRUE; break;
    	}
    	while(0);
    	if( $_122 === TRUE ) { return $this->finalise($result); }
    	if( $_122 === FALSE) { return FALSE; }
    }


    /* BracedInjection: '{$' :Lookup "}" */
    protected $match_BracedInjection_typestack = array('BracedInjection');
    function match_BracedInjection ($stack = array()) {
    	$matchrule = "BracedInjection"; $result = $this->construct($matchrule, $matchrule, null);
    	$_127 = NULL;
    	do {
    		if (( $subres = $this->literal( '{$' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_127 = FALSE; break; }
    		$matcher = 'match_'.'Lookup'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Lookup" );
    		}
    		else { $_127 = FALSE; break; }
    		if (substr($this->string,$this->pos,1) == '}') {
    			$this->pos += 1;
    			$result["text"] .= '}';
    		}
    		else { $_127 = FALSE; break; }
    		$_127 = TRUE; break;
    	}
    	while(0);
    	if( $_127 === TRUE ) { return $this->finalise($result); }
    	if( $_127 === FALSE) { return FALSE; }
    }


    /* Injection: BracedInjection | SimpleInjection */
    protected $match_Injection_typestack = array('Injection');
    function match_Injection ($stack = array()) {
    	$matchrule = "Injection"; $result = $this->construct($matchrule, $matchrule, null);
    	$_132 = NULL;
    	do {
    		$res_129 = $result;
    		$pos_129 = $this->pos;
    		$matcher = 'match_'.'BracedInjection'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_132 = TRUE; break;
    		}
    		$result = $res_129;
    		$this->pos = $pos_129;
    		$matcher = 'match_'.'SimpleInjection'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_132 = TRUE; break;
    		}
    		$result = $res_129;
    		$this->pos = $pos_129;
    		$_132 = FALSE; break;
    	}
    	while(0);
    	if( $_132 === TRUE ) { return $this->finalise($result); }
    	if( $_132 === FALSE) { return FALSE; }
    }



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

    /* SimpleArgumentInjection: '$' :Lookup */
    protected $match_SimpleArgumentInjection_typestack = array('SimpleArgumentInjection');
    function match_SimpleArgumentInjection ($stack = array()) {
    	$matchrule = "SimpleArgumentInjection"; $result = $this->construct($matchrule, $matchrule, null);
    	$_136 = NULL;
    	do {
    		if (substr($this->string,$this->pos,1) == '$') {
    			$this->pos += 1;
    			$result["text"] .= '$';
    		}
    		else { $_136 = FALSE; break; }
    		$matcher = 'match_'.'Lookup'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Lookup" );
    		}
    		else { $_136 = FALSE; break; }
    		$_136 = TRUE; break;
    	}
    	while(0);
    	if( $_136 === TRUE ) { return $this->finalise($result); }
    	if( $_136 === FALSE) { return FALSE; }
    }


    /* DollarMarkedLookup: SimpleArgumentInjection */
    protected $match_DollarMarkedLookup_typestack = array('DollarMarkedLookup');
    function match_DollarMarkedLookup ($stack = array()) {
    	$matchrule = "DollarMarkedLookup"; $result = $this->construct($matchrule, $matchrule, null);
    	$matcher = 'match_'.'SimpleArgumentInjection'; $key = $matcher; $pos = $this->pos;
    	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    	if ($subres !== FALSE) {
    		$this->store( $result, $subres );
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }



    public function DollarMarkedLookup_STR(&$res, $sub)
    {
        $res['Lookup'] = $sub['Lookup'];
    }

    /* QuotedString: q:/['"]/   String:/ (\\\\ | \\. | [^$q\\])* /   '$q' */
    protected $match_QuotedString_typestack = array('QuotedString');
    function match_QuotedString ($stack = array()) {
    	$matchrule = "QuotedString"; $result = $this->construct($matchrule, $matchrule, null);
    	$_142 = NULL;
    	do {
    		$stack[] = $result; $result = $this->construct( $matchrule, "q" ); 
    		if (( $subres = $this->rx( '/[\'"]/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'q' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_142 = FALSE; break;
    		}
    		$stack[] = $result; $result = $this->construct( $matchrule, "String" ); 
    		if (( $subres = $this->rx( '/ (\\\\\\\\ | \\\\. | [^'.$this->expression($result, $stack, 'q').'\\\\])* /' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'String' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_142 = FALSE; break;
    		}
    		if (( $subres = $this->literal( ''.$this->expression($result, $stack, 'q').'' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_142 = FALSE; break; }
    		$_142 = TRUE; break;
    	}
    	while(0);
    	if( $_142 === TRUE ) { return $this->finalise($result); }
    	if( $_142 === FALSE) { return FALSE; }
    }


    /* PHPLiteral: /[^,)%!=><|&]+/ */
    protected $match_PHPLiteral_typestack = array('PHPLiteral');
    function match_PHPLiteral ($stack = array()) {
    	$matchrule = "PHPLiteral"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/[^,)%!=><|&]+/' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* Concatenatable:
    :DollarMarkedLookup |
    :QuotedString |
    :PHPLiteral */
    protected $match_Concatenatable_typestack = array('Concatenatable');
    function match_Concatenatable ($stack = array()) {
    	$matchrule = "Concatenatable"; $result = $this->construct($matchrule, $matchrule, null);
    	$_152 = NULL;
    	do {
    		$res_145 = $result;
    		$pos_145 = $this->pos;
    		$matcher = 'match_'.'DollarMarkedLookup'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "DollarMarkedLookup" );
    			$_152 = TRUE; break;
    		}
    		$result = $res_145;
    		$this->pos = $pos_145;
    		$_150 = NULL;
    		do {
    			$res_147 = $result;
    			$pos_147 = $this->pos;
    			$matcher = 'match_'.'QuotedString'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "QuotedString" );
    				$_150 = TRUE; break;
    			}
    			$result = $res_147;
    			$this->pos = $pos_147;
    			$matcher = 'match_'.'PHPLiteral'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "PHPLiteral" );
    				$_150 = TRUE; break;
    			}
    			$result = $res_147;
    			$this->pos = $pos_147;
    			$_150 = FALSE; break;
    		}
    		while(0);
    		if( $_150 === TRUE ) { $_152 = TRUE; break; }
    		$result = $res_145;
    		$this->pos = $pos_145;
    		$_152 = FALSE; break;
    	}
    	while(0);
    	if( $_152 === TRUE ) { return $this->finalise($result); }
    	if( $_152 === FALSE) { return FALSE; }
    }


    /* Concatenated: :Concatenatable ( < "." < :Concatenatable )+ */
    protected $match_Concatenated_typestack = array('Concatenated');
    function match_Concatenated ($stack = array()) {
    	$matchrule = "Concatenated"; $result = $this->construct($matchrule, $matchrule, null);
    	$_161 = NULL;
    	do {
    		$matcher = 'match_'.'Concatenatable'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Concatenatable" );
    		}
    		else { $_161 = FALSE; break; }
    		$count = 0;
    		while (true) {
    			$res_160 = $result;
    			$pos_160 = $this->pos;
    			$_159 = NULL;
    			do {
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				if (substr($this->string,$this->pos,1) == '.') {
    					$this->pos += 1;
    					$result["text"] .= '.';
    				}
    				else { $_159 = FALSE; break; }
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				$matcher = 'match_'.'Concatenatable'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "Concatenatable" );
    				}
    				else { $_159 = FALSE; break; }
    				$_159 = TRUE; break;
    			}
    			while(0);
    			if( $_159 === FALSE) {
    				$result = $res_160;
    				$this->pos = $pos_160;
    				unset( $res_160 );
    				unset( $pos_160 );
    				break;
    			}
    			$count += 1;
    		}
    		if ($count > 0) {  }
    		else { $_161 = FALSE; break; }
    		$_161 = TRUE; break;
    	}
    	while(0);
    	if( $_161 === TRUE ) { return $this->finalise($result); }
    	if( $_161 === FALSE) { return FALSE; }
    }


    /* Argument:
    :Concatenated |
    :DollarMarkedLookup |
    :QuotedString |
    :PHPLiteral */
    protected $match_Argument_typestack = array('Argument');
    function match_Argument ($stack = array()) {
    	$matchrule = "Argument"; $result = $this->construct($matchrule, $matchrule, null);
    	$_174 = NULL;
    	do {
    		$res_163 = $result;
    		$pos_163 = $this->pos;
    		$matcher = 'match_'.'Concatenated'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Concatenated" );
    			$_174 = TRUE; break;
    		}
    		$result = $res_163;
    		$this->pos = $pos_163;
    		$_172 = NULL;
    		do {
    			$res_165 = $result;
    			$pos_165 = $this->pos;
    			$matcher = 'match_'.'DollarMarkedLookup'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "DollarMarkedLookup" );
    				$_172 = TRUE; break;
    			}
    			$result = $res_165;
    			$this->pos = $pos_165;
    			$_170 = NULL;
    			do {
    				$res_167 = $result;
    				$pos_167 = $this->pos;
    				$matcher = 'match_'.'QuotedString'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "QuotedString" );
    					$_170 = TRUE; break;
    				}
    				$result = $res_167;
    				$this->pos = $pos_167;
    				$matcher = 'match_'.'PHPLiteral'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "PHPLiteral" );
    					$_170 = TRUE; break;
    				}
    				$result = $res_167;
    				$this->pos = $pos_167;
    				$_170 = FALSE; break;
    			}
    			while(0);
    			if( $_170 === TRUE ) { $_172 = TRUE; break; }
    			$result = $res_165;
    			$this->pos = $pos_165;
    			$_172 = FALSE; break;
    		}
    		while(0);
    		if( $_172 === TRUE ) { $_174 = TRUE; break; }
    		$result = $res_163;
    		$this->pos = $pos_163;
    		$_174 = FALSE; break;
    	}
    	while(0);
    	if( $_174 === TRUE ) { return $this->finalise($result); }
    	if( $_174 === FALSE) { return FALSE; }
    }



    
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

    /* ComparisonOperator: "!=" | "==" | ">=" | ">" | "<=" | "<" | "=" */
    protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
    function match_ComparisonOperator ($stack = array()) {
    	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
    	$_199 = NULL;
    	do {
    		$res_176 = $result;
    		$pos_176 = $this->pos;
    		if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$_199 = TRUE; break;
    		}
    		$result = $res_176;
    		$this->pos = $pos_176;
    		$_197 = NULL;
    		do {
    			$res_178 = $result;
    			$pos_178 = $this->pos;
    			if (( $subres = $this->literal( '==' ) ) !== FALSE) {
    				$result["text"] .= $subres;
    				$_197 = TRUE; break;
    			}
    			$result = $res_178;
    			$this->pos = $pos_178;
    			$_195 = NULL;
    			do {
    				$res_180 = $result;
    				$pos_180 = $this->pos;
    				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_195 = TRUE; break;
    				}
    				$result = $res_180;
    				$this->pos = $pos_180;
    				$_193 = NULL;
    				do {
    					$res_182 = $result;
    					$pos_182 = $this->pos;
    					if (substr($this->string,$this->pos,1) == '>') {
    						$this->pos += 1;
    						$result["text"] .= '>';
    						$_193 = TRUE; break;
    					}
    					$result = $res_182;
    					$this->pos = $pos_182;
    					$_191 = NULL;
    					do {
    						$res_184 = $result;
    						$pos_184 = $this->pos;
    						if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
    							$result["text"] .= $subres;
    							$_191 = TRUE; break;
    						}
    						$result = $res_184;
    						$this->pos = $pos_184;
    						$_189 = NULL;
    						do {
    							$res_186 = $result;
    							$pos_186 = $this->pos;
    							if (substr($this->string,$this->pos,1) == '<') {
    								$this->pos += 1;
    								$result["text"] .= '<';
    								$_189 = TRUE; break;
    							}
    							$result = $res_186;
    							$this->pos = $pos_186;
    							if (substr($this->string,$this->pos,1) == '=') {
    								$this->pos += 1;
    								$result["text"] .= '=';
    								$_189 = TRUE; break;
    							}
    							$result = $res_186;
    							$this->pos = $pos_186;
    							$_189 = FALSE; break;
    						}
    						while(0);
    						if( $_189 === TRUE ) { $_191 = TRUE; break; }
    						$result = $res_184;
    						$this->pos = $pos_184;
    						$_191 = FALSE; break;
    					}
    					while(0);
    					if( $_191 === TRUE ) { $_193 = TRUE; break; }
    					$result = $res_182;
    					$this->pos = $pos_182;
    					$_193 = FALSE; break;
    				}
    				while(0);
    				if( $_193 === TRUE ) { $_195 = TRUE; break; }
    				$result = $res_180;
    				$this->pos = $pos_180;
    				$_195 = FALSE; break;
    			}
    			while(0);
    			if( $_195 === TRUE ) { $_197 = TRUE; break; }
    			$result = $res_178;
    			$this->pos = $pos_178;
    			$_197 = FALSE; break;
    		}
    		while(0);
    		if( $_197 === TRUE ) { $_199 = TRUE; break; }
    		$result = $res_176;
    		$this->pos = $pos_176;
    		$_199 = FALSE; break;
    	}
    	while(0);
    	if( $_199 === TRUE ) { return $this->finalise($result); }
    	if( $_199 === FALSE) { return FALSE; }
    }


    /* Comparison: Argument < ComparisonOperator > Argument */
    protected $match_Comparison_typestack = array('Comparison');
    function match_Comparison ($stack = array()) {
    	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
    	$_206 = NULL;
    	do {
    		$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_206 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_206 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_206 = FALSE; break; }
    		$_206 = TRUE; break;
    	}
    	while(0);
    	if( $_206 === TRUE ) { return $this->finalise($result); }
    	if( $_206 === FALSE) { return FALSE; }
    }



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

    /* PresenceCheck: (Not:'not' <)? Argument */
    protected $match_PresenceCheck_typestack = array('PresenceCheck');
    function match_PresenceCheck ($stack = array()) {
    	$matchrule = "PresenceCheck"; $result = $this->construct($matchrule, $matchrule, null);
    	$_213 = NULL;
    	do {
    		$res_211 = $result;
    		$pos_211 = $this->pos;
    		$_210 = NULL;
    		do {
    			$stack[] = $result; $result = $this->construct( $matchrule, "Not" ); 
    			if (( $subres = $this->literal( 'not' ) ) !== FALSE) {
    				$result["text"] .= $subres;
    				$subres = $result; $result = array_pop($stack);
    				$this->store( $result, $subres, 'Not' );
    			}
    			else {
    				$result = array_pop($stack);
    				$_210 = FALSE; break;
    			}
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$_210 = TRUE; break;
    		}
    		while(0);
    		if( $_210 === FALSE) {
    			$result = $res_211;
    			$this->pos = $pos_211;
    			unset( $res_211 );
    			unset( $pos_211 );
    		}
    		$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_213 = FALSE; break; }
    		$_213 = TRUE; break;
    	}
    	while(0);
    	if( $_213 === TRUE ) { return $this->finalise($result); }
    	if( $_213 === FALSE) { return FALSE; }
    }



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

    /* IfArgumentPortion: Comparison | PresenceCheck */
    protected $match_IfArgumentPortion_typestack = array('IfArgumentPortion');
    function match_IfArgumentPortion ($stack = array()) {
    	$matchrule = "IfArgumentPortion"; $result = $this->construct($matchrule, $matchrule, null);
    	$_218 = NULL;
    	do {
    		$res_215 = $result;
    		$pos_215 = $this->pos;
    		$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_218 = TRUE; break;
    		}
    		$result = $res_215;
    		$this->pos = $pos_215;
    		$matcher = 'match_'.'PresenceCheck'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_218 = TRUE; break;
    		}
    		$result = $res_215;
    		$this->pos = $pos_215;
    		$_218 = FALSE; break;
    	}
    	while(0);
    	if( $_218 === TRUE ) { return $this->finalise($result); }
    	if( $_218 === FALSE) { return FALSE; }
    }



    public function IfArgumentPortion_STR(&$res, $sub)
    {
        $res['php'] = $sub['php'];
    }

    /* BooleanOperator: "||" | "&&" */
    protected $match_BooleanOperator_typestack = array('BooleanOperator');
    function match_BooleanOperator ($stack = array()) {
    	$matchrule = "BooleanOperator"; $result = $this->construct($matchrule, $matchrule, null);
    	$_223 = NULL;
    	do {
    		$res_220 = $result;
    		$pos_220 = $this->pos;
    		if (( $subres = $this->literal( '||' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$_223 = TRUE; break;
    		}
    		$result = $res_220;
    		$this->pos = $pos_220;
    		if (( $subres = $this->literal( '&&' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$_223 = TRUE; break;
    		}
    		$result = $res_220;
    		$this->pos = $pos_220;
    		$_223 = FALSE; break;
    	}
    	while(0);
    	if( $_223 === TRUE ) { return $this->finalise($result); }
    	if( $_223 === FALSE) { return FALSE; }
    }


    /* IfArgument: :IfArgumentPortion ( < :BooleanOperator < :IfArgumentPortion )* */
    protected $match_IfArgument_typestack = array('IfArgument');
    function match_IfArgument ($stack = array()) {
    	$matchrule = "IfArgument"; $result = $this->construct($matchrule, $matchrule, null);
    	$_232 = NULL;
    	do {
    		$matcher = 'match_'.'IfArgumentPortion'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "IfArgumentPortion" );
    		}
    		else { $_232 = FALSE; break; }
    		while (true) {
    			$res_231 = $result;
    			$pos_231 = $this->pos;
    			$_230 = NULL;
    			do {
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				$matcher = 'match_'.'BooleanOperator'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "BooleanOperator" );
    				}
    				else { $_230 = FALSE; break; }
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				$matcher = 'match_'.'IfArgumentPortion'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "IfArgumentPortion" );
    				}
    				else { $_230 = FALSE; break; }
    				$_230 = TRUE; break;
    			}
    			while(0);
    			if( $_230 === FALSE) {
    				$result = $res_231;
    				$this->pos = $pos_231;
    				unset( $res_231 );
    				unset( $pos_231 );
    				break;
    			}
    		}
    		$_232 = TRUE; break;
    	}
    	while(0);
    	if( $_232 === TRUE ) { return $this->finalise($result); }
    	if( $_232 === FALSE) { return FALSE; }
    }



    public function IfArgument_IfArgumentPortion(&$res, $sub)
    {
        $res['php'] .= $sub['php'];
    }

    public function IfArgument_BooleanOperator(&$res, $sub)
    {
        $res['php'] .= $sub['text'];
    }

    /* IfPart: '<%' < 'if' [ :IfArgument > '%>' Template:$TemplateMatcher? */
    protected $match_IfPart_typestack = array('IfPart');
    function match_IfPart ($stack = array()) {
    	$matchrule = "IfPart"; $result = $this->construct($matchrule, $matchrule, null);
    	$_242 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_242 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_242 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_242 = FALSE; break; }
    		$matcher = 'match_'.'IfArgument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "IfArgument" );
    		}
    		else { $_242 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_242 = FALSE; break; }
    		$res_241 = $result;
    		$pos_241 = $this->pos;
    		$matcher = 'match_'.$this->expression($result, $stack, 'TemplateMatcher'); $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Template" );
    		}
    		else {
    			$result = $res_241;
    			$this->pos = $pos_241;
    			unset( $res_241 );
    			unset( $pos_241 );
    		}
    		$_242 = TRUE; break;
    	}
    	while(0);
    	if( $_242 === TRUE ) { return $this->finalise($result); }
    	if( $_242 === FALSE) { return FALSE; }
    }


    /* ElseIfPart: '<%' < 'else_if' [ :IfArgument > '%>' Template:$TemplateMatcher? */
    protected $match_ElseIfPart_typestack = array('ElseIfPart');
    function match_ElseIfPart ($stack = array()) {
    	$matchrule = "ElseIfPart"; $result = $this->construct($matchrule, $matchrule, null);
    	$_252 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_252 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'else_if' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_252 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_252 = FALSE; break; }
    		$matcher = 'match_'.'IfArgument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "IfArgument" );
    		}
    		else { $_252 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_252 = FALSE; break; }
    		$res_251 = $result;
    		$pos_251 = $this->pos;
    		$matcher = 'match_'.$this->expression($result, $stack, 'TemplateMatcher'); $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Template" );
    		}
    		else {
    			$result = $res_251;
    			$this->pos = $pos_251;
    			unset( $res_251 );
    			unset( $pos_251 );
    		}
    		$_252 = TRUE; break;
    	}
    	while(0);
    	if( $_252 === TRUE ) { return $this->finalise($result); }
    	if( $_252 === FALSE) { return FALSE; }
    }


    /* ElsePart: '<%' < 'else' > '%>' Template:$TemplateMatcher? */
    protected $match_ElsePart_typestack = array('ElsePart');
    function match_ElsePart ($stack = array()) {
    	$matchrule = "ElsePart"; $result = $this->construct($matchrule, $matchrule, null);
    	$_260 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_260 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'else' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_260 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_260 = FALSE; break; }
    		$res_259 = $result;
    		$pos_259 = $this->pos;
    		$matcher = 'match_'.$this->expression($result, $stack, 'TemplateMatcher'); $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Template" );
    		}
    		else {
    			$result = $res_259;
    			$this->pos = $pos_259;
    			unset( $res_259 );
    			unset( $pos_259 );
    		}
    		$_260 = TRUE; break;
    	}
    	while(0);
    	if( $_260 === TRUE ) { return $this->finalise($result); }
    	if( $_260 === FALSE) { return FALSE; }
    }


    /* If: IfPart ElseIfPart* ElsePart? '<%' < 'end_if' > '%>' */
    protected $match_If_typestack = array('If');
    function match_If ($stack = array()) {
    	$matchrule = "If"; $result = $this->construct($matchrule, $matchrule, null);
    	$_270 = NULL;
    	do {
    		$matcher = 'match_'.'IfPart'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_270 = FALSE; break; }
    		while (true) {
    			$res_263 = $result;
    			$pos_263 = $this->pos;
    			$matcher = 'match_'.'ElseIfPart'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else {
    				$result = $res_263;
    				$this->pos = $pos_263;
    				unset( $res_263 );
    				unset( $pos_263 );
    				break;
    			}
    		}
    		$res_264 = $result;
    		$pos_264 = $this->pos;
    		$matcher = 'match_'.'ElsePart'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_264;
    			$this->pos = $pos_264;
    			unset( $res_264 );
    			unset( $pos_264 );
    		}
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_270 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'end_if' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_270 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_270 = FALSE; break; }
    		$_270 = TRUE; break;
    	}
    	while(0);
    	if( $_270 === TRUE ) { return $this->finalise($result); }
    	if( $_270 === FALSE) { return FALSE; }
    }



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

    /* Require: '<%' < 'require' [ Call:(Method:Word "(" < :CallArguments  > ")") > '%>' */
    protected $match_Require_typestack = array('Require');
    function match_Require ($stack = array()) {
    	$matchrule = "Require"; $result = $this->construct($matchrule, $matchrule, null);
    	$_286 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_286 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'require' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_286 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_286 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "Call" ); 
    		$_282 = NULL;
    		do {
    			$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "Method" );
    			}
    			else { $_282 = FALSE; break; }
    			if (substr($this->string,$this->pos,1) == '(') {
    				$this->pos += 1;
    				$result["text"] .= '(';
    			}
    			else { $_282 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$matcher = 'match_'.'CallArguments'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "CallArguments" );
    			}
    			else { $_282 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			if (substr($this->string,$this->pos,1) == ')') {
    				$this->pos += 1;
    				$result["text"] .= ')';
    			}
    			else { $_282 = FALSE; break; }
    			$_282 = TRUE; break;
    		}
    		while(0);
    		if( $_282 === TRUE ) {
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'Call' );
    		}
    		if( $_282 === FALSE) {
    			$result = array_pop($stack);
    			$_286 = FALSE; break;
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_286 = FALSE; break; }
    		$_286 = TRUE; break;
    	}
    	while(0);
    	if( $_286 === TRUE ) { return $this->finalise($result); }
    	if( $_286 === FALSE) { return FALSE; }
    }



    public function Require_Call(&$res, $sub)
    {
        $res['php'] = "Requirements::".$sub['Method']['text'].'('.$sub['CallArguments']['php'].');';
    }

    /* CacheBlockArgument:
   !( "if " | "unless " )
    (
        :DollarMarkedLookup |
        :QuotedString |
        :Lookup
    ) */
    protected $match_CacheBlockArgument_typestack = array('CacheBlockArgument');
    function match_CacheBlockArgument ($stack = array()) {
    	$matchrule = "CacheBlockArgument"; $result = $this->construct($matchrule, $matchrule, null);
    	$_306 = NULL;
    	do {
    		$res_294 = $result;
    		$pos_294 = $this->pos;
    		$_293 = NULL;
    		do {
    			$_291 = NULL;
    			do {
    				$res_288 = $result;
    				$pos_288 = $this->pos;
    				if (( $subres = $this->literal( 'if ' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_291 = TRUE; break;
    				}
    				$result = $res_288;
    				$this->pos = $pos_288;
    				if (( $subres = $this->literal( 'unless ' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_291 = TRUE; break;
    				}
    				$result = $res_288;
    				$this->pos = $pos_288;
    				$_291 = FALSE; break;
    			}
    			while(0);
    			if( $_291 === FALSE) { $_293 = FALSE; break; }
    			$_293 = TRUE; break;
    		}
    		while(0);
    		if( $_293 === TRUE ) {
    			$result = $res_294;
    			$this->pos = $pos_294;
    			$_306 = FALSE; break;
    		}
    		if( $_293 === FALSE) {
    			$result = $res_294;
    			$this->pos = $pos_294;
    		}
    		$_304 = NULL;
    		do {
    			$_302 = NULL;
    			do {
    				$res_295 = $result;
    				$pos_295 = $this->pos;
    				$matcher = 'match_'.'DollarMarkedLookup'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "DollarMarkedLookup" );
    					$_302 = TRUE; break;
    				}
    				$result = $res_295;
    				$this->pos = $pos_295;
    				$_300 = NULL;
    				do {
    					$res_297 = $result;
    					$pos_297 = $this->pos;
    					$matcher = 'match_'.'QuotedString'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres, "QuotedString" );
    						$_300 = TRUE; break;
    					}
    					$result = $res_297;
    					$this->pos = $pos_297;
    					$matcher = 'match_'.'Lookup'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres, "Lookup" );
    						$_300 = TRUE; break;
    					}
    					$result = $res_297;
    					$this->pos = $pos_297;
    					$_300 = FALSE; break;
    				}
    				while(0);
    				if( $_300 === TRUE ) { $_302 = TRUE; break; }
    				$result = $res_295;
    				$this->pos = $pos_295;
    				$_302 = FALSE; break;
    			}
    			while(0);
    			if( $_302 === FALSE) { $_304 = FALSE; break; }
    			$_304 = TRUE; break;
    		}
    		while(0);
    		if( $_304 === FALSE) { $_306 = FALSE; break; }
    		$_306 = TRUE; break;
    	}
    	while(0);
    	if( $_306 === TRUE ) { return $this->finalise($result); }
    	if( $_306 === FALSE) { return FALSE; }
    }



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

    /* CacheBlockArguments: CacheBlockArgument ( < "," < CacheBlockArgument )* */
    protected $match_CacheBlockArguments_typestack = array('CacheBlockArguments');
    function match_CacheBlockArguments ($stack = array()) {
    	$matchrule = "CacheBlockArguments"; $result = $this->construct($matchrule, $matchrule, null);
    	$_315 = NULL;
    	do {
    		$matcher = 'match_'.'CacheBlockArgument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_315 = FALSE; break; }
    		while (true) {
    			$res_314 = $result;
    			$pos_314 = $this->pos;
    			$_313 = NULL;
    			do {
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				if (substr($this->string,$this->pos,1) == ',') {
    					$this->pos += 1;
    					$result["text"] .= ',';
    				}
    				else { $_313 = FALSE; break; }
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				$matcher = 'match_'.'CacheBlockArgument'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres );
    				}
    				else { $_313 = FALSE; break; }
    				$_313 = TRUE; break;
    			}
    			while(0);
    			if( $_313 === FALSE) {
    				$result = $res_314;
    				$this->pos = $pos_314;
    				unset( $res_314 );
    				unset( $pos_314 );
    				break;
    			}
    		}
    		$_315 = TRUE; break;
    	}
    	while(0);
    	if( $_315 === TRUE ) { return $this->finalise($result); }
    	if( $_315 === FALSE) { return FALSE; }
    }



    public function CacheBlockArguments_CacheBlockArgument(&$res, $sub)
    {
        if (!empty($res['php'])) {
            $res['php'] .= ".'_'.";
        } else {
            $res['php'] = '';
        }
        
        $res['php'] .= str_replace('$$FINAL', 'XML_val', $sub['php']);
    }

    /* CacheBlockTemplate: (Comment | Translate | If | Require |    Include | ClosedBlock | OpenBlock |
    MalformedBlock | Injection | Text)+ */
    protected $match_CacheBlockTemplate_typestack = array('CacheBlockTemplate','Template');
    function match_CacheBlockTemplate ($stack = array()) {
    	$matchrule = "CacheBlockTemplate"; $result = $this->construct($matchrule, $matchrule, array('TemplateMatcher' => 'CacheRestrictedTemplate'));
    	$count = 0;
    	while (true) {
    		$res_355 = $result;
    		$pos_355 = $this->pos;
    		$_354 = NULL;
    		do {
    			$_352 = NULL;
    			do {
    				$res_317 = $result;
    				$pos_317 = $this->pos;
    				$matcher = 'match_'.'Comment'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres );
    					$_352 = TRUE; break;
    				}
    				$result = $res_317;
    				$this->pos = $pos_317;
    				$_350 = NULL;
    				do {
    					$res_319 = $result;
    					$pos_319 = $this->pos;
    					$matcher = 'match_'.'Translate'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    						$_350 = TRUE; break;
    					}
    					$result = $res_319;
    					$this->pos = $pos_319;
    					$_348 = NULL;
    					do {
    						$res_321 = $result;
    						$pos_321 = $this->pos;
    						$matcher = 'match_'.'If'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres );
    							$_348 = TRUE; break;
    						}
    						$result = $res_321;
    						$this->pos = $pos_321;
    						$_346 = NULL;
    						do {
    							$res_323 = $result;
    							$pos_323 = $this->pos;
    							$matcher = 'match_'.'Require'; $key = $matcher; $pos = $this->pos;
    							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    							if ($subres !== FALSE) {
    								$this->store( $result, $subres );
    								$_346 = TRUE; break;
    							}
    							$result = $res_323;
    							$this->pos = $pos_323;
    							$_344 = NULL;
    							do {
    								$res_325 = $result;
    								$pos_325 = $this->pos;
    								$matcher = 'match_'.'Include'; $key = $matcher; $pos = $this->pos;
    								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    								if ($subres !== FALSE) {
    									$this->store( $result, $subres );
    									$_344 = TRUE; break;
    								}
    								$result = $res_325;
    								$this->pos = $pos_325;
    								$_342 = NULL;
    								do {
    									$res_327 = $result;
    									$pos_327 = $this->pos;
    									$matcher = 'match_'.'ClosedBlock'; $key = $matcher; $pos = $this->pos;
    									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    									if ($subres !== FALSE) {
    										$this->store( $result, $subres );
    										$_342 = TRUE; break;
    									}
    									$result = $res_327;
    									$this->pos = $pos_327;
    									$_340 = NULL;
    									do {
    										$res_329 = $result;
    										$pos_329 = $this->pos;
    										$matcher = 'match_'.'OpenBlock'; $key = $matcher; $pos = $this->pos;
    										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    										if ($subres !== FALSE) {
    											$this->store( $result, $subres );
    											$_340 = TRUE; break;
    										}
    										$result = $res_329;
    										$this->pos = $pos_329;
    										$_338 = NULL;
    										do {
    											$res_331 = $result;
    											$pos_331 = $this->pos;
    											$matcher = 'match_'.'MalformedBlock'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres );
    												$_338 = TRUE; break;
    											}
    											$result = $res_331;
    											$this->pos = $pos_331;
    											$_336 = NULL;
    											do {
    												$res_333 = $result;
    												$pos_333 = $this->pos;
    												$matcher = 'match_'.'Injection'; $key = $matcher; $pos = $this->pos;
    												$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    												if ($subres !== FALSE) {
    													$this->store( $result, $subres );
    													$_336 = TRUE; break;
    												}
    												$result = $res_333;
    												$this->pos = $pos_333;
    												$matcher = 'match_'.'Text'; $key = $matcher; $pos = $this->pos;
    												$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    												if ($subres !== FALSE) {
    													$this->store( $result, $subres );
    													$_336 = TRUE; break;
    												}
    												$result = $res_333;
    												$this->pos = $pos_333;
    												$_336 = FALSE; break;
    											}
    											while(0);
    											if( $_336 === TRUE ) { $_338 = TRUE; break; }
    											$result = $res_331;
    											$this->pos = $pos_331;
    											$_338 = FALSE; break;
    										}
    										while(0);
    										if( $_338 === TRUE ) { $_340 = TRUE; break; }
    										$result = $res_329;
    										$this->pos = $pos_329;
    										$_340 = FALSE; break;
    									}
    									while(0);
    									if( $_340 === TRUE ) { $_342 = TRUE; break; }
    									$result = $res_327;
    									$this->pos = $pos_327;
    									$_342 = FALSE; break;
    								}
    								while(0);
    								if( $_342 === TRUE ) { $_344 = TRUE; break; }
    								$result = $res_325;
    								$this->pos = $pos_325;
    								$_344 = FALSE; break;
    							}
    							while(0);
    							if( $_344 === TRUE ) { $_346 = TRUE; break; }
    							$result = $res_323;
    							$this->pos = $pos_323;
    							$_346 = FALSE; break;
    						}
    						while(0);
    						if( $_346 === TRUE ) { $_348 = TRUE; break; }
    						$result = $res_321;
    						$this->pos = $pos_321;
    						$_348 = FALSE; break;
    					}
    					while(0);
    					if( $_348 === TRUE ) { $_350 = TRUE; break; }
    					$result = $res_319;
    					$this->pos = $pos_319;
    					$_350 = FALSE; break;
    				}
    				while(0);
    				if( $_350 === TRUE ) { $_352 = TRUE; break; }
    				$result = $res_317;
    				$this->pos = $pos_317;
    				$_352 = FALSE; break;
    			}
    			while(0);
    			if( $_352 === FALSE) { $_354 = FALSE; break; }
    			$_354 = TRUE; break;
    		}
    		while(0);
    		if( $_354 === FALSE) {
    			$result = $res_355;
    			$this->pos = $pos_355;
    			unset( $res_355 );
    			unset( $pos_355 );
    			break;
    		}
    		$count += 1;
    	}
    	if ($count > 0) { return $this->finalise($result); }
    	else { return FALSE; }
    }



        
    /* UncachedBlock:
    '<%' < "uncached" < CacheBlockArguments? ( < Conditional:("if"|"unless") > Condition:IfArgument )? > '%>'
        Template:$TemplateMatcher?
        '<%' < 'end_' ("uncached"|"cached"|"cacheblock") > '%>' */
    protected $match_UncachedBlock_typestack = array('UncachedBlock');
    function match_UncachedBlock ($stack = array()) {
    	$matchrule = "UncachedBlock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_392 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_392 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'uncached' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_392 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_360 = $result;
    		$pos_360 = $this->pos;
    		$matcher = 'match_'.'CacheBlockArguments'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_360;
    			$this->pos = $pos_360;
    			unset( $res_360 );
    			unset( $pos_360 );
    		}
    		$res_372 = $result;
    		$pos_372 = $this->pos;
    		$_371 = NULL;
    		do {
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$stack[] = $result; $result = $this->construct( $matchrule, "Conditional" ); 
    			$_367 = NULL;
    			do {
    				$_365 = NULL;
    				do {
    					$res_362 = $result;
    					$pos_362 = $this->pos;
    					if (( $subres = $this->literal( 'if' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_365 = TRUE; break;
    					}
    					$result = $res_362;
    					$this->pos = $pos_362;
    					if (( $subres = $this->literal( 'unless' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_365 = TRUE; break;
    					}
    					$result = $res_362;
    					$this->pos = $pos_362;
    					$_365 = FALSE; break;
    				}
    				while(0);
    				if( $_365 === FALSE) { $_367 = FALSE; break; }
    				$_367 = TRUE; break;
    			}
    			while(0);
    			if( $_367 === TRUE ) {
    				$subres = $result; $result = array_pop($stack);
    				$this->store( $result, $subres, 'Conditional' );
    			}
    			if( $_367 === FALSE) {
    				$result = array_pop($stack);
    				$_371 = FALSE; break;
    			}
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$matcher = 'match_'.'IfArgument'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "Condition" );
    			}
    			else { $_371 = FALSE; break; }
    			$_371 = TRUE; break;
    		}
    		while(0);
    		if( $_371 === FALSE) {
    			$result = $res_372;
    			$this->pos = $pos_372;
    			unset( $res_372 );
    			unset( $pos_372 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_392 = FALSE; break; }
    		$res_375 = $result;
    		$pos_375 = $this->pos;
    		$matcher = 'match_'.$this->expression($result, $stack, 'TemplateMatcher'); $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Template" );
    		}
    		else {
    			$result = $res_375;
    			$this->pos = $pos_375;
    			unset( $res_375 );
    			unset( $pos_375 );
    		}
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_392 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'end_' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_392 = FALSE; break; }
    		$_388 = NULL;
    		do {
    			$_386 = NULL;
    			do {
    				$res_379 = $result;
    				$pos_379 = $this->pos;
    				if (( $subres = $this->literal( 'uncached' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_386 = TRUE; break;
    				}
    				$result = $res_379;
    				$this->pos = $pos_379;
    				$_384 = NULL;
    				do {
    					$res_381 = $result;
    					$pos_381 = $this->pos;
    					if (( $subres = $this->literal( 'cached' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_384 = TRUE; break;
    					}
    					$result = $res_381;
    					$this->pos = $pos_381;
    					if (( $subres = $this->literal( 'cacheblock' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_384 = TRUE; break;
    					}
    					$result = $res_381;
    					$this->pos = $pos_381;
    					$_384 = FALSE; break;
    				}
    				while(0);
    				if( $_384 === TRUE ) { $_386 = TRUE; break; }
    				$result = $res_379;
    				$this->pos = $pos_379;
    				$_386 = FALSE; break;
    			}
    			while(0);
    			if( $_386 === FALSE) { $_388 = FALSE; break; }
    			$_388 = TRUE; break;
    		}
    		while(0);
    		if( $_388 === FALSE) { $_392 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_392 = FALSE; break; }
    		$_392 = TRUE; break;
    	}
    	while(0);
    	if( $_392 === TRUE ) { return $this->finalise($result); }
    	if( $_392 === FALSE) { return FALSE; }
    }



    public function UncachedBlock_Template(&$res, $sub)
    {
        $res['php'] = $sub['php'];
    }

    /* CacheRestrictedTemplate: (Comment | Translate | If | Require | CacheBlock | UncachedBlock | Include | ClosedBlock | OpenBlock |
    MalformedBlock | Injection | Text)+ */
    protected $match_CacheRestrictedTemplate_typestack = array('CacheRestrictedTemplate','Template');
    function match_CacheRestrictedTemplate ($stack = array()) {
    	$matchrule = "CacheRestrictedTemplate"; $result = $this->construct($matchrule, $matchrule, null);
    	$count = 0;
    	while (true) {
    		$res_440 = $result;
    		$pos_440 = $this->pos;
    		$_439 = NULL;
    		do {
    			$_437 = NULL;
    			do {
    				$res_394 = $result;
    				$pos_394 = $this->pos;
    				$matcher = 'match_'.'Comment'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres );
    					$_437 = TRUE; break;
    				}
    				$result = $res_394;
    				$this->pos = $pos_394;
    				$_435 = NULL;
    				do {
    					$res_396 = $result;
    					$pos_396 = $this->pos;
    					$matcher = 'match_'.'Translate'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    						$_435 = TRUE; break;
    					}
    					$result = $res_396;
    					$this->pos = $pos_396;
    					$_433 = NULL;
    					do {
    						$res_398 = $result;
    						$pos_398 = $this->pos;
    						$matcher = 'match_'.'If'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres );
    							$_433 = TRUE; break;
    						}
    						$result = $res_398;
    						$this->pos = $pos_398;
    						$_431 = NULL;
    						do {
    							$res_400 = $result;
    							$pos_400 = $this->pos;
    							$matcher = 'match_'.'Require'; $key = $matcher; $pos = $this->pos;
    							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    							if ($subres !== FALSE) {
    								$this->store( $result, $subres );
    								$_431 = TRUE; break;
    							}
    							$result = $res_400;
    							$this->pos = $pos_400;
    							$_429 = NULL;
    							do {
    								$res_402 = $result;
    								$pos_402 = $this->pos;
    								$matcher = 'match_'.'CacheBlock'; $key = $matcher; $pos = $this->pos;
    								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    								if ($subres !== FALSE) {
    									$this->store( $result, $subres );
    									$_429 = TRUE; break;
    								}
    								$result = $res_402;
    								$this->pos = $pos_402;
    								$_427 = NULL;
    								do {
    									$res_404 = $result;
    									$pos_404 = $this->pos;
    									$matcher = 'match_'.'UncachedBlock'; $key = $matcher; $pos = $this->pos;
    									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    									if ($subres !== FALSE) {
    										$this->store( $result, $subres );
    										$_427 = TRUE; break;
    									}
    									$result = $res_404;
    									$this->pos = $pos_404;
    									$_425 = NULL;
    									do {
    										$res_406 = $result;
    										$pos_406 = $this->pos;
    										$matcher = 'match_'.'Include'; $key = $matcher; $pos = $this->pos;
    										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    										if ($subres !== FALSE) {
    											$this->store( $result, $subres );
    											$_425 = TRUE; break;
    										}
    										$result = $res_406;
    										$this->pos = $pos_406;
    										$_423 = NULL;
    										do {
    											$res_408 = $result;
    											$pos_408 = $this->pos;
    											$matcher = 'match_'.'ClosedBlock'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres );
    												$_423 = TRUE; break;
    											}
    											$result = $res_408;
    											$this->pos = $pos_408;
    											$_421 = NULL;
    											do {
    												$res_410 = $result;
    												$pos_410 = $this->pos;
    												$matcher = 'match_'.'OpenBlock'; $key = $matcher; $pos = $this->pos;
    												$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    												if ($subres !== FALSE) {
    													$this->store( $result, $subres );
    													$_421 = TRUE; break;
    												}
    												$result = $res_410;
    												$this->pos = $pos_410;
    												$_419 = NULL;
    												do {
    													$res_412 = $result;
    													$pos_412 = $this->pos;
    													$matcher = 'match_'.'MalformedBlock'; $key = $matcher; $pos = $this->pos;
    													$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    													if ($subres !== FALSE) {
    														$this->store( $result, $subres );
    														$_419 = TRUE; break;
    													}
    													$result = $res_412;
    													$this->pos = $pos_412;
    													$_417 = NULL;
    													do {
    														$res_414 = $result;
    														$pos_414 = $this->pos;
    														$matcher = 'match_'.'Injection'; $key = $matcher; $pos = $this->pos;
    														$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    														if ($subres !== FALSE) {
    															$this->store( $result, $subres );
    															$_417 = TRUE; break;
    														}
    														$result = $res_414;
    														$this->pos = $pos_414;
    														$matcher = 'match_'.'Text'; $key = $matcher; $pos = $this->pos;
    														$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    														if ($subres !== FALSE) {
    															$this->store( $result, $subres );
    															$_417 = TRUE; break;
    														}
    														$result = $res_414;
    														$this->pos = $pos_414;
    														$_417 = FALSE; break;
    													}
    													while(0);
    													if( $_417 === TRUE ) { $_419 = TRUE; break; }
    													$result = $res_412;
    													$this->pos = $pos_412;
    													$_419 = FALSE; break;
    												}
    												while(0);
    												if( $_419 === TRUE ) { $_421 = TRUE; break; }
    												$result = $res_410;
    												$this->pos = $pos_410;
    												$_421 = FALSE; break;
    											}
    											while(0);
    											if( $_421 === TRUE ) { $_423 = TRUE; break; }
    											$result = $res_408;
    											$this->pos = $pos_408;
    											$_423 = FALSE; break;
    										}
    										while(0);
    										if( $_423 === TRUE ) { $_425 = TRUE; break; }
    										$result = $res_406;
    										$this->pos = $pos_406;
    										$_425 = FALSE; break;
    									}
    									while(0);
    									if( $_425 === TRUE ) { $_427 = TRUE; break; }
    									$result = $res_404;
    									$this->pos = $pos_404;
    									$_427 = FALSE; break;
    								}
    								while(0);
    								if( $_427 === TRUE ) { $_429 = TRUE; break; }
    								$result = $res_402;
    								$this->pos = $pos_402;
    								$_429 = FALSE; break;
    							}
    							while(0);
    							if( $_429 === TRUE ) { $_431 = TRUE; break; }
    							$result = $res_400;
    							$this->pos = $pos_400;
    							$_431 = FALSE; break;
    						}
    						while(0);
    						if( $_431 === TRUE ) { $_433 = TRUE; break; }
    						$result = $res_398;
    						$this->pos = $pos_398;
    						$_433 = FALSE; break;
    					}
    					while(0);
    					if( $_433 === TRUE ) { $_435 = TRUE; break; }
    					$result = $res_396;
    					$this->pos = $pos_396;
    					$_435 = FALSE; break;
    				}
    				while(0);
    				if( $_435 === TRUE ) { $_437 = TRUE; break; }
    				$result = $res_394;
    				$this->pos = $pos_394;
    				$_437 = FALSE; break;
    			}
    			while(0);
    			if( $_437 === FALSE) { $_439 = FALSE; break; }
    			$_439 = TRUE; break;
    		}
    		while(0);
    		if( $_439 === FALSE) {
    			$result = $res_440;
    			$this->pos = $pos_440;
    			unset( $res_440 );
    			unset( $pos_440 );
    			break;
    		}
    		$count += 1;
    	}
    	if ($count > 0) { return $this->finalise($result); }
    	else { return FALSE; }
    }



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

    /* CacheBlock:
    '<%' < CacheTag:("cached"|"cacheblock") < (CacheBlockArguments)? ( < Conditional:("if"|"unless") >
    Condition:IfArgument )? > '%>'
        (CacheBlock | UncachedBlock | CacheBlockTemplate)*
    '<%' < 'end_' ("cached"|"uncached"|"cacheblock") > '%>' */
    protected $match_CacheBlock_typestack = array('CacheBlock');
    function match_CacheBlock ($stack = array()) {
    	$matchrule = "CacheBlock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_495 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_495 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "CacheTag" ); 
    		$_448 = NULL;
    		do {
    			$_446 = NULL;
    			do {
    				$res_443 = $result;
    				$pos_443 = $this->pos;
    				if (( $subres = $this->literal( 'cached' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_446 = TRUE; break;
    				}
    				$result = $res_443;
    				$this->pos = $pos_443;
    				if (( $subres = $this->literal( 'cacheblock' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_446 = TRUE; break;
    				}
    				$result = $res_443;
    				$this->pos = $pos_443;
    				$_446 = FALSE; break;
    			}
    			while(0);
    			if( $_446 === FALSE) { $_448 = FALSE; break; }
    			$_448 = TRUE; break;
    		}
    		while(0);
    		if( $_448 === TRUE ) {
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'CacheTag' );
    		}
    		if( $_448 === FALSE) {
    			$result = array_pop($stack);
    			$_495 = FALSE; break;
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_453 = $result;
    		$pos_453 = $this->pos;
    		$_452 = NULL;
    		do {
    			$matcher = 'match_'.'CacheBlockArguments'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else { $_452 = FALSE; break; }
    			$_452 = TRUE; break;
    		}
    		while(0);
    		if( $_452 === FALSE) {
    			$result = $res_453;
    			$this->pos = $pos_453;
    			unset( $res_453 );
    			unset( $pos_453 );
    		}
    		$res_465 = $result;
    		$pos_465 = $this->pos;
    		$_464 = NULL;
    		do {
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$stack[] = $result; $result = $this->construct( $matchrule, "Conditional" ); 
    			$_460 = NULL;
    			do {
    				$_458 = NULL;
    				do {
    					$res_455 = $result;
    					$pos_455 = $this->pos;
    					if (( $subres = $this->literal( 'if' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_458 = TRUE; break;
    					}
    					$result = $res_455;
    					$this->pos = $pos_455;
    					if (( $subres = $this->literal( 'unless' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_458 = TRUE; break;
    					}
    					$result = $res_455;
    					$this->pos = $pos_455;
    					$_458 = FALSE; break;
    				}
    				while(0);
    				if( $_458 === FALSE) { $_460 = FALSE; break; }
    				$_460 = TRUE; break;
    			}
    			while(0);
    			if( $_460 === TRUE ) {
    				$subres = $result; $result = array_pop($stack);
    				$this->store( $result, $subres, 'Conditional' );
    			}
    			if( $_460 === FALSE) {
    				$result = array_pop($stack);
    				$_464 = FALSE; break;
    			}
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			$matcher = 'match_'.'IfArgument'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "Condition" );
    			}
    			else { $_464 = FALSE; break; }
    			$_464 = TRUE; break;
    		}
    		while(0);
    		if( $_464 === FALSE) {
    			$result = $res_465;
    			$this->pos = $pos_465;
    			unset( $res_465 );
    			unset( $pos_465 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_495 = FALSE; break; }
    		while (true) {
    			$res_478 = $result;
    			$pos_478 = $this->pos;
    			$_477 = NULL;
    			do {
    				$_475 = NULL;
    				do {
    					$res_468 = $result;
    					$pos_468 = $this->pos;
    					$matcher = 'match_'.'CacheBlock'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    						$_475 = TRUE; break;
    					}
    					$result = $res_468;
    					$this->pos = $pos_468;
    					$_473 = NULL;
    					do {
    						$res_470 = $result;
    						$pos_470 = $this->pos;
    						$matcher = 'match_'.'UncachedBlock'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres );
    							$_473 = TRUE; break;
    						}
    						$result = $res_470;
    						$this->pos = $pos_470;
    						$matcher = 'match_'.'CacheBlockTemplate'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres );
    							$_473 = TRUE; break;
    						}
    						$result = $res_470;
    						$this->pos = $pos_470;
    						$_473 = FALSE; break;
    					}
    					while(0);
    					if( $_473 === TRUE ) { $_475 = TRUE; break; }
    					$result = $res_468;
    					$this->pos = $pos_468;
    					$_475 = FALSE; break;
    				}
    				while(0);
    				if( $_475 === FALSE) { $_477 = FALSE; break; }
    				$_477 = TRUE; break;
    			}
    			while(0);
    			if( $_477 === FALSE) {
    				$result = $res_478;
    				$this->pos = $pos_478;
    				unset( $res_478 );
    				unset( $pos_478 );
    				break;
    			}
    		}
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_495 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'end_' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_495 = FALSE; break; }
    		$_491 = NULL;
    		do {
    			$_489 = NULL;
    			do {
    				$res_482 = $result;
    				$pos_482 = $this->pos;
    				if (( $subres = $this->literal( 'cached' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_489 = TRUE; break;
    				}
    				$result = $res_482;
    				$this->pos = $pos_482;
    				$_487 = NULL;
    				do {
    					$res_484 = $result;
    					$pos_484 = $this->pos;
    					if (( $subres = $this->literal( 'uncached' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_487 = TRUE; break;
    					}
    					$result = $res_484;
    					$this->pos = $pos_484;
    					if (( $subres = $this->literal( 'cacheblock' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_487 = TRUE; break;
    					}
    					$result = $res_484;
    					$this->pos = $pos_484;
    					$_487 = FALSE; break;
    				}
    				while(0);
    				if( $_487 === TRUE ) { $_489 = TRUE; break; }
    				$result = $res_482;
    				$this->pos = $pos_482;
    				$_489 = FALSE; break;
    			}
    			while(0);
    			if( $_489 === FALSE) { $_491 = FALSE; break; }
    			$_491 = TRUE; break;
    		}
    		while(0);
    		if( $_491 === FALSE) { $_495 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_495 = FALSE; break; }
    		$_495 = TRUE; break;
    	}
    	while(0);
    	if( $_495 === TRUE ) { return $this->finalise($result); }
    	if( $_495 === FALSE) { return FALSE; }
    }



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

    /* NamedArgument: Name:Word "=" Value:Argument */
    protected $match_NamedArgument_typestack = array('NamedArgument');
    function match_NamedArgument ($stack = array()) {
    	$matchrule = "NamedArgument"; $result = $this->construct($matchrule, $matchrule, null);
    	$_500 = NULL;
    	do {
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Name" );
    		}
    		else { $_500 = FALSE; break; }
    		if (substr($this->string,$this->pos,1) == '=') {
    			$this->pos += 1;
    			$result["text"] .= '=';
    		}
    		else { $_500 = FALSE; break; }
    		$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Value" );
    		}
    		else { $_500 = FALSE; break; }
    		$_500 = TRUE; break;
    	}
    	while(0);
    	if( $_500 === TRUE ) { return $this->finalise($result); }
    	if( $_500 === FALSE) { return FALSE; }
    }



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

    /* Include: "<%" < "include" < Template:Word < (NamedArgument ( < "," < NamedArgument )*)? > "%>" */
    protected $match_Include_typestack = array('Include');
    function match_Include ($stack = array()) {
    	$matchrule = "Include"; $result = $this->construct($matchrule, $matchrule, null);
    	$_519 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_519 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'include' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_519 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Template" );
    		}
    		else { $_519 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_516 = $result;
    		$pos_516 = $this->pos;
    		$_515 = NULL;
    		do {
    			$matcher = 'match_'.'NamedArgument'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres );
    			}
    			else { $_515 = FALSE; break; }
    			while (true) {
    				$res_514 = $result;
    				$pos_514 = $this->pos;
    				$_513 = NULL;
    				do {
    					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    					if (substr($this->string,$this->pos,1) == ',') {
    						$this->pos += 1;
    						$result["text"] .= ',';
    					}
    					else { $_513 = FALSE; break; }
    					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    					$matcher = 'match_'.'NamedArgument'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    					}
    					else { $_513 = FALSE; break; }
    					$_513 = TRUE; break;
    				}
    				while(0);
    				if( $_513 === FALSE) {
    					$result = $res_514;
    					$this->pos = $pos_514;
    					unset( $res_514 );
    					unset( $pos_514 );
    					break;
    				}
    			}
    			$_515 = TRUE; break;
    		}
    		while(0);
    		if( $_515 === FALSE) {
    			$result = $res_516;
    			$this->pos = $pos_516;
    			unset( $res_516 );
    			unset( $pos_516 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_519 = FALSE; break; }
    		$_519 = TRUE; break;
    	}
    	while(0);
    	if( $_519 === TRUE ) { return $this->finalise($result); }
    	if( $_519 === FALSE) { return FALSE; }
    }



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

    /* BlockArguments: :Argument ( < "," < :Argument)* */
    protected $match_BlockArguments_typestack = array('BlockArguments');
    function match_BlockArguments ($stack = array()) {
    	$matchrule = "BlockArguments"; $result = $this->construct($matchrule, $matchrule, null);
    	$_528 = NULL;
    	do {
    		$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Argument" );
    		}
    		else { $_528 = FALSE; break; }
    		while (true) {
    			$res_527 = $result;
    			$pos_527 = $this->pos;
    			$_526 = NULL;
    			do {
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				if (substr($this->string,$this->pos,1) == ',') {
    					$this->pos += 1;
    					$result["text"] .= ',';
    				}
    				else { $_526 = FALSE; break; }
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				$matcher = 'match_'.'Argument'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "Argument" );
    				}
    				else { $_526 = FALSE; break; }
    				$_526 = TRUE; break;
    			}
    			while(0);
    			if( $_526 === FALSE) {
    				$result = $res_527;
    				$this->pos = $pos_527;
    				unset( $res_527 );
    				unset( $pos_527 );
    				break;
    			}
    		}
    		$_528 = TRUE; break;
    	}
    	while(0);
    	if( $_528 === TRUE ) { return $this->finalise($result); }
    	if( $_528 === FALSE) { return FALSE; }
    }


    /* NotBlockTag: "end_" | (("if" | "else_if" | "else" | "require" | "cached" | "uncached" | "cacheblock" | "include")]) */
    protected $match_NotBlockTag_typestack = array('NotBlockTag');
    function match_NotBlockTag ($stack = array()) {
    	$matchrule = "NotBlockTag"; $result = $this->construct($matchrule, $matchrule, null);
    	$_566 = NULL;
    	do {
    		$res_530 = $result;
    		$pos_530 = $this->pos;
    		if (( $subres = $this->literal( 'end_' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$_566 = TRUE; break;
    		}
    		$result = $res_530;
    		$this->pos = $pos_530;
    		$_564 = NULL;
    		do {
    			$_561 = NULL;
    			do {
    				$_559 = NULL;
    				do {
    					$res_532 = $result;
    					$pos_532 = $this->pos;
    					if (( $subres = $this->literal( 'if' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_559 = TRUE; break;
    					}
    					$result = $res_532;
    					$this->pos = $pos_532;
    					$_557 = NULL;
    					do {
    						$res_534 = $result;
    						$pos_534 = $this->pos;
    						if (( $subres = $this->literal( 'else_if' ) ) !== FALSE) {
    							$result["text"] .= $subres;
    							$_557 = TRUE; break;
    						}
    						$result = $res_534;
    						$this->pos = $pos_534;
    						$_555 = NULL;
    						do {
    							$res_536 = $result;
    							$pos_536 = $this->pos;
    							if (( $subres = $this->literal( 'else' ) ) !== FALSE) {
    								$result["text"] .= $subres;
    								$_555 = TRUE; break;
    							}
    							$result = $res_536;
    							$this->pos = $pos_536;
    							$_553 = NULL;
    							do {
    								$res_538 = $result;
    								$pos_538 = $this->pos;
    								if (( $subres = $this->literal( 'require' ) ) !== FALSE) {
    									$result["text"] .= $subres;
    									$_553 = TRUE; break;
    								}
    								$result = $res_538;
    								$this->pos = $pos_538;
    								$_551 = NULL;
    								do {
    									$res_540 = $result;
    									$pos_540 = $this->pos;
    									if (( $subres = $this->literal( 'cached' ) ) !== FALSE) {
    										$result["text"] .= $subres;
    										$_551 = TRUE; break;
    									}
    									$result = $res_540;
    									$this->pos = $pos_540;
    									$_549 = NULL;
    									do {
    										$res_542 = $result;
    										$pos_542 = $this->pos;
    										if (( $subres = $this->literal( 'uncached' ) ) !== FALSE) {
    											$result["text"] .= $subres;
    											$_549 = TRUE; break;
    										}
    										$result = $res_542;
    										$this->pos = $pos_542;
    										$_547 = NULL;
    										do {
    											$res_544 = $result;
    											$pos_544 = $this->pos;
    											if (( $subres = $this->literal( 'cacheblock' ) ) !== FALSE) {
    												$result["text"] .= $subres;
    												$_547 = TRUE; break;
    											}
    											$result = $res_544;
    											$this->pos = $pos_544;
    											if (( $subres = $this->literal( 'include' ) ) !== FALSE) {
    												$result["text"] .= $subres;
    												$_547 = TRUE; break;
    											}
    											$result = $res_544;
    											$this->pos = $pos_544;
    											$_547 = FALSE; break;
    										}
    										while(0);
    										if( $_547 === TRUE ) { $_549 = TRUE; break; }
    										$result = $res_542;
    										$this->pos = $pos_542;
    										$_549 = FALSE; break;
    									}
    									while(0);
    									if( $_549 === TRUE ) { $_551 = TRUE; break; }
    									$result = $res_540;
    									$this->pos = $pos_540;
    									$_551 = FALSE; break;
    								}
    								while(0);
    								if( $_551 === TRUE ) { $_553 = TRUE; break; }
    								$result = $res_538;
    								$this->pos = $pos_538;
    								$_553 = FALSE; break;
    							}
    							while(0);
    							if( $_553 === TRUE ) { $_555 = TRUE; break; }
    							$result = $res_536;
    							$this->pos = $pos_536;
    							$_555 = FALSE; break;
    						}
    						while(0);
    						if( $_555 === TRUE ) { $_557 = TRUE; break; }
    						$result = $res_534;
    						$this->pos = $pos_534;
    						$_557 = FALSE; break;
    					}
    					while(0);
    					if( $_557 === TRUE ) { $_559 = TRUE; break; }
    					$result = $res_532;
    					$this->pos = $pos_532;
    					$_559 = FALSE; break;
    				}
    				while(0);
    				if( $_559 === FALSE) { $_561 = FALSE; break; }
    				$_561 = TRUE; break;
    			}
    			while(0);
    			if( $_561 === FALSE) { $_564 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_564 = FALSE; break; }
    			$_564 = TRUE; break;
    		}
    		while(0);
    		if( $_564 === TRUE ) { $_566 = TRUE; break; }
    		$result = $res_530;
    		$this->pos = $pos_530;
    		$_566 = FALSE; break;
    	}
    	while(0);
    	if( $_566 === TRUE ) { return $this->finalise($result); }
    	if( $_566 === FALSE) { return FALSE; }
    }


    /* ClosedBlock: '<%' < !NotBlockTag BlockName:Word ( [ :BlockArguments ] )? > Zap:'%>' Template:$TemplateMatcher?
    '<%' < 'end_' '$BlockName' > '%>' */
    protected $match_ClosedBlock_typestack = array('ClosedBlock');
    function match_ClosedBlock ($stack = array()) {
    	$matchrule = "ClosedBlock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_586 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_586 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_570 = $result;
    		$pos_570 = $this->pos;
    		$matcher = 'match_'.'NotBlockTag'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$result = $res_570;
    			$this->pos = $pos_570;
    			$_586 = FALSE; break;
    		}
    		else {
    			$result = $res_570;
    			$this->pos = $pos_570;
    		}
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "BlockName" );
    		}
    		else { $_586 = FALSE; break; }
    		$res_576 = $result;
    		$pos_576 = $this->pos;
    		$_575 = NULL;
    		do {
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_575 = FALSE; break; }
    			$matcher = 'match_'.'BlockArguments'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "BlockArguments" );
    			}
    			else { $_575 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_575 = FALSE; break; }
    			$_575 = TRUE; break;
    		}
    		while(0);
    		if( $_575 === FALSE) {
    			$result = $res_576;
    			$this->pos = $pos_576;
    			unset( $res_576 );
    			unset( $pos_576 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "Zap" ); 
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'Zap' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_586 = FALSE; break;
    		}
    		$res_579 = $result;
    		$pos_579 = $this->pos;
    		$matcher = 'match_'.$this->expression($result, $stack, 'TemplateMatcher'); $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Template" );
    		}
    		else {
    			$result = $res_579;
    			$this->pos = $pos_579;
    			unset( $res_579 );
    			unset( $pos_579 );
    		}
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_586 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'end_' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_586 = FALSE; break; }
    		if (( $subres = $this->literal( ''.$this->expression($result, $stack, 'BlockName').'' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_586 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_586 = FALSE; break; }
    		$_586 = TRUE; break;
    	}
    	while(0);
    	if( $_586 === TRUE ) { return $this->finalise($result); }
    	if( $_586 === FALSE) { return FALSE; }
    }



    
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

    /* OpenBlock: '<%' < !NotBlockTag BlockName:Word ( [ :BlockArguments ] )? > '%>' */
    protected $match_OpenBlock_typestack = array('OpenBlock');
    function match_OpenBlock ($stack = array()) {
    	$matchrule = "OpenBlock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_599 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_599 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_590 = $result;
    		$pos_590 = $this->pos;
    		$matcher = 'match_'.'NotBlockTag'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$result = $res_590;
    			$this->pos = $pos_590;
    			$_599 = FALSE; break;
    		}
    		else {
    			$result = $res_590;
    			$this->pos = $pos_590;
    		}
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "BlockName" );
    		}
    		else { $_599 = FALSE; break; }
    		$res_596 = $result;
    		$pos_596 = $this->pos;
    		$_595 = NULL;
    		do {
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_595 = FALSE; break; }
    			$matcher = 'match_'.'BlockArguments'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "BlockArguments" );
    			}
    			else { $_595 = FALSE; break; }
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_595 = FALSE; break; }
    			$_595 = TRUE; break;
    		}
    		while(0);
    		if( $_595 === FALSE) {
    			$result = $res_596;
    			$this->pos = $pos_596;
    			unset( $res_596 );
    			unset( $pos_596 );
    		}
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_599 = FALSE; break; }
    		$_599 = TRUE; break;
    	}
    	while(0);
    	if( $_599 === TRUE ) { return $this->finalise($result); }
    	if( $_599 === FALSE) { return FALSE; }
    }



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
    
    /* MismatchedEndBlock: '<%' < 'end_' :Word > '%>' */
    protected $match_MismatchedEndBlock_typestack = array('MismatchedEndBlock');
    function match_MismatchedEndBlock ($stack = array()) {
    	$matchrule = "MismatchedEndBlock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_607 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_607 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( 'end_' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_607 = FALSE; break; }
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Word" );
    		}
    		else { $_607 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_607 = FALSE; break; }
    		$_607 = TRUE; break;
    	}
    	while(0);
    	if( $_607 === TRUE ) { return $this->finalise($result); }
    	if( $_607 === FALSE) { return FALSE; }
    }



    public function MismatchedEndBlock__finalise(&$res)
    {
        $blockname = $res['Word']['text'];
        throw new SSTemplateParseException('Unexpected close tag end_' . $blockname .
            ' encountered. Perhaps you have mis-nested blocks, or have mis-spelled a tag?', $this);
    }

    /* MalformedOpenTag: '<%' < !NotBlockTag Tag:Word  !( ( [ :BlockArguments ] )? > '%>' ) */
    protected $match_MalformedOpenTag_typestack = array('MalformedOpenTag');
    function match_MalformedOpenTag ($stack = array()) {
    	$matchrule = "MalformedOpenTag"; $result = $this->construct($matchrule, $matchrule, null);
    	$_622 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_622 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$res_611 = $result;
    		$pos_611 = $this->pos;
    		$matcher = 'match_'.'NotBlockTag'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$result = $res_611;
    			$this->pos = $pos_611;
    			$_622 = FALSE; break;
    		}
    		else {
    			$result = $res_611;
    			$this->pos = $pos_611;
    		}
    		$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "Tag" );
    		}
    		else { $_622 = FALSE; break; }
    		$res_621 = $result;
    		$pos_621 = $this->pos;
    		$_620 = NULL;
    		do {
    			$res_617 = $result;
    			$pos_617 = $this->pos;
    			$_616 = NULL;
    			do {
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				else { $_616 = FALSE; break; }
    				$matcher = 'match_'.'BlockArguments'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "BlockArguments" );
    				}
    				else { $_616 = FALSE; break; }
    				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    				else { $_616 = FALSE; break; }
    				$_616 = TRUE; break;
    			}
    			while(0);
    			if( $_616 === FALSE) {
    				$result = $res_617;
    				$this->pos = $pos_617;
    				unset( $res_617 );
    				unset( $pos_617 );
    			}
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_620 = FALSE; break; }
    			$_620 = TRUE; break;
    		}
    		while(0);
    		if( $_620 === TRUE ) {
    			$result = $res_621;
    			$this->pos = $pos_621;
    			$_622 = FALSE; break;
    		}
    		if( $_620 === FALSE) {
    			$result = $res_621;
    			$this->pos = $pos_621;
    		}
    		$_622 = TRUE; break;
    	}
    	while(0);
    	if( $_622 === TRUE ) { return $this->finalise($result); }
    	if( $_622 === FALSE) { return FALSE; }
    }



    public function MalformedOpenTag__finalise(&$res)
    {
        $tag = $res['Tag']['text'];
        throw new SSTemplateParseException("Malformed opening block tag $tag. Perhaps you have tried to use operators?", $this);
    }
    
    /* MalformedCloseTag: '<%' < Tag:('end_' :Word ) !( > '%>' ) */
    protected $match_MalformedCloseTag_typestack = array('MalformedCloseTag');
    function match_MalformedCloseTag ($stack = array()) {
    	$matchrule = "MalformedCloseTag"; $result = $this->construct($matchrule, $matchrule, null);
    	$_634 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_634 = FALSE; break; }
    		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "Tag" ); 
    		$_628 = NULL;
    		do {
    			if (( $subres = $this->literal( 'end_' ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_628 = FALSE; break; }
    			$matcher = 'match_'.'Word'; $key = $matcher; $pos = $this->pos;
    			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    			if ($subres !== FALSE) {
    				$this->store( $result, $subres, "Word" );
    			}
    			else { $_628 = FALSE; break; }
    			$_628 = TRUE; break;
    		}
    		while(0);
    		if( $_628 === TRUE ) {
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'Tag' );
    		}
    		if( $_628 === FALSE) {
    			$result = array_pop($stack);
    			$_634 = FALSE; break;
    		}
    		$res_633 = $result;
    		$pos_633 = $this->pos;
    		$_632 = NULL;
    		do {
    			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
    			if (( $subres = $this->literal( '%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    			else { $_632 = FALSE; break; }
    			$_632 = TRUE; break;
    		}
    		while(0);
    		if( $_632 === TRUE ) {
    			$result = $res_633;
    			$this->pos = $pos_633;
    			$_634 = FALSE; break;
    		}
    		if( $_632 === FALSE) {
    			$result = $res_633;
    			$this->pos = $pos_633;
    		}
    		$_634 = TRUE; break;
    	}
    	while(0);
    	if( $_634 === TRUE ) { return $this->finalise($result); }
    	if( $_634 === FALSE) { return FALSE; }
    }



    public function MalformedCloseTag__finalise(&$res)
    {
        $tag = $res['Tag']['text'];
        throw new SSTemplateParseException("Malformed closing block tag $tag. Perhaps you have tried to pass an " .
            "argument to one?", $this);
    }

    /* MalformedBlock: MalformedOpenTag | MalformedCloseTag */
    protected $match_MalformedBlock_typestack = array('MalformedBlock');
    function match_MalformedBlock ($stack = array()) {
    	$matchrule = "MalformedBlock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_639 = NULL;
    	do {
    		$res_636 = $result;
    		$pos_636 = $this->pos;
    		$matcher = 'match_'.'MalformedOpenTag'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_639 = TRUE; break;
    		}
    		$result = $res_636;
    		$this->pos = $pos_636;
    		$matcher = 'match_'.'MalformedCloseTag'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    			$_639 = TRUE; break;
    		}
    		$result = $res_636;
    		$this->pos = $pos_636;
    		$_639 = FALSE; break;
    	}
    	while(0);
    	if( $_639 === TRUE ) { return $this->finalise($result); }
    	if( $_639 === FALSE) { return FALSE; }
    }




    /* Comment: "<%--" (!"--%>" /(?s)./)+ "--%>" */
    protected $match_Comment_typestack = array('Comment');
    function match_Comment ($stack = array()) {
    	$matchrule = "Comment"; $result = $this->construct($matchrule, $matchrule, null);
    	$_647 = NULL;
    	do {
    		if (( $subres = $this->literal( '<%--' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_647 = FALSE; break; }
    		$count = 0;
    		while (true) {
    			$res_645 = $result;
    			$pos_645 = $this->pos;
    			$_644 = NULL;
    			do {
    				$res_642 = $result;
    				$pos_642 = $this->pos;
    				if (( $subres = $this->literal( '--%>' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$result = $res_642;
    					$this->pos = $pos_642;
    					$_644 = FALSE; break;
    				}
    				else {
    					$result = $res_642;
    					$this->pos = $pos_642;
    				}
    				if (( $subres = $this->rx( '/(?s)./' ) ) !== FALSE) { $result["text"] .= $subres; }
    				else { $_644 = FALSE; break; }
    				$_644 = TRUE; break;
    			}
    			while(0);
    			if( $_644 === FALSE) {
    				$result = $res_645;
    				$this->pos = $pos_645;
    				unset( $res_645 );
    				unset( $pos_645 );
    				break;
    			}
    			$count += 1;
    		}
    		if ($count > 0) {  }
    		else { $_647 = FALSE; break; }
    		if (( $subres = $this->literal( '--%>' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_647 = FALSE; break; }
    		$_647 = TRUE; break;
    	}
    	while(0);
    	if( $_647 === TRUE ) { return $this->finalise($result); }
    	if( $_647 === FALSE) { return FALSE; }
    }



    public function Comment__construct(&$res)
    {
        $res['php'] = '';
    }

    /* TopTemplate: (Comment | Translate | If | Require | CacheBlock | UncachedBlock | Include | ClosedBlock | OpenBlock |
     MalformedBlock | MismatchedEndBlock  | Injection | Text)+ */
    protected $match_TopTemplate_typestack = array('TopTemplate','Template');
    function match_TopTemplate ($stack = array()) {
    	$matchrule = "TopTemplate"; $result = $this->construct($matchrule, $matchrule, array('TemplateMatcher' => 'Template'));
    	$count = 0;
    	while (true) {
    		$res_699 = $result;
    		$pos_699 = $this->pos;
    		$_698 = NULL;
    		do {
    			$_696 = NULL;
    			do {
    				$res_649 = $result;
    				$pos_649 = $this->pos;
    				$matcher = 'match_'.'Comment'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres );
    					$_696 = TRUE; break;
    				}
    				$result = $res_649;
    				$this->pos = $pos_649;
    				$_694 = NULL;
    				do {
    					$res_651 = $result;
    					$pos_651 = $this->pos;
    					$matcher = 'match_'.'Translate'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres );
    						$_694 = TRUE; break;
    					}
    					$result = $res_651;
    					$this->pos = $pos_651;
    					$_692 = NULL;
    					do {
    						$res_653 = $result;
    						$pos_653 = $this->pos;
    						$matcher = 'match_'.'If'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres );
    							$_692 = TRUE; break;
    						}
    						$result = $res_653;
    						$this->pos = $pos_653;
    						$_690 = NULL;
    						do {
    							$res_655 = $result;
    							$pos_655 = $this->pos;
    							$matcher = 'match_'.'Require'; $key = $matcher; $pos = $this->pos;
    							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    							if ($subres !== FALSE) {
    								$this->store( $result, $subres );
    								$_690 = TRUE; break;
    							}
    							$result = $res_655;
    							$this->pos = $pos_655;
    							$_688 = NULL;
    							do {
    								$res_657 = $result;
    								$pos_657 = $this->pos;
    								$matcher = 'match_'.'CacheBlock'; $key = $matcher; $pos = $this->pos;
    								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    								if ($subres !== FALSE) {
    									$this->store( $result, $subres );
    									$_688 = TRUE; break;
    								}
    								$result = $res_657;
    								$this->pos = $pos_657;
    								$_686 = NULL;
    								do {
    									$res_659 = $result;
    									$pos_659 = $this->pos;
    									$matcher = 'match_'.'UncachedBlock'; $key = $matcher; $pos = $this->pos;
    									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    									if ($subres !== FALSE) {
    										$this->store( $result, $subres );
    										$_686 = TRUE; break;
    									}
    									$result = $res_659;
    									$this->pos = $pos_659;
    									$_684 = NULL;
    									do {
    										$res_661 = $result;
    										$pos_661 = $this->pos;
    										$matcher = 'match_'.'Include'; $key = $matcher; $pos = $this->pos;
    										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    										if ($subres !== FALSE) {
    											$this->store( $result, $subres );
    											$_684 = TRUE; break;
    										}
    										$result = $res_661;
    										$this->pos = $pos_661;
    										$_682 = NULL;
    										do {
    											$res_663 = $result;
    											$pos_663 = $this->pos;
    											$matcher = 'match_'.'ClosedBlock'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres );
    												$_682 = TRUE; break;
    											}
    											$result = $res_663;
    											$this->pos = $pos_663;
    											$_680 = NULL;
    											do {
    												$res_665 = $result;
    												$pos_665 = $this->pos;
    												$matcher = 'match_'.'OpenBlock'; $key = $matcher; $pos = $this->pos;
    												$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    												if ($subres !== FALSE) {
    													$this->store( $result, $subres );
    													$_680 = TRUE; break;
    												}
    												$result = $res_665;
    												$this->pos = $pos_665;
    												$_678 = NULL;
    												do {
    													$res_667 = $result;
    													$pos_667 = $this->pos;
    													$matcher = 'match_'.'MalformedBlock'; $key = $matcher; $pos = $this->pos;
    													$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    													if ($subres !== FALSE) {
    														$this->store( $result, $subres );
    														$_678 = TRUE; break;
    													}
    													$result = $res_667;
    													$this->pos = $pos_667;
    													$_676 = NULL;
    													do {
    														$res_669 = $result;
    														$pos_669 = $this->pos;
    														$matcher = 'match_'.'MismatchedEndBlock'; $key = $matcher; $pos = $this->pos;
    														$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    														if ($subres !== FALSE) {
    															$this->store( $result, $subres );
    															$_676 = TRUE; break;
    														}
    														$result = $res_669;
    														$this->pos = $pos_669;
    														$_674 = NULL;
    														do {
    															$res_671 = $result;
    															$pos_671 = $this->pos;
    															$matcher = 'match_'.'Injection'; $key = $matcher; $pos = $this->pos;
    															$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    															if ($subres !== FALSE) {
    																$this->store( $result, $subres );
    																$_674 = TRUE; break;
    															}
    															$result = $res_671;
    															$this->pos = $pos_671;
    															$matcher = 'match_'.'Text'; $key = $matcher; $pos = $this->pos;
    															$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    															if ($subres !== FALSE) {
    																$this->store( $result, $subres );
    																$_674 = TRUE; break;
    															}
    															$result = $res_671;
    															$this->pos = $pos_671;
    															$_674 = FALSE; break;
    														}
    														while(0);
    														if( $_674 === TRUE ) { $_676 = TRUE; break; }
    														$result = $res_669;
    														$this->pos = $pos_669;
    														$_676 = FALSE; break;
    													}
    													while(0);
    													if( $_676 === TRUE ) { $_678 = TRUE; break; }
    													$result = $res_667;
    													$this->pos = $pos_667;
    													$_678 = FALSE; break;
    												}
    												while(0);
    												if( $_678 === TRUE ) { $_680 = TRUE; break; }
    												$result = $res_665;
    												$this->pos = $pos_665;
    												$_680 = FALSE; break;
    											}
    											while(0);
    											if( $_680 === TRUE ) { $_682 = TRUE; break; }
    											$result = $res_663;
    											$this->pos = $pos_663;
    											$_682 = FALSE; break;
    										}
    										while(0);
    										if( $_682 === TRUE ) { $_684 = TRUE; break; }
    										$result = $res_661;
    										$this->pos = $pos_661;
    										$_684 = FALSE; break;
    									}
    									while(0);
    									if( $_684 === TRUE ) { $_686 = TRUE; break; }
    									$result = $res_659;
    									$this->pos = $pos_659;
    									$_686 = FALSE; break;
    								}
    								while(0);
    								if( $_686 === TRUE ) { $_688 = TRUE; break; }
    								$result = $res_657;
    								$this->pos = $pos_657;
    								$_688 = FALSE; break;
    							}
    							while(0);
    							if( $_688 === TRUE ) { $_690 = TRUE; break; }
    							$result = $res_655;
    							$this->pos = $pos_655;
    							$_690 = FALSE; break;
    						}
    						while(0);
    						if( $_690 === TRUE ) { $_692 = TRUE; break; }
    						$result = $res_653;
    						$this->pos = $pos_653;
    						$_692 = FALSE; break;
    					}
    					while(0);
    					if( $_692 === TRUE ) { $_694 = TRUE; break; }
    					$result = $res_651;
    					$this->pos = $pos_651;
    					$_694 = FALSE; break;
    				}
    				while(0);
    				if( $_694 === TRUE ) { $_696 = TRUE; break; }
    				$result = $res_649;
    				$this->pos = $pos_649;
    				$_696 = FALSE; break;
    			}
    			while(0);
    			if( $_696 === FALSE) { $_698 = FALSE; break; }
    			$_698 = TRUE; break;
    		}
    		while(0);
    		if( $_698 === FALSE) {
    			$result = $res_699;
    			$this->pos = $pos_699;
    			unset( $res_699 );
    			unset( $pos_699 );
    			break;
    		}
    		$count += 1;
    	}
    	if ($count > 0) { return $this->finalise($result); }
    	else { return FALSE; }
    }



    
    /**
     * The TopTemplate also includes the opening stanza to start off the template
     */
    public function TopTemplate__construct(&$res)
    {
        $res['php'] = <<<PHP
<?php


PHP;
    }

    /* Text: (
        / [^<${\\]+ / |
        / (\\.) / |
        '<' !'%' |
        !'{' '$' |
        '{' !'$' |
        '{$' !(/[A-Za-z_]/)
    )+ */
    protected $match_Text_typestack = array('Text');
    function match_Text ($stack = array()) {
    	$matchrule = "Text"; $result = $this->construct($matchrule, $matchrule, null);
    	$count = 0;
    	while (true) {
    		$res_736 = $result;
    		$pos_736 = $this->pos;
    		$_735 = NULL;
    		do {
    			$_733 = NULL;
    			do {
    				$res_700 = $result;
    				$pos_700 = $this->pos;
    				if (( $subres = $this->rx( '/ [^<${\\\\]+ /' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_733 = TRUE; break;
    				}
    				$result = $res_700;
    				$this->pos = $pos_700;
    				$_731 = NULL;
    				do {
    					$res_702 = $result;
    					$pos_702 = $this->pos;
    					if (( $subres = $this->rx( '/ (\\\\.) /' ) ) !== FALSE) {
    						$result["text"] .= $subres;
    						$_731 = TRUE; break;
    					}
    					$result = $res_702;
    					$this->pos = $pos_702;
    					$_729 = NULL;
    					do {
    						$res_704 = $result;
    						$pos_704 = $this->pos;
    						$_707 = NULL;
    						do {
    							if (substr($this->string,$this->pos,1) == '<') {
    								$this->pos += 1;
    								$result["text"] .= '<';
    							}
    							else { $_707 = FALSE; break; }
    							$res_706 = $result;
    							$pos_706 = $this->pos;
    							if (substr($this->string,$this->pos,1) == '%') {
    								$this->pos += 1;
    								$result["text"] .= '%';
    								$result = $res_706;
    								$this->pos = $pos_706;
    								$_707 = FALSE; break;
    							}
    							else {
    								$result = $res_706;
    								$this->pos = $pos_706;
    							}
    							$_707 = TRUE; break;
    						}
    						while(0);
    						if( $_707 === TRUE ) { $_729 = TRUE; break; }
    						$result = $res_704;
    						$this->pos = $pos_704;
    						$_727 = NULL;
    						do {
    							$res_709 = $result;
    							$pos_709 = $this->pos;
    							$_712 = NULL;
    							do {
    								$res_710 = $result;
    								$pos_710 = $this->pos;
    								if (substr($this->string,$this->pos,1) == '{') {
    									$this->pos += 1;
    									$result["text"] .= '{';
    									$result = $res_710;
    									$this->pos = $pos_710;
    									$_712 = FALSE; break;
    								}
    								else {
    									$result = $res_710;
    									$this->pos = $pos_710;
    								}
    								if (substr($this->string,$this->pos,1) == '$') {
    									$this->pos += 1;
    									$result["text"] .= '$';
    								}
    								else { $_712 = FALSE; break; }
    								$_712 = TRUE; break;
    							}
    							while(0);
    							if( $_712 === TRUE ) { $_727 = TRUE; break; }
    							$result = $res_709;
    							$this->pos = $pos_709;
    							$_725 = NULL;
    							do {
    								$res_714 = $result;
    								$pos_714 = $this->pos;
    								$_717 = NULL;
    								do {
    									if (substr($this->string,$this->pos,1) == '{') {
    										$this->pos += 1;
    										$result["text"] .= '{';
    									}
    									else { $_717 = FALSE; break; }
    									$res_716 = $result;
    									$pos_716 = $this->pos;
    									if (substr($this->string,$this->pos,1) == '$') {
    										$this->pos += 1;
    										$result["text"] .= '$';
    										$result = $res_716;
    										$this->pos = $pos_716;
    										$_717 = FALSE; break;
    									}
    									else {
    										$result = $res_716;
    										$this->pos = $pos_716;
    									}
    									$_717 = TRUE; break;
    								}
    								while(0);
    								if( $_717 === TRUE ) { $_725 = TRUE; break; }
    								$result = $res_714;
    								$this->pos = $pos_714;
    								$_723 = NULL;
    								do {
    									if (( $subres = $this->literal( '{$' ) ) !== FALSE) {
    										$result["text"] .= $subres;
    									}
    									else { $_723 = FALSE; break; }
    									$res_722 = $result;
    									$pos_722 = $this->pos;
    									$_721 = NULL;
    									do {
    										if (( $subres = $this->rx( '/[A-Za-z_]/' ) ) !== FALSE) {
    											$result["text"] .= $subres;
    										}
    										else { $_721 = FALSE; break; }
    										$_721 = TRUE; break;
    									}
    									while(0);
    									if( $_721 === TRUE ) {
    										$result = $res_722;
    										$this->pos = $pos_722;
    										$_723 = FALSE; break;
    									}
    									if( $_721 === FALSE) {
    										$result = $res_722;
    										$this->pos = $pos_722;
    									}
    									$_723 = TRUE; break;
    								}
    								while(0);
    								if( $_723 === TRUE ) { $_725 = TRUE; break; }
    								$result = $res_714;
    								$this->pos = $pos_714;
    								$_725 = FALSE; break;
    							}
    							while(0);
    							if( $_725 === TRUE ) { $_727 = TRUE; break; }
    							$result = $res_709;
    							$this->pos = $pos_709;
    							$_727 = FALSE; break;
    						}
    						while(0);
    						if( $_727 === TRUE ) { $_729 = TRUE; break; }
    						$result = $res_704;
    						$this->pos = $pos_704;
    						$_729 = FALSE; break;
    					}
    					while(0);
    					if( $_729 === TRUE ) { $_731 = TRUE; break; }
    					$result = $res_702;
    					$this->pos = $pos_702;
    					$_731 = FALSE; break;
    				}
    				while(0);
    				if( $_731 === TRUE ) { $_733 = TRUE; break; }
    				$result = $res_700;
    				$this->pos = $pos_700;
    				$_733 = FALSE; break;
    			}
    			while(0);
    			if( $_733 === FALSE) { $_735 = FALSE; break; }
    			$_735 = TRUE; break;
    		}
    		while(0);
    		if( $_735 === FALSE) {
    			$result = $res_736;
    			$this->pos = $pos_736;
    			unset( $res_736 );
    			unset( $pos_736 );
    			break;
    		}
    		$count += 1;
    	}
    	if ($count > 0) { return $this->finalise($result); }
    	else { return FALSE; }
    }




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
