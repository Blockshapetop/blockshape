<?php namespace App\Services;

use App\Exercise;

class PulseMapper
{
    /**
     * @var Exercise $exercise
     */
    private $exercise;

    private $map;

    /**
     * @param Exercise $exercise
     * @param          $pulse
     * @return
     * @throws \Exception
     */
    public function findMax(Exercise $exercise, $pulse)
    {
        $this->exercise = $exercise;

        $map = $this->getMap();

        $pulse = $this->normalize($pulse);

        return $map->getAttribute("p_{$pulse}");
    }

    private function getMap()
    {
        if (null === $this->map || $this->map->exercise_id !== $this->exercise->id) {
            $this->map = $this->exercise->pulseMap;
        }

        return $this->map;
    }

    private function normalize($pulse)
    {
        $pulse = round($pulse / 10, 0) * 10;

        return $pulse;
    }
}


    }

        $training = $userRation->training;
        $date = $userRation->date;

        $userHistory = UserHistory::where('user_id', Auth::user()->id)->first();

        if (is_null($userHistory)) { return redirect()->back(); }

        $user = User::where('id', $userHistory->user_id)->first();
        $HtmlBuilder->build($user, $userHistory, null, null, 1);

        return redirect()->back();
    }

    public function getRebuldRation($ration)
    {
        $rationRebuild =  MRationRebuild::where('user_id', Auth::user()->id)
                                        ->where('date', date('Y-m-d'))
                                        ->where('food_1', "!=", $ration->food_1)
                                        ->orderByRaw("RAND()")
                                        ->first();

        MRation::where('user_id', Auth::user()->id)
                ->where('date', date('Y-m-d'))
                ->update([
                    'food_1' => $rationRebuild->food_1,
                    'food_2' => $rationRebuild->food_2,
                    'food_3' => $rationRebuild->food_3,
                    'food_4' => $rationRebuild->food_4,
                    'food_5' => $rationRebuild->food_5,
                ]);
    }

    public function changeHistory($date)
    {
        if (strtotime($date)) {
            Session::set('history_date', $date);
            echo Session::get('history_date');
        }
        return redirect()->back();
    }

    public function changeHistoryPost()
    {
        $date = Input::get('date');
        if (Session::get('history_date') == $date) {
            return '{"msg":"false"}';
        }

        Session::set('history_date', $date);
        return '{"msg":"true"}';
    }

}
