<?php

namespace App\Services;

use App\Services\Contracts\CardioReactionDetector as DetectorContract;
use App\Services\Responses\CardioReaction;

class CardioReactionDetector implements DetectorContract
{
    /**
     * Detect pressure type
     *
     * @param $before
     * @param $after
     * @return int
     * @throws \Exception
     */
    public function detect($before, $after = null)
    {
        if ($before >= 140) {
            return new CardioReaction(self::R_HIPERTONIE . '-' . self::R_ATTENTION);
        } else {
            $this->validateArguments($before, $after);

            $before = $this->detectBeforeValue($before);

            $after = $this->detectAfterValue($after);

            return new CardioReaction($before . '-' . $after);
        }
    }

    /**
     * @param $before
     * @return string
     */
    protected function detectBeforeValue($before)
    {
        if ($before < 110) {
            return self::R_HIPOTONIE;
        }

        return self::R_NORMOTONIE;
    }

    /**
     * @param $after
     * @return string
     */
    protected function detectAfterValue($after)
    {
        if ($after <= 130) {
            return self::R_NORMOTONIE;
        }

        return self::R_HIPERTONIE;
    }

    /**
     * @param $before
     * @param $after
     * @throws \Exception
     */
    protected function validateArguments($before, $after)
    {
        if (! ($before && $after)) {
            throw new \Exception('Missing before or after load pressure');
        }
    }
}

