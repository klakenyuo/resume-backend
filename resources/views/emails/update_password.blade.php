@extends('emails.layouts.layout')

@section('content')
<p> Bonjour {{ $user->pseudo }},</p>
<p>Votre mot de passe a été réinitialisé avec succès.</p>
@endsection