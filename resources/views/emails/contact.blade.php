@extends('emails.layouts.layout')

@section('content')
    <p>Bonjour,</p>
    <p>Nous avons reçu une nouvelle demande de contact pour votre compte sur <a href="https://ubbfy.com">ubbfy.com</a>.</p>
    <p>Pour répondre à cette demande, voici les informations que nous avons reçues:</p>
    <p>Nom: {{ $data['name'] }}</p>
    <p>Email: {{ $data['email'] }}</p>
    <p>Message:</p>
    <p>{{ $data['message'] }}</p>
    <p>Si vous n'avez pas demandé de contact, vous pouvez ignorer ce message.</p>
@endsection