
@extends('emails.layouts.layout')

@section('content')

<p>Bienvenue sur Ubbfy {{ $user->pseudo }} !</p>   

<p>Nous sommes heureux de vous compter parmi nos membres ! </p> 

<p>Code de vérification  : <br></p>
<p><strong> {{ $user->verification_code }} </strong> </p>

@endsection

                                        