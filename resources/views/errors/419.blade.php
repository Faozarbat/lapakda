@extends('errors.layout')

@section('kode', '419')
@section('title', 'Halaman sudah kedaluwarsa')
@section('message', 'Kamu terlalu lama membiarkan halaman ini terbuka. Muat ulang halaman lalu coba lagi.')

@section('aksi')
    <button type="button" class="btn btn-utama" onclick="location.reload()">Muat Ulang Halaman</button>
@endsection
