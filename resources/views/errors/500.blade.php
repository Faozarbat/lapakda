@extends('errors.layout')

@section('kode', '500')
@section('title', 'Ada masalah di sisi kami')
@section('message', 'Maaf, terjadi kesalahan. Coba lagi beberapa saat lagi.')

@section('aksi')
    <button type="button" class="btn btn-utama" onclick="location.reload()">Coba Lagi</button>
@endsection
