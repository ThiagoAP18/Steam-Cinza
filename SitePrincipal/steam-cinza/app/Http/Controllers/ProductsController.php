<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function index()
    {
        $games = Game::all();

        $minBuyPrice = License::where('status', 'available')->whereNotNull('price')->min('price');
        $minRentPrice = License::where('status', 'available')->whereNotNull('rent_price')->min('rent_price');

        return view('welcome', ['games' => $games, 'minBuyPrice' => $minBuyPrice, 'minRentPrice' => $minRentPrice]);
    }

    public function search(){
        $search = request('search');
        $games = Game::where('name_game', 'like', '%'. $search. "%")->get();

        $minBuyPrice = License::where('status', 'available')->whereNotNull('price')->min('price');
        $minRentPrice = License::where('status', 'available')->whereNotNull('rent_price')->min('rent_price');
        
        return view('search', ['games' => $games, 'search' => $search, 'minRentPrice' => $minRentPrice, 'minBuyPrice' => $minBuyPrice]);
    }

    public function show($id){
        $game = Game::findOrFail($id);
        
        $hasGame = false;

        if(Auth::check()){
            $hasGame = License::where('user_id', Auth::id())->where('game_id', $game->id)->exists();
        }

        $publisher = User::where('id', $game->publisher_id)->first();
        
        if(!$publisher) {
            $publisherName = 'Desconhecido';
        } else {
            $publisherName = $publisher->name;
        }

        $licenses = License::where('game_id', $game->id)->where('status', 'available')->with('user')->orderBy('price', 'asc')->get();

        return view('games.show', ['game' => $game, 'licenses' => $licenses, 'publisher' => $publisherName, 'hasGame' => $hasGame]);
    }

    public function store(Request $request){
        $user = auth()->user();

        if($user->type != "publisher"){
            abort(403, 'Acesso não autorizado!');
        }

        $game = new Game;

        $game->name_game = $request->name_game;
        $game->dt_launch = $request->dt_launch;
        $game->initial_quantity = $request->initial_quantity;
        $game->description = $request->description;
        $game->publisher_id = $user->id;

        if($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . "_" . strtotime("now")). "." . $extension;
            
            $requestImage->move(public_path("img/games"), $imageName);
            
            $game->image = $imageName;
        }

        $game->save();

        $licensesData = [];
        $quantity = (int)$request->initial_quantity;
        $price = $request->price;

        for($i = 0; $i < $quantity; $i++){
            $licensesData[] = [
                'game_id' => $game->id,
                'user_id' => $user->id,
                'license_key' => strtoupper(Str::random(16)),
                'price' => $price,
                'rent_price' => null,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        License::insert($licensesData);

        return redirect('dashboard')->with('msg', 'Jogo e '. $quantity .' licenças adicionadas com sucesso');
    }

    public function addFunds(){
        $user = auth()->user();

        return view('profile.addfunds', ['user' => $user]);
    }

    public function updateFunds(Request $request){
        $user = auth()->user();
        $user->cash += $request->amount;
        $user->save();

        return redirect('/')->with('msg', 'Saldo atualizado com sucesso!');
    }

    public function dashboard(){
        $user = auth()->user();

        $games = [];
        $userLicenses = [];
        $buyedExists = false;
        $rentExists = false;
        $userProperty = "";
        $rentedLicenses = [];
        $boughtLicenses = [];

        $pageTitle = ($user->type == 'publisher') ? 'Painel da Publicadora' : 'Minha Biblioteca';


        if($user->type == 'common'){
            $userLicenses = License::where('user_id', $user->id)->where('status', 'sold')->with('game')->get();
            $rentedLicenses = $userLicenses->where('rent', true);
            $boughtLicenses = $userLicenses->where('buy', true);
        }
        else if($user->type == 'publisher'){
            $games = Game::where('publisher_id', $user->id)->get();
        }

        return view('/dashboard', ['rentedLicenses' => $rentedLicenses, 'boughtLicenses' => $boughtLicenses, 'games' => $games, 'pageTitle' => $pageTitle, 'user' => $user]);
    }

    public function createGame(){
        $user = auth()->user();

        if($user->type != 'publisher'){
            return redirect('/')->with('msg', "Apenas publicadoras podem acessar essa página");
        }

        return view('games.create', ['user' => $user]);
    }
}
