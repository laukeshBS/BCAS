@extends('errors.errors_layout')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<style>
  .notf-wraper {
    background: #293264;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #fff;
}
.notf-wraper a {
    background: #fff;
    padding: 10px 30px;
    display: inline-block;
    border-radius: 4px;
    font-size: 17px;
    color: #293264;
    font-weight: 600;
    line-height: 20px;
}
.notf-wraper h2 {
    font-size: 50px;
}
</style>
@section('title')
404 - Page Not Found
@endsection

@section('error-content')
    <h2>404</h2>
    <p>Sorry ! Page Not Found !</p>
    {{-- <a href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
    <a href="{{ route('admin.login') }}">Login Again !</a> --}}
@endsection
