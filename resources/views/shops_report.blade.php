<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
          integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <title>Shop report</title>
</head>
<body>

<h1 class="h1"> Shop and corresponding number of products per shop</h1>

    @foreach($websiteData as  $website=>$shopsData)
        <h2 class="h2">{{$website}}</h2>
        @php($totalProducts=0)
        <ul class="list-group">
        @foreach($shopsData as $shop=>$count)
            @php($totalProducts +=$count)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $shop }}
                <span class="badge badge-primary badge-pill">{{ $count }}</span>
            </li>
        </ul>
        @endforeach
       <h2>Total products at the shop <span class="badge badge-primary">{{ $totalProducts }}</span></h2>
<hr>
<hr>
    @endforeach

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
</body>
</html>
