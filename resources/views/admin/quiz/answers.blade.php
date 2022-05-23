@inject('hints', 'quiz_hints')
@inject('attached', 'quiz_attached_hints')

@if ($answers->count())
    <ul class="list-unstyled">
        @foreach($answers as $answer)
        <li>
            <label class="label label-info">{{ $answer->answer }}</label>

            <div class="callout callout-info" style="margin-bottom: 10px;">
            @foreach($hints as $hint)
                <?php $name = $question->id .'_'. $answer->id .'_'. $hint->id; ?>
                <label for="hint_{{ $name }}" style="display: inline-block; width: 80px;" title="{{ $hint->hint }}">
                    <input type="checkbox" {{ (in_array($name, $attached) ? 'checked="checked"' : '') }} class="simple" name="{{ $name }}" id="hint_{{ $name }}" />
                    {{ $hint->code }}
                </label>
            @endforeach
            </div>
        </li>
        @endforeach
    </ul>
@endif
