@php
    $badgeMap = ['completed' => 'success', 'pending' => 'warning', 'failed' => 'danger', 'cancelled' => 'secondary', 'refunded' => 'info'];
@endphp
@foreach($orderStatusCounts as $status => $count)
    <div class="col-4">
        <span class="badge badge-pill badge-{{ $badgeMap[$status] ?? 'secondary' }} d-inline-block mb-1 text-capitalize">{{ $status }}</span>
        <div class="font-weight-bold h5">{{ $count }}</div>
    </div>
@endforeach
