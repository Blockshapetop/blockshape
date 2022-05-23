<?php namespace App\Services;

use App\Repositories\OrdersRepository;
use Auth;
use App\User;
use Carbon\Carbon;
use App\UserHistory;
use App\Repositories\UserHistoryRepository;

class HistoryCreator
{
    /**
     * @var UserHistoryRepository
     */
    private $historyRepository;

    /**
     * HistoryCreator constructor.
     *
     * @param UserHistoryRepository $historyRepository
     */
    public function __construct(UserHistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function persist(User $user, UserHistory $record, array $data)
    {
        /**
         * @var $allergies
         * @var $diseases
         * @var $excludes
         * @var $quiz
         */
        extract(array_only($data, ['diseases', 'allergies', 'excludes', 'quiz']));

        $data = $this->prepareData($data);

        if ($record && $record->exists) {
            $object = $this->historyRepository->update($record, $data);
        } else {
            $object = $this->historyRepository->insert($user, $data);
        }

        $object->diseases()->sync($diseases);
        $object->allergies()->sync($allergies);
        $object->excludes()->sync($excludes);

        foreach ($quiz as $question_id => $answer_id) {
            try {
                $object->quizPairs()->create(compact('question_id', 'answer_id'));
            } catch (\Exception $e) {
                $object->quizPairs()->where(compact('question_id'))->update(compact('answer_id'));
            }
        }

        return $object;
    }

    private function handleMenstrualData($data)
    {
        if (isset($data['menstrual_cycle']['menopause'])) {
            if ($data['menstrual_cycle']['menopause']) {
                $data['menstrual_cycle'] = ['menopause' => true];
            } else {
                unset($data['menstrual_cycle']['menopause']);
            }
        }

        return $data;
    }

    private function handleCreatedDate($data)
    {
        foreach (['created_at', 'purchased_at'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = Carbon::createFromDate(
                    $data[$key]['year'],
                    $data[$key]['month'],
                    $data[$key]['day']
                );
            }
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function prepareData(array $data)
    {
        $data = $this->handleMenstrualData($data);

        $data = $this->handleCreatedDate($data);

        $data = $this->allowOnlyFillable($data);

        $data = $this->resetStatus($data);

        $data = $this->setDefaultPulse($data);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function setDefaultPulse(array $data)
    {
        if (! array_has($data, 'pulse3') || ! (int) $data['pulse3']) {
            $data['pulse3'] = 120;

            return $data;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function allowOnlyFillable(array $data)
    {
        $data = array_only($data, (new UserHistory)->getFillable());

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function resetStatus(array $data)
    {
        $data = array_merge($data, [
            'instructor_id' => auth()->check() ? auth()->user()->id : null,
            'status'        => UserHistory::STATUS_PENDING,
            'document'      => null
        ]);

        return $data;
    }
}

