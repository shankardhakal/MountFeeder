<ul>
   @php($totalFeeds =0)
@foreach($websites as $website)
    <li class="list-group-item d-flex justify-content-between align-items-center">
     <a href="{{url()->current()}}/websites/{{$website->id}}/feeds"> {{ $website->name }} </a>
        <span class="badge badge-primary badge-pill">{{ $website->feeds->count() }}</span>
        @php( $totalFeeds += $website->feeds->count() )
    </li>
@endforeach
</ul>

<div><h2 class="h2">Total websites {{ count($websites) }}</h2></div>
<div><h2 class="h2"> Total feeds {{ $totalFeeds }}</h2></div>

