@extends('admin.layouts.app')

@section('title', 'Ubah Produk - Admin FunFarm')

@section('content')
    <h1 class="mb-1 text-xl font-semibold text-stone-900">Ubah Produk</h1>
    <p class="mb-6 text-sm text-stone-500">{{ $product->name }} &middot; /produk/{{ $product->slug }}</p>

    @include('admin.products.form', ['action' => route('admin.products.update', $product), 'method' => 'PUT'])
@endsection
