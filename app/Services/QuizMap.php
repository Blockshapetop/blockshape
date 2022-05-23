<?php namespace App\Services;

use App\Repositories\QuizQuestionAnswersRepository;
use App\Services\Contracts\QuizMap as QuizMapContract;

class QuizMap implements QuizMapContract
{
    /**
     * @var QuizQuestionAnswersRepository
     */
    private $mapRepository;

    /**
     * QuizMap constructor.
     *
     * @param QuizQuestionAnswersRepository $mapRepository
     */
    public function __construct(QuizQuestionAnswersRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    /**
     * Sync answers with question and hints
     *
     * @param $questionId
     * @param $answerId
     * @param $hintId
     * @param $value
     * @return mixed
     */
    public function sync($questionId, $answerId, $hintId, $value)
    {
        if ($value) {
            return $this->mapRepository->attachHint($questionId, $answerId, $hintId);
        }

        return $this->mapRepository->detachHint($questionId, $answerId, $hintId);
    }
}

