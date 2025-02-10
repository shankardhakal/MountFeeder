@foreach($importLogs as $log)

    <div style="font-size: small" class="alert alert-{{strtolower($log['log_type'])}}" role="alert">
    <span class="font-weight-bold"> {{$log['created_at']}} {{$log['log_type']}} : {{$log['message']}} </span>
    </div>
@endforeach

