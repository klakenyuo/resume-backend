@extends('emails.layouts.layout')

@section('content')
    <p>Bonjour </p>
    <p>Vous avez demandé un nouveau mot de passe pour votre compte sur <a href="https://ubbfy.com">ubbfy.com</a>.</p>
    <p>Pour réinitialiser votre mot de passe, voici votre code:</p>
    <p>{{ $code }}</p>
    <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, vous pouvez ignorer ce message.</p>
@endsection
 