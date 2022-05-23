<?php

use App\Convert\MinsToTime;
use App\Convert\TimeToMins;
use App\Site;
use App\User;
use App\UserHistory;
use Terranet\VictoriaBank\Gateway\Security;
use App\MBlockText;
use App\MBlockImage;
use App\MRationTerm;
use App\MUserSubscrs;


function site_id()
{
    if ($id = env('SITE_ID')) {
        return $id;
    }

    return site()->id;
}

function site($id = null)
{
    if ($site = config('site')) {
        return $site;
    }

    if (!is_null($id)) {
        $site = Site::findOrFail($id);
    } else if ($id = env('SITE_ID')) {
        $site = Site::findOrFail($id);
    } else {
        $request = app('request');

        $site = Site::resolveRequest($request);
    }

    Config::set('site', $site);

    return $site;
}

function content($file)
{
    return file_get_contents(public_path($file));
}

function dataBase64($file)
{
    return "data:image/png;base64," . base64_encode(content($file));
}

function glossary($slug, $name = null, array $replacements = [])
{
    $app = app();
    $key = 'glossary.' . $slug;
    if (!isset($app[$key])) {
        $app->singleton($key, function () use ($slug) {
            return App\Glossary::whereSlug($slug)->first();
        });
    }

    $string = ($name ? $app[$key]->$name : $app[$key]);

    return assign($string, $replacements);
}

/**
 * @param array $replacements
 * @param       $string
 *
 * @return mixed
 */
function assign($string, array $replacements)
{
    foreach (sortReplacements($replacements) as $key => $value) {
        $string = str_replace(':' . $key, $value, $string);
    }

    return $string;
}

/**
 * @param array $replacements
 *
 * @return static
 */
function sortReplacements(array $replacements)
{
    return (new \Illuminate\Support\Collection($replacements))->sortBy(function ($value, $key) {
        return mb_strlen($key) * -1;
    });
}

function auto_p($value, $lineBreaks = true)
{
    if (trim($value) === '') {
        return '';
    }

    $value = $value . "\n"; // just to make things a little easier, pad the end
    $value = preg_replace('|<br />\s*<br />|', "\n\n", $value);

    // Space things out a little
    $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
    $value = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $value);
    $value = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $value);
    $value = str_replace(["\r\n", "\r"], "\n", $value); // cross-platform newlines

    if (strpos($value, '<object') !== false) {
        $value = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $value); // no pee inside object/embed
        $value = preg_replace('|\s*</embed>\s*|', '</embed>', $value);
    }

    $value = preg_replace("/\n\n+/", "\n\n", $value); // take care of duplicates

    // make paragraphs, including one at the end
    $values = preg_split('/\n\s*\n/', $value, -1, PREG_SPLIT_NO_EMPTY);
    $value = '';

    foreach ($values as $tinkle) {
        $value .= '<p>' . trim($tinkle, "\n") . "</p>\n";
    }

    // under certain strange conditions it could create a P of entirely whitespace
    $value = preg_replace('|<p>\s*</p>|', '', $value);
    $value = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $value);
    $value = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $value); // don't pee all over a tag
    $value = preg_replace("|<p>(<li.+?)</p>|", "$1", $value); // problem with nested lists
    $value = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $value);
    $value = str_replace('</blockquote></p>', '</p></blockquote>', $value);
    $value = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $value);
    $value = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $value);

    if ($lineBreaks) {
        $value = preg_replace_callback('/<(script|style).*?<\/\\1>/s', 'autop_newline_preservation_helper', $value);
        $value = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $value); // optionally make line breaks
        $value = str_replace('<WPPreserveNewline />', "\n", $value);
    }

    $value = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $value);
    $value = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $value);

    if (strpos($value, '<pre') !== false) {
        $value = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', 'clean_pre', $value);
    }
    $value = preg_replace("|\n</p>$|", '</p>', $value);

    return $value;
}

/**
 * Accepts matches array from preg_replace_callback in wpautop() or a string.
 *
 * Ensures that the contents of a <<pre>>...<</pre>> HTML block are not
 * converted into paragraphs or line-breaks.
 *
 *
 * @param array|string $matches The array or string
 *
 * @return string The pre block without paragraph/line-break conversion.
 */
function clean_pre($matches)
{
    if (is_array($matches)) {
        $text = $matches[1] . $matches[2] . "</pre>";
    } else {
        $text = $matches;
    }

    $text = str_replace(['<br />', '<br/>', '<br>'], ['', '', ''], $text);
    $text = str_replace('<p>', "\n", $text);
    $text = str_replace('</p>', '', $text);

    return $text;
}

