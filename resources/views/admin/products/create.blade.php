@extends('admin.layouts.app')

@section('title', 'Tambah Produk - Admin FunFarm')

@section('content')
    <h1 class="mb-1 text-xl font-semibold text-stone-900">Tambah Produk</h1>
    <p class="mb-6 text-sm text-stone-500">Produk baru untuk marketplace FunFarm.</p>

    @include('admin.products.form', ['action' => route('admin.products.store'), 'method' => 'POST'])
@endsection
