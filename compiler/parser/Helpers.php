<?php

namespace js4php5\compiler\parser;


class Helpers
{

    public static $FA_NO_MARK = 99999;

    static public function gen_label()
    {
        # Won't return the same number twice. Note that we use state labels
        # for hash keys all over the place. To prevent PHP from doing the
        # wrong thing when we merge such hashes, we tack a letter on the
        # front of the labels.
        static $count = 0;
        $count++;
        return 's' . $count;
    }

}

