<?php

namespace App\Services;

class FigureType
{
    const TYPE_CLEPSIDRA = 'clepsidra';

    const TYPE_PEAR = 'pear';

    const TYPE_APPLE = 'apple';

    /**
     * Detect figure type
     *
     * @param $buttocks
     * @param $shoulders
     * @return string
     */
    public function detect($buttocks, $shoulders)
    {
        if ($buttocks - $shoulders >= 6) {
            return static::TYPE_PEAR;
        }

        if ($shoulders - $buttocks >= 6) {
            return static::TYPE_APPLE;
        }

        return static::TYPE_CLEPSIDRA;
    }
}

