<?php

namespace js4php5\compiler\parser;

use hiltonjanfield\js4php5\compiler\lexer\Lexer;
use hiltonjanfield\js4php5\VarDumper;

//TODO: Fix known bug whre parser will get stuck on a script ending with a + (does not recognize bad syntax).
//Can be tested with a script containing only '+'.
abstract class Parser
{

    protected $pda;

    protected $action;

    protected $delta;

    public function __construct($pda)
    {
        $this->pda = $pda;
        $this->action = $pda['action'];
        $this->start = $pda['start'];
        $this->delta = $pda['delta'];
    }

    public function report()
    {
        # pr($this->action);
        pr($this->start);
        foreach ($this->delta as $label => $d) {
            echo "<h3>State $label</h3>";
            foreach ($d as $glyph => $step) {
                echo $glyph . " -&gt; " . implode(':', $step) . "<br>";
            }
        }
    }

    /**
     * @param string              $symbol
     * @param Lexer               $lex
     * @param null|ParserStrategy $strategy
     *
     * @throws parse_error
     */
    public function parse($symbol, Lexer $lex, ParserStrategy $strategy = null)
    {
        $stack = array();
        $tos = $this->frame($symbol);
        $token = $lex->next();

        $c = 0;
        while (true) {
            $step = $this->getStep($tos->state, $token->getType());
//            if ($c++ == 16) {
//                VarDumper::dump($token, 'tos');
//                exit;
//            }

            // echo implode(':', $step)."<br>";
            switch ($step[0]) {
                case 'go':
                    $tos->shift($token->getText());
                    $tos->state = $step[1];
                    $token = $lex->next();
                    break;

                case 'do':
                    $semantic = $this->reduce($step[1], $tos->semantic());
                    if (empty($stack)) {
                        $strategy->assertDone($token, $lex);
                        return $semantic;
                    } else {
                        $tos = array_pop($stack);
                        $tos->shift($semantic);
                    }
                    break;

                case 'push':
                    $tos->state = $step[2];
                    $stack[] = $tos;
                    $tos = $this->frame($step[1]);
                    break;

                case 'fold':
                    $tos->fold($this->reduce($step[1], $tos->semantic()));
                    $tos->state = $step[2];
                    break;

                case 'error':
                    $stack[] = $tos;
                    $strategy->stuck($token, $lex, $stack);
                    break;

                default:
                    throw new parse_error("Impossible. Bad PDA has $step[0] instruction.");
            }
        }
        // return statement is in case 'do'
    }

    public function frame($symbol)
    {
        return new ParseStackFrame($symbol, $this->start[$symbol]);
    }

    public function getStep($label, $glyph)
    {
        $delta = $this->delta[$label];
        if (isset($delta[$glyph])) {
            return $delta[$glyph];
        }
        if (isset($delta['[default]'])) {
            return $delta['[default]'];
        }
        return array('error');
    }

    abstract public function reduce($action, $tokens);
}

/*
File: automata.so.php
License: GPL
Purpose: Contains various utilities for operating on finite automata.
*/

/*
First, we care about finality-extended e-NFAs. These are the basis for most
of the remainder of the systems.


Let it be noted early that pushdown automata will be constructed in terms
of (determinized) FAs which will interpret automata death as an indication
that the system needs to do something with the PDA stack.

We'll form the left-recursive closure of the available non-terminals following
the dieing state. If the next terminal is found in that set, then we also know
what to push and what state to enter. (Alternatively, we know the first step
in a recursive set of "pushes" which don't accept the terminal until the stack
looks the way it should.) Ambiguity here can be considered a bug in the
grammar specification. It makes the PDA non-deterministic. While it may be
possible to remove this non-determinism in some limited cases, I don't think
it's actually necessary.

We can, by this procedure, form a set of PDA rules for what to do with any
given terminal, assuming that the state transitions in the production DFA
call for a non-terminal. We thus have a special category of rule

There is another possibility: We are in a "final" state and we don't have
and edge or a push that accepts the next Token. In this case, we assume that
we have recognized a complete production rule.

We call its associated code block, which is expected to return a syntax tree
node. Then, we pop the stack. The symbol on the stack should tell which
DFA to jump into and what state it will be in after recognizing a member of
the production known to the called DFA.

We can convert this entire idea to the normal definition of a DPDA by:
1. Selecting disjoint state labels for every DFA.
2. Keeping all DFA transitions in the same table.

That done, a stack symbol is merely also a state label.

*/

/*
Now we can turn any production rule (head + set <body, action>) into a
DFA that recognizes the rule and can even invoke the correct action based
on a set of distinguishing marks. Any given final state in the DFA will
be marked with exactly the best matching action number.

A remaining problem is that of transduction. We would like to mark certain
glyphs with a symbol indicating that they cause the corresponding parse node
to go into the correct slot of a special parsing data structure which makes
for convenient reference within an action part of a rule. In other words,
we would really ideally like to turn NFTs into DFTs. It seems, at the moment,
that the transduction might still be non-deterministic. This not so much of
a problem as a big hassle.

However, if we always make the entire matched glyph list available in the form
of a list of parse nodes, then the action that corresponds to a given rule
branch is free to do fancy things.

All that remains is to build a PDA from a collection of DFAs.

These various DFAs will mostly have some transitions that are predicated on
non-terminal symbols in the CFG. We have to find all such transitions and deal
with them specially.
*/


/*
File: lex.so.php
License: GPL
Purpose: Provides a simple lexical analysis framework
Purpose: useful in so many ways. (Minilanguages are a
Purpose: fabulous way to save programming time.)
*/

$GLOBALS['wasted'] = 0;

/*
File: parser.so.php
License: GPL
Purpose: Contains the code necessary to operate the left-recursive parsers
Purpose: whose execution tables are generated by the parser generator.
*/

