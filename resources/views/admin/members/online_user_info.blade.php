@if ($user->isOnline())
<span class="label label-default">Online</span><br />
@endif

@if ($orders)
    <ul class="list-unstyled">
        <li class="title" style="border-bottom: 1px solid #ccc; padding-top: 10px;">Orders</li>
        <li>
            <label>Total:</label>
            {{ $orders }}
        </li>
        <li>
            <label>Paid:</label>
            {{ $paid }}
        </li>
        <li>
            <label>Pending</label>
            {{ $pending }}
        </li>
    </ul>
@endif
