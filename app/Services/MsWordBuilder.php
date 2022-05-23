<?php

namespace App\Services;

use App\Services\Contracts\DocumentBuilder as DocumentBuilderContract;
use App\User;
use App\UserHistory;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use PDFConverter;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use SplFileInfo;


/**
 * Class PdfBuilder
 *
 * @package App\Services
 */
class MsWordBuilder implements DocumentBuilderContract
{

    /**
     * @var DocumentBuilder
     */
    private $builder;
    /**
     * @var Filesystem
     */
    private $filesystem;


    /**
     * PdfBuilder constructor.
     *
     * @param DocumentBuilderContract $builder
     */
    public function __construct(DocumentBuilderContract $builder, Filesystem $filesystem)
    {
        $this->builder = $builder;
        $this->filesystem = $filesystem;

        $this->path = rtrim(config('builder.path'), '/');
    }

    /**
     * @param User        $user
     * @param UserHistory $record
     * @return string
     */
    public function build(User $user, UserHistory $record)
    {
        $document = $this->builder->build($user, $record);

        $writers = array('Word2007' => 'docx', 'ODText' => 'odt', 'RTF' => 'rtf', 'HTML' => 'html', 'PDF' => 'pdf');

        $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        Html::addHtml($section, $document, true);

        foreach ($writers as $format => $extension) {
            $fileName = ("unicasport_{$user->sluggify()}_{$record->sluggify()}.{$extension}");
            $phpWord->save($this->path . '/' . $fileName, $format);
        }

//        $this->filesystem->put(
//            $this->path . '/' . ($fileName = ("unicasport_{$user->sluggify()}_{$record->sluggify()}.doc")),
//            $document
//        );

        return ($fileName);
    }
}
=> $ration->food_1,
                'food_2' => $ration->food_2,
                'food_3' => $ration->food_3,
                'food_4' => $ration->food_4,
                'food_5' => $ration->food_5,
                'actual' => 1,
            ]);
        }

        $userRation = MRation::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->first();
        if ($userRation->type == 'discharging') {
            return redirect()->back();
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
