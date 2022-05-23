@extends('front.app')

@section('content')

    @include('front.partials.account-tabs')

    <div class="account-base">
        <div class="container">

            @if (!is_null($history) && (isValidSubscr() !== false))
                <div class="row">
                    <div class="col-md-9">
                        <div class="row videos-area">
                            <div class="col-md-6 item">
                                <div class="inside">
                                    <video src="{{ asset('assets/IMG_5078.MOV') }}" autoplay muted playsinline style="width: 100%"></video>
                                </div>
                            </div>
                            <div class="col-md-6 item">
                                <div class="inside">
                                    <img src="{{ asset('/assets/3.jpg') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row btns-area">
                            <div class="col-md-6">
                                <div class="inside">
                                    <h3>CHOOSE TRAINING TIME</h3>
                                    <select class="traning" name="">
                                        <option value="08:00" {{ $schedule->traning == '08:00' ? 'selected' : ''}}>
                                            08:00
                                        </option>
                                        <option value="09:00" {{ $schedule->traning == '09:00' ? 'selected' : ''}}>
                                            09:00
                                        </option>
                                        <option value="10:00" {{ $schedule->traning == '10:00' ? 'selected' : ''}}>
                                            10:00
                                        </option>
                                        <option value="11:00" {{ $schedule->traning == '11:00' ? 'selected' : ''}}>
                                            11:00
                                        </option>
                                        <option value="12:00" {{ $schedule->traning == '12:00' ? 'selected' : ''}}>
                                            12:00
                                        </option>
                                        <option value="13:00" {{ $schedule->traning == '13:00' ? 'selected' : ''}}>
                                            13:00
                                        </option>
                                        <option value="14:00" {{ $schedule->traning == '14:00' ? 'selected' : ''}}>
                                            14:00
                                        </option>
                                        <option value="15:00" {{ $schedule->traning == '15:00' ? 'selected' : ''}}>
                                            15:00
                                        </option>
                                        <option value="16:00" {{ $schedule->traning == '16:00' ? 'selected' : ''}}>
                                            16:00
                                        </option>
                                        <option value="17:00" {{ $schedule->traning == '17:00' ? 'selected' : ''}}>
                                            17:00
                                        </option>
                                        <option value="18:00" {{ $schedule->traning == '18:00' ? 'selected' : ''}}>
                                            18:00
                                        </option>
                                        <option value="19:00" {{ $schedule->traning == '19:00' ? 'selected' : ''}}>
                                            19:00
                                        </option>
                                        <option value="20:00" {{ $schedule->traning == '20:00' ? 'selected' : ''}}>
                                            20:00
                                        </option>
                                        <option value="21:00" {{ $schedule->traning == '21:00' ? 'selected' : ''}}>
                                            21:00
                                        </option>
                                        <option value="22:00" {{ $schedule->traning == '22:00' ? 'selected' : ''}}>
                                            22:00
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 heading">
                                <div class="inside">
                                    <h2>Meal Plan</h2>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <section class="note">
                                    <strong>Recommended</strong> daily requirement of water 3l / a day
                                </section>
                            </div>
                        </div>

                        <div class="regime">
                            @if (!is_null($schedule))
                                @if ($schedule->type != 'discharging')
                                    @if ($schedule->type == 'rest')
                                        <h2>Day {{ $schedule->day }} (day without workout)</h2>
                                    @else
                                        <h2>Day {{ $schedule->day }} (workout at {{ $schedule->traning }})</h2>
                                    @endif
                                    <div class="row item">
                                        <div class="col-md-2 time">{{ json_decode($schedule->food_1)->time }}</div>
                                        <div class="col-md-8 name">{{ json_decode($schedule->food_1)->name }}</div>
                                        <div class="col-md-2 weight">{{ json_decode($schedule->food_1)->qty }}</div>
                                    </div>
                                    <div class="row item">
                                        <div class="col-md-2 time">{{ json_decode($schedule->food_2)->time }}</div>
                                        <div class="col-md-8 name">{{ json_decode($schedule->food_2)->name }}</div>
                                        <div class="col-md-2 weight">{{ json_decode($schedule->food_2)->qty }}</div>
                                    </div>
                                    <div class="row item">
                                        <div class="col-md-2 time">{{ json_decode($schedule->food_3)->time }}</div>
                                        <div class="col-md-8 name">{{ json_decode($schedule->food_3)->name }}</div>
                                        <div class="col-md-2 weight">{{ json_decode($schedule->food_3)->qty }}</div>
                                    </div>
                                    <div class="row item">
                                        <div class="col-md-2 time">{{ json_decode($schedule->food_4)->time }}</div>
                                        <div class="col-md-8 name">{{ json_decode($schedule->food_4)->name }}</div>
                                        <div class="col-md-2 weight">{{ json_decode($schedule->food_4)->qty }}</div>
                                    </div>
                                    @if (!empty($schedule->food_5))
                                        <div class="row item">
                                            <div class="col-md-2 time">{{ json_decode($schedule->food_5)->time }}</div>
                                            <div class="col-md-8 name">{{ json_decode($schedule->food_5)->name }}</div>
                                            <div class="col-md-2 weight">{{ json_decode($schedule->food_5)->qty }}</div>
                                        </div>
                                    @endif
                                @else
                                    <h2>Day {{ $schedule->day }} (Detox)</h2>
                                    {!! $schedule->details !!}
                                @endif
                            @endif
                        </div>
                        <div class="text-center"><br><br>
                            <a href="{{ route('rebuild.ration') }}" class="btn btn-default pull-center">Generate New</a>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="weights">
                            <div class="init">

                                <?php
                                $recomand = $history->current_weight - 24;
                                $wish = $history->target_weight;
                                $current = !empty($diary->weight_body) ? $diary->weight_body : $history->current_weight;
                                $initial = $history->current_weight;
                                ?>

                                <span>
                        @if ($initial >= $current)
                                        <small>Current weight</small>
                                        <p>{{ $initial }}<b>kg</b></p>
                                    @else
                                        <small>Weight</small>
                                        <p>{{ $current }}<b>kg</b></p>
                                    @endif
                            </span>
                                <div class="now"><span>
                            @if(($wish > $initial) || ($wish > $current))
                                <small>Desired weight</small>
                                <p>{{ $wish }}<b>kg</b></p>
                            @elseif ($initial < $current)
                                <small>Initial weight</small>
                                <p>{{ $initial }}<b>kg</b></p>
                            @else
                                <small>Weight</small>
                                <p>{{ $current }}<b>kg</b></p>
                            @endif
                        </span>
                                    <div class="wish">
                            <span>
                                @if($wish > $recomand)
                                    <small>Desired weight</small>
                                    <p>{{ $wish }}<b>kg</b></p>

                                @elseif (($wish < $initial) || ($wish < $current))
                                    <small>Weight</small>
                                    <p>{{ $current }}<b>kg</b></p>
                                @else
                                    <small>Recommended weight</small>
                                    <p>{{ $recomand }}<b></b></p>
                                @endif
                            </span>
                                        <div class="recom">
                                            @if ($wish > $recomand)
                                                <span>
                                        <small>Recommended weight</small>
                                        <p>{{ $recomand }}<b>kg</b></p>
                                    </span>
                                            @else
                                                <span>
                                        <small>Desired weight</small>
                                        <p>{{ $wish }}<b>kg</b></p>
                                    </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (!is_null($history) && (isValidSubscr() === false))
                <div class="row">
                    <div class="col-md-9">
                        <div class="row videos-area">
                            <div class="col-md-6 item">
                                <div class="inside">
                                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/v4xTx8cjbkY"
                                            frameborder="0" allowfullscreen></iframe>
                                    <a href="#"></a>
                                    <p>Lorem Ipsum</p>
                                </div>
                            </div>
                            <div class="col-md-6 item">
                                <div class="inside">
                                    <iframe width="100%" height="100%"
                                            src="https://www.youtube.com/embed/dvTaXj7rKJU?rel=0&amp;controls=0&amp;showinfo=0"
                                            frameborder="0" allowfullscreen></iframe>
                                    <a href="#"></a>
                                    <p>TNF + RAP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <p class="wellcome">Congratulations! You are now in your personal office offering a set of
                            individualized tools to achieve your goal - shaping, toning,
                            increase muscle mass, maintain and learn the rules of a healthy lifestyle.
                            If you have purchased a subscription or taken a free week.</p>
                        <div class="goto-block">
                            <a href="/page/subscriptions">PRICING PLAN</a>
                            <a href="/add-free-week">GET ONE FREE WEEK</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @include('front.partials.footer-line')
@stop

