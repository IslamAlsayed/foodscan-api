{{-- @php
$baseUrl = url()->current();
$queryString = http_build_query($queryParameters);
$fullUrl = $queryString ? "{$baseUrl}?{$queryString}" : $baseUrl;
@endphp
<a href="{{ $fullUrl }}">Parameters</a> --}}

{{-- <p>current {{url()->current()}}</p>
<p>full {{url()->full()}}</p>
<a href="{{ url()->full() }}">Parameters</a> --}}


{{-- name= {{$name}} --}}

{{-- @php
$baseUrl = url()->current();
echo $baseUrl;
$queryString = http_build_query($queryParameters);
print_r($queryString);
// $fullUrl = $queryString ? "{$baseUrl}?{$queryString}" : $baseUrl;
// echo $fullUrl;
@endphp --}}

{{-- @php
$baseUrl = url()->current();
$queryParameters = request()->query();
$queryString = http_build_query($queryParameters);
@endphp

<a href="{{'/uniland/proprieties2/' . $queryString }}">querys</a> --}}
{{http_build_query(request()->query())}}

<a href="{{ url()->current() . '?' . http_build_query(request()->query()) }}">querys</a>