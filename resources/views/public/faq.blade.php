@extends('layouts.lms', ['title' => 'FAQ - Trama Verse'])

@section('content')
    @include('partials.faq-section', [
        'faqMode' => 'page',
        'faqTitle' => "FAQ's",
        'faqOpenFirst' => true,
    ])
@endsection
