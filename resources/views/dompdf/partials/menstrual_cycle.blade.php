@inject('mCycle', 'App\Services\MenstrualCycle')

<?php
list($start, $p1, $p2, $p3) = $mCycle->parse(
        $record->menstrual_cycle['start_date'],
        $record->menstrual_cycle['duration'],
        $record->date()
);
?>
<table width="100%" class="unbreakable">
    <tr>
        <td align="center">
            <h5>{{ trans('result.labels.menstrual_cycle_scheme') }}</h5>
            <div id="labels" style="position: relative; top: 98px; left:8px;">
                <span style="width: 46px; margin-left: -67px;" class="i-block a-center">{{ $start->format('d.m') }}</span>
                <span style="width: 46px;" class="i-block a-center">{{ $p1->format('d.m') }}</span>
                <span style="width: 46px; margin-left: 97px;" class="i-block a-center">{{ $p2->format('d.m') }}</span>
                <span style="width: 46px; margin-left: 99px;" class="i-block a-center">{{ $p3->format('d.m') }}</span>
            </div>
            <img src="{{ dataBase64('images/menstrual_cycle.png') }}" width="350" alt=""><br/>
        </td>
    </tr>
</table>