/**
 * Newline preservation help function for wpautop
 *
 * @since  3.1.0
 * @access private
 *
 * @param array $matches preg_replace_callback matches array
 *
 * @returns string
 */
function autop_newline_preservation_helper($matches)
{
    return str_replace("\n", "<PreserveNewline />", $matches[0]);
}

function months_translated_list()
{
    $out = [];

    foreach ($months = range(1, 12, 1) as $month) {
        $out[] = [
            'month' => (int) $month,
            'name' => \Carbon\Carbon::createFromDate(null, $month, null)->formatLocalized('%b'),
        ];
    }

    return $out;
}

function time_to_mins($time)
{
    return (new TimeToMins($time))->convert();
}

function mins_to_time($mins)
{
    return (new MinsToTime($mins))->convert();
}

function normalize_time($time)
{
    list($h, $m) = explode(':', $time);
    list($h, $m) = $this->zeroPaddify($h, $m);

    return "{$h}:{$m}";
}

function md_chips($collection)
{
    $collection = collection_to_array($collection);

    return !empty($collection) ? '<md-chips><md-chip>' . join('</md-chip><md-chip>', $collection) . '</md-chip></md-chips>' : '';
}

/**
 * @param $collection
 * @return mixed
 */
function collection_to_array($collection)
{
    if (is_object($collection) && method_exists($collection, 'toArray')) {
        $collection = $collection->toArray();

        return $collection;
    }

    return $collection;
}

function join_collection($collection, $sep = ', ')
{
    return join($sep, collection_to_array($collection));
}

function download_tnf_token(User $user, UserHistory $record)
{
    return md5("user.download.tnf.{$user->id}.{$record->id}");
}

function debugMessage($data)
{
    $log = "/*" . str_repeat('=', 10) . " " . gmdate("YmdHis") . " " . str_repeat('=', 10) . "*/" . PHP_EOL;
    $log .= json_encode($data);
    $log .= PHP_EOL;

    return $log;
}

function show_synevo_info_for_user($user)
{
    return 1 === (int) $user->site_id;
}

/**
 * Build Sites filtering control
 *
 * @param Closure|null $query
 * @return array
 */
function sites_control(\Closure $query = null)
{
    return [
        'label' => 'Site',
        'type' => 'select',
        'value' => function () {
            return Request::get('site_id', site_id());
        },
        'options' => function () {
            return Site::lists('name', 'id')->toArray();
        },
        'query' => $query ?: null,
    ];
}

/**
 * When accepting or refunding a payment from backend,
 * the right payment data should be used for that purpose.
 * Use this method to re-assign config values providing the right config path and key.
 *
 * @param $path
 * @param $key
 */
function mergeConfig($path, $key)
{
    $config = app('config')->get($key, []);

    app('config')->set($key,
        array_merge($config, require $path)
    );
}

/**
 * Re-assign order specific payment data if payment was produced by site-clone.
 *
 * @param $siteId
 */
function switchToSitePaymentData($siteId)
{
    if ('admin' == app('request')->segment(1) && $siteId != site_id()) {
        mergeConfig(
            config_path("vb" . $siteId . '.php'),
            'vb'
        );
    }
}

function Label($id, $lang_id = 1)
{
    $label = MBlockText::where('id', $id)
            ->where('lang_id', $lang_id)
            ->first();
    if (is_null($label)) {
        return "";
    }
    return $label->body;
}

function Image($id)
{
    $label = MBlockImage::where('id', $id)
            ->first();
    if (is_null($label)) {
        return "";
    }

    return $label->img;
}

function getMonthList($user_id){
    setlocale(LC_TIME, 'ro_RO');
    $term = MRationTerm::where('user_id', $user_id)->first();

    if (is_null($term)) {
        return false;
    }

    $begin = new DateTime($term->term_from);
    $end = new DateTime($term->term_to);

    $months = [];
    $years = [];

    while ($begin <= $end) {
        if (!in_array($begin->format('m'), $months)) {
            $months[] = $begin->format('m');
        }
        if (!in_array($begin->format('Y'), $years)) {
            $years[] = $begin->format('Y');
        }

        $begin->modify('first day of next month');
    }

    return  [
        'years' => $years,
        'months' => $months,
    ];
}

function getSubscrEnd()
{
    $subscr = MUserSubscrs::where('user_id', Auth::user()->id)
                        ->first();
    return $subscr;
}

function isValidSubscr(){
    $subscr = MUserSubscrs::where('user_id', Auth::user()->id)
                        ->first();
    if (!is_null($subscr)) {
        if (strtotime(date('Y/m/d')) < strtotime($subscr->end)) {
            return true;
        }
    }
    return false;
}

