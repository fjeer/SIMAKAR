@extends('layouts.app')

@section('title', 'Dashboard Admin - SIMAKAR')

@section('content')
    <h1 class="text-2xl font-bold">Dashboard {{ ucfirst(Auth::user()->peran) }}</h1>
    <!-- Nanti komponen datatables/form masuk ke sini -->
@endsection
