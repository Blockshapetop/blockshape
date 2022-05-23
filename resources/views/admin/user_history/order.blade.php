<div class="well text-muted">
    <ul class="list-unstyled">
        <li>
            <label>Plata prin:</label>
            {{ $order->gateway }}
        </li>
        <li>
            <label>Termen de executare:</label>
            {{ $order->period }} zile
        </li>
        <li>
            <label>Suma:</label>
            {{ $order->amount }} {{ $order->offer->site->currency }}
            @if ($order->discount)
                (-{{ $order->discount }}%)
            @endif
        </li>
        <li>
            @if ('pending' == $order->status)
                <span class="label label-warning">Neachitat</span>
            @elseif ('paid' == $order->status)
                <span class="label label-success">Achitat</span>
            @else
                <span class="label label-danger">Refuzat</span>
            @endif
        </li>
    </ul>
</div>
