<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource; 
use App\Http\Resources\EntrepriseResource; 
use App\Models\Profil;
use App\Models\Client;
use App\Models\Entreprise;
use App\Models\Candidat;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class DashboardController extends Controller
{

    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'unique' => 'Le  champ :attribute est déjà utilisé.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'in' => 'Le champ :attribute doit être l\'une des valeurs suivantes : :values.',
    ];

    // get stats total of users, entreprises and profils
    // period can be  'today', 'this_month', 'this_week', 'this_year','all'
    public function stats($period = 'this_month')
    {   
        // get stats by period created at 
        // add 0 to start if count less than 10
        $users = User::all()->count();
        $users = $users < 10 ? '0'.$users : $users;
        $entreprises = Entreprise::all()->count();
        $entreprises = $entreprises < 10 ? '0'.$entreprises : $entreprises;
        $profils = Profil::all()->count();
        $profils = $profils < 10 ? '0'.$profils : $profils;
        $clients = Client::all()->count();
        $clients = $clients < 10 ? '0'.$clients : $clients;
        // candidats
        $candidats = Candidat::all()->count();
        $candidats = $candidats < 10 ? '0'.$candidats : $candidats;
        // offers with status open
        $open_offers = Offer::where('status', 'open')->get()->count();
        $open_offers = $open_offers < 10 ? '0'.$open_offers : $open_offers;
        // offers with status close
        $close_offers = Offer::where('status', 'close')->get()->count();
        $close_offers = $close_offers < 10 ? '0'.$close_offers : $close_offers;
        return response()->json([
            'users' => $users,
            'entreprises' => $entreprises,
            'profils' => $profils,
            'clients'=>$clients,
            'candidats'=>$candidats,
            'open_offers'=>$open_offers,
            'close_offers'=>$close_offers
        ], 200);
    }

    // get last 5 entreprises and profils 
    public function last_data()
    {
        $entreprises = Entreprise::orderBy('created_at', 'desc')->take(5)->get();
        $profils = Profil::orderBy('created_at', 'desc')->take(5)->get();
        return response()->json(['entreprises' => EntrepriseResource::collection($entreprises), 'profils' => ProfilResource::collection($profils)], 200);
    }

    public function getPeriod($period){
        $now = now();
        switch ($period) {
            case 'today':
                return [$now->startOfDay(), $now->endOfDay()];
                break;
            case 'this_month':
                return [$now->startOfMonth(), $now->endOfMonth()];
                break;
            case 'this_week':
                return [$now->startOfWeek(), $now->endOfWeek()];
                break;
            case 'this_year':
                return [$now->startOfYear(), $now->endOfYear()];
                break;
            case 'all':
                return [now()->subYears(10), now()];
                break;
            default:
                return [$now->startOfMonth(), $now->endOfMonth()];
                break;
        }
        
    }

    

}