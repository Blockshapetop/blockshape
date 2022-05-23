<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Page;
use App\MPages;
use App\MSubscription;
use App\MSeminars;
use App\MEvents;
use App\MProjects;
use App\UserHistory;
use App\User;
use Input;
use Auth;
use Session;
use App\Services\Contracts\HtmlBuilder;


class SearchController extends MainController
{
    public function index()
    {
        $query =  Input::get('search');

        $seminars = MSeminars::where('title', 'LIKE', '%'.$query.'%')
                            ->orWhere('body', 'LIKE', '%'.$query.'%')
                            ->get();

        $events = MEvents::where('title', 'LIKE', '%'.$query.'%')
                        ->orWhere('body', 'LIKE', '%'.$query.'%')
                        ->get();

        $data['seminars'] = $seminars;
        $data['events'] = $events;
        $data['amount'] = count($seminars) +  count($events);

        return view('front.pages.search', $data);
    }
}

