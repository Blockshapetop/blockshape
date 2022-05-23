<?php

namespace App\Support;
use App\MBlockText;
use App\Cart;
use App\MOrder;
use App\MUserDiary;
use Auth;

/**
 * Helper Class
 */
class Helper
{
    public static function label($id, $lang_id = 1) {

        $label = MBlockText::where('id', $id)
                ->where('lang_id', $lang_id)
                ->first();

        if (is_null($label)) {
            return "";
        }

        return $label->body;
    }

    public static function getCartCount(){
        if (!is_null(Auth::user())) {
            $orderId = MOrder::where('status', 'pending')->where('user_id', Auth::user()->id)->first();
            if (!is_null($orderId)) {
                $cartCount = Cart::where('user_id', Auth::user()->id)->where('order_id', $orderId->id)->count();
                return $cartCount;
            }
        }
        return '';
    }

    public static function getMenstruationPeriod($date)
    {
        $diary = MUserDiary::where('date', $date)
                            ->where('user_id', Auth::user()->id)
                            ->first();

        if (!is_null($diary)) {
            return $diary->period;
        }
        return false;
    }

    public static function getWeight($date)
    {
        $diary = MUserDiary::where('date', $date)
                            ->where('user_id', Auth::user()->id)
                            ->first();

        if (!is_null($diary)) {
            if ($diary->weight_body !== '') {
                return $diary->weight_body. ' kg';
            }
        }
        return false;
    }

    public static function emptyDay($date)
    {
        $diary = MUserDiary::where('date', $date)
                            ->where('weight_body', ' ')
                            ->where('wake', ' ')
                            ->where('water_qty', ' ')
                            ->where('weight_body', ' ')
                            ->where('dejection_solidity', ' ')
                            ->where('comment', ' ')
                            ->where('user_id', Auth::user()->id)
                            ->first();

        if (!is_null($diary)) {
            return true;
        }
        return false;
    }
}

