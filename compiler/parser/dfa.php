<?php

namespace js4php5\compiler\parser;

use js4php5\compiler\parser\set;

class dfa
{
    /*
    A DFA has a simpler representation than that of an NFA.
    It also has a bit of a different interface.
    */

    function dfa()
    {
        # $this->alphabet = array();    # We don't care
        $this->states = array();    # Contains a list of labels
        $this->initial = '';        # Set this later.

        # These are hashes with state labels for keys:
        $this->final = array(); # Just a bit for each state
        $this->delta = array(); # sub-hash from symbol to label
        $this->mark = array();      # distinguishing mark
    }

    function add_state($label)
    {
        if ($this->has_state($label)) {
            die ("Trying to add existing state to an DFA.");
        }
        $this->states[] = $label;
        $this->final[$label] = false;
        $this->delta[$label] = array();
        $this->mark[$label] = Helpers::$FA_NO_MARK;
        return $label;
    }

    function has_state($label)
    {
        return isset($this->delta[$label]);
    }

    function add_transition($src, $glyph, $dest)
    {
        $this->delta[$src][$glyph] = $dest;
    }

    function step($label, $glyph)
    {
        return @$this->delta[$label][$glyph];
    }

    function accepting($label)
    {
        return array_keys($this->delta[$label]);
    }

    function minimize()
    {
        /*
        We'll use the table-filling algorithm to find pairs of
        distinguishable states. When that algorithm is done, any states
        not distinguishable are equivalent. We'll return a new DFA.
        */

        $map = $this->indistinguishable_state_map($this->table_fill());
        $dist = array();
        foreach ($map as $p => $q) {
            $dist[$q] = $q;
        }

        $dfa = new dfa();
        foreach ($dist as $p) {
            $dfa->add_state($p);
        }
        foreach ($dist as $p) {
            foreach ($this->delta[$p] as $glyph => $q) {
                $dfa->add_transition($p, $glyph, $map[$q]);
            }
            $dfa->final[$p] = $this->final[$p];
            $dfa->mark[$p] = $this->mark[$p];
        }
        $dfa->initial = $map[$this->initial];

        return $dfa;
    }

    function indistinguishable_state_map($table)
    {
        # Assumes that $table is filled according to the table filling
        # algorithm.
        $map = array();
        $set = new set($this->states);
        while ($set->count()) {
            $p = $set->one();
            foreach ($set->all() as $q) {
                if (!$table->differ($p, $q)) {
                    $map[$q] = $p;
                    $set->del($q);
                }
            }
        }
        return $map;
    }

    function table_fill()
    {
        /*
        We use a slight modification of the standard base case:
        Two states are automatically distinguishable if their marks
        differ.
        */

        # Base Case:
        $table = new distinguishing_table();

        foreach ($this->states as $s1) {
            foreach ($this->states as $s2) {
                if ($this->mark[$s1] != $this->mark[$s2]) {
                    $table->distinguish($s1, $s2);
                }
            }
        }

        # Induction: 
        do { /* nothing */
        } while (!$this->filling_round($table));

        return $table;
    }

    function filling_round(&$table)
    {
        $done = true;

        foreach ($this->states as $s1) {
            foreach ($this->states as $s2) {
                if ($s1 == $s2) {
                    continue;
                }
                if (!$table->differ($s1, $s2)) {
                    # Try to find a reason why the two states
                    # differ. If so, then mark them different
                    # and clear $done. Note that if the table
                    # has no record of either state, then we
                    # can't yet make a determination.
                    $different = $this->compare_states($s1, $s2, $table);
                    if ($different) {
                        $table->distinguish($s1, $s2);
                        $done = false;
                        break;
                    }
                }
            }
        }
        # ("Done Round<br/>");
        return $done;
    }

    function compare_states($p, $q, $table)
    {
        $sigma = array_unique(array_merge($this->accepting($p), $this->accepting($q)));
        # "Comparing $p and $q - shared vocabulary: [ ".implode(' : ', $sigma)." ] - ");
        if ($p == $q) {
            # "Same State<br/>";
            return false;
        }

        foreach ($sigma as $glyph) {
            $p1 = $this->step($p, $glyph);
            $q1 = $this->step($q, $glyph);
            if (!($p1 and $q1) or $table->differ($p1, $q1)) {
                # "<font color=green>They differ on $glyph - $p1/$q1<br/></font>");
                return true;
            }
        }

        # ("No difference found (yet)<br/>");
        return false;
    }
}

