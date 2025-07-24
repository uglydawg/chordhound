@extends('layouts.app')

@section('content')
    <livewire:math-chord-test />
@endsection

@push('scripts')
    <script src="{{ asset('js/multi-instrument-player.js') }}"></script>
@endpush