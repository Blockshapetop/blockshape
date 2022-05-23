<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Contracts\QuizMap as QuizMapContract;
use App\Services\QuizMap;
use Illuminate\Http\Request;
use Restable;

class QuizController extends Controller
{
    /**
     * @var QuizMapContract
     */
    private $quizMap;


    /**
     * QuizController constructor.
     *
     * @param QuizMapContract $quizMap
     */
    public function __construct(QuizMapContract $quizMap)
    {
        $this->quizMap = $quizMap;
    }

    public function syncHint(Request $request)
    {
        if ($request->has('name')) {
            list($questionId, $answerId, $hintId) = explode('_', $request->get('name'));
            $value = (int) $request->get('value');

            $this->quizMap->sync($questionId, $answerId, $hintId, $value);

            return \Restable::success('Ok');
        }

        return Restable::bad('Bad request');
    }
}
