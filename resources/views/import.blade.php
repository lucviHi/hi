@extends('layouts.app')

@section('content')
    <h2>Upload File Excel</h2>
    <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
@endsection