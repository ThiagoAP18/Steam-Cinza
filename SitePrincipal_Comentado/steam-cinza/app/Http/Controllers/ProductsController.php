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
    // Página inicial — lista jogos e menor preço de compra/aluguel
    public function index()
    {
        $games = Game::all();

        // Busca o menor preço de compra entre licenças disponíveis
        $minBuyPrice = License::where('status', 'available')->whereNotNull('price')->min('price');

        // Busca o menor preço de aluguel entre licenças disponíveis
        $minRentPrice = License::where('status', 'available')->whereNotNull('rent_price')->min('rent_price');

        return view('welcome', ['games' => $games, 'minBuyPrice' => $minBuyPrice, 'minRentPrice' => $minRentPrice]);
    }

    // Função de pesquisa
    public function search(){
        $search = request('search');

        // Busca jogos cujo nome contém a expressão digitada
        $games = Game::where('name_game', 'like', '%'. $search. "%")->get();

        $minBuyPrice = License::where('status', 'available')->whereNotNull('price')->min('price');
        $minRentPrice = License::where('status', 'available')->whereNotNull('rent_price')->min('rent_price');
        
        return view('search', ['games' => $games, 'search' => $search, 'minRentPrice' => $minRentPrice, 'minBuyPrice' => $minBuyPrice]);
    }

    // Exibe detalhes de um jogo
    public function show($id){
        $game = Game::findOrFail($id);
        
        $hasGame = false;

        // Verifica se o usuário já comprou o jogo
        if(Auth::check()){
            $hasGame = License::where('user_id', Auth::id())
                              ->where('game_id', $game->id)
                              ->where('status', 'sold')
                              ->exists();
        }

        // Busca nome da publicadora
        $publisher = User::where('id', $game->publisher_id)->first();
        $publisherName = $publisher ? $publisher->name : 'Desconhecido';

        // Licenças disponíveis daquele jogo organizadas por preço
        $licenses = License::where('game_id', $game->id)
                           ->where('status', 'available')
                           ->with('user')
                           ->orderBy('price', 'asc')
                           ->get();

        // Divide licenças entre as do publisher e as de outros usuários
        $commonLicenses = $licenses->where('user_id', '!=', $game->publisher_id);
        $publisherLicenses = $licenses->where('user_id', $game->publisher_id);

        return view('games.show', [
            'game' => $game,
            'commonLicenses' => $commonLicenses,
            'publisherLicenses' => $publisherLicenses,
            'publisher' => $publisherName,
            'hasGame' => $hasGame
        ]);
    }

    // Criação de jogo pelo publisher
    public function store(Request $request){
        $user = auth()->user();

        // Somente publisher pode criar jogo
        if($user->type != "publisher"){
            abort(403, 'Acesso não autorizado!');
        }

        // Validação dos campos
        $request->validate([
            'name_game' => 'required|string',
            'dt_launch' => 'required|date',
            'initial_quantity' => 'required|integer|min:1',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $game = new Game;

        $game->name_game = $request->name_game;
        $game->dt_launch = $request->dt_launch;
        $game->initial_quantity = $request->initial_quantity;
        $game->actual_quantity = $game->initial_quantity;
        $game->description = $request->description;
        $game->publisher_id = $user->id;
        $price = $request->price;
        $game->initial_price = $price;

        // Upload da imagem
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . "_" . strtotime("now")). "." . $extension;
            $requestImage->move(public_path("img/games"), $imageName);
            $game->image = $imageName;
        }

        $game->save();

        // Criação das licenças iniciais
        $licensesData = [];
        $quantity = (int)$request->initial_quantity;

        for($i = 0; $i < $quantity; $i++){
            $licensesData[] = [
                'game_id' => $game->id,
                'user_id' => $user->id,
                'license_key' => strtoupper(Str::random(16)),
                'price' => $price,
                'rent_price' => null,
                'status' => 'available',
                'buy' => true,
                'rent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        License::insert($licensesData);

        return redirect('dashboard')->with('msg', 'Jogo e '. $quantity .' licenças adicionadas com sucesso')->with('type', 'success');
    }

    // Tela de adicionar saldo
    public function addFunds(){
        $user = auth()->user();

        return view('profile.addfunds', ['user' => $user]);
    }

    // Atualiza o saldo do usuário
    public function updateFunds(Request $request){
        $user = auth()->user();
        $user->cash += $request->amount;
        $user->save();

        return redirect('/')->with('msg', 'Saldo atualizado com sucesso!')->with('type', 'success');
    }

    // Dashboard (Se for jogador, mostra biblioteca. Se for publisher, mostra jogos publicados)
    public function dashboard(){
        $user = auth()->user();

        $games = [];
        $userLicenses = [];
        $rentedLicenses = [];
        $boughtLicenses = [];

        $pageTitle = ($user->type == 'publisher') ? 'Painel da Publicadora' : 'Minha Biblioteca';

        if($user->type == 'common'){
            $userLicenses = License::where('user_id', $user->id)
                                   ->where('status', 'sold')
                                   ->with('game')
                                   ->get();

            $rentedLicenses = $userLicenses->where('rent', true);
            $boughtLicenses = $userLicenses->where('buy', true);
        }
        else if($user->type == 'publisher'){
            $games = Game::where('publisher_id', $user->id)->get();
        }

        return view('/dashboard', [
            'rentedLicenses' => $rentedLicenses,
            'boughtLicenses' => $boughtLicenses,
            'games' => $games,
            'pageTitle' => $pageTitle,
            'user' => $user
        ]);
    }

    // Página de criação de jogo
    public function createGame(){
        $user = auth()->user();

        if($user->type != 'publisher'){
            return redirect('/')->with('msg', "Apenas publicadoras podem acessar essa página")->with('type', 'danger');;
        }

        return view('games.create', ['user' => $user]);
    }

    // Compra de jogo
    public function buy($id){
        $user = auth()->user();
        $license = License::with('game')->findOrFail($id);
        $game = $license->game;
        
        // Restrições
        if($user->type == 'publisher'){
            return redirect('/')->with('msg', 'Apenas jogadores podem comprar jogos!')->wih('msg', 'danger');
        }
        if($license->status != 'available'){
            return redirect('/')->with('msg', 'Licença indisponível para compra!')->with('msg', 'danger');
        }
        if($user->cash < $license->price){
            $errorMessage = "Saldo insuficiente para concluir a compra! <a href='/addfunds' class='error_message_cash'>Clique aqui para adicionar fundos.</a>";
            return redirect()->back()->with('msg', $errorMessage)->with('type', 'danger');
        }

        // Transação
        try{
            DB::beginTransaction();

            $user->cash -= $license->price;

            $seller = User::findOrFail($license->user_id);
            $publisher = User::findOrFail($game->publisher_id);

            if($seller){
                // 50% vai pro vendedor
                $seller->cash += (0.5 * $license->price);

                // Se o vendedor for publisher, reduz estoque
                if($seller->type == 'publisher'){
                    $game = Game::findOrFail($license->game_id);
                    $game->actual_quantity -= 1;
                    $game->save();
                }
                else{
                    // Senão, 45% vai para a publicadora
                    if($publisher){
                        $publisher->cash += (0.45 * $license->price);
                        $publisher->save();
                    }
                }

                $seller->save();
            }

            // Transfere propriedade da licença
            $license->last_owner_id = $license->user_id;
            $license->user_id = $user->id;
            $license->status = 'sold';
            $license->rent = false;

            $license->save();
            $user->save();

            DB::commit();
            
            return redirect('/dashboard')->with('msg', 'Jogo Comprado com sucesso!')->with('type', 'sucess');
        }
        catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('msg', 'Erro ao processar a compra! Tente novamente.')->with('type', 'danger');
        }
    }

    // Aluguel de jogo
    public function rent($id){
        $user = auth()->user();
        $license = License::with('game')->findOrFail($id);
        $game = $license->game;

        if($user->type == 'publisher'){
            return redirect('/')->with('msg', 'Apenas jogadores podem alugar jogos!')->with('msg', 'danger');
        }
        if($license->status != 'available'){
            return redirect('/')->with('msg', 'Licença indisponível para aluguel!')->with('msg', 'danger');
        }
        if($user->cash < $license->rent_price){
            $errorMessage = "Saldo insuficiente para fazer aluguel! <a href='/addfunds' class='error_message_cash'>Clique aqui para adicionar fundos.</a>";
            return redirect()->back()->with('msg', $errorMessage)->with('type','danger');
        }
        
        try{
            DB::beginTransaction();

            // Cobra o aluguel
            $user->cash -= $license->rent_price;
            $user->save();

            $seller = User::findOrFail($license->user_id);
            $publisher = User::findOrFail($game->publisher_id);

            // Pagamentos
            if($seller){
                $seller->cash += (0.45 * $license->rent_price);
                $seller->save();
            }
            if($publisher){
                $publisher->cash += (0.45 * $license->rent_price);
                $publisher->save();
            }

            // Atualiza licença
            $license->last_owner_id = $seller->id;
            $license->user_id = $user->id;
            $license->status = 'sold';
            $license->buy = false;
            $license->rented_at = now();
            $license->rent_expires_at = $license->rented_at->copy()->addDays($license->rent_time);
            $license->save();

            DB::commit();
            return redirect("/dashboard")->with('msg', 'Aluguel feito com sucesso!')->with('type', 'sucess');
        }
        catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('msg', 'Erro ao processar o aluguel! Tente novamente.')->with('type', 'danger');
        }
    }

    // Edita jogo (apenas publisher dono)
    public function edit($id){
        $user = auth()->user();
        $game = Game::findOrFail($id);

        if($user->id != $game->publisher_id){
            return redirect('/')->with('msg', 'Você não é o dono desse jogo!')->with('type', 'danger');
        }

        return view('games.edit', ['game' => $game]);
    }

    // Atualiza jogo
    public function update(Request $request){
        $user = Auth::user();
        
        $game = Game::findOrFail($request->id);

        if($user->id != $game->publisher_id){
            abort(403, 'Você não tem permissão para editar este jogo.');
        }

        // Atualiza imagem
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . "_" . strtotime("now")). "." . $extension;
            
            $requestImage->move(public_path("img/games"), $imageName);
            $game->image = $imageName;
        }

        // Atualiza dados básicos
        $game->name_game = $request->name_game;
        $game->description = $request->description;
        $game->dt_launch = $request->dt_launch;
        
        // Se o preço mudou, atualiza todas as licenças disponíveis
        if($request->price != $game->initial_price) {
            License::where('game_id', $game->id)
                   ->where('status', 'available')
                   ->update(['price' => $request->price]);

            $game->initial_price = $request->price;
        }

        // Controle de quantidade de licenças
        $currentAvailable = License::where('game_id', $game->id)
                                   ->where('status', 'available')
                                   ->count();
        
        $targetQuantity = (int)$request->new_quantity;

        // Se aumentar quantidade
        if($targetQuantity > $currentAvailable){
            $toAdd = $targetQuantity - $currentAvailable;
            $licensesData = [];
            $priceToUse = $request->price ?? $game->licenses()->first()->price ?? 0;

            for($i = 0; $i < $toAdd; $i++){
                $licensesData[] = [
                    'game_id' => $game->id,
                    'user_id' => $user->id,
                    'license_key' => strtoupper(Str::random(16)),
                    'price' => $priceToUse, 
                    'rent_price' => null,
                    'status' => 'available',
                    'buy' => true,
                    'rent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            License::insert($licensesData);
        }
        // Se reduzir quantidade
        else if($targetQuantity < $currentAvailable){
            $toRemove = $currentAvailable - $targetQuantity;

            $licensesToDelete = License::where('game_id', $game->id)
                                       ->where('user_id', $user->id)
                                       ->where('status', 'available')
                                       ->limit($toRemove)
                                       ->pluck('id');
            
            if($licensesToDelete->isNotEmpty()){
                License::destroy($licensesToDelete);
            }
        }
        
        $game->actual_quantity = $request->new_quantity;

        $game->save();

        return redirect('/dashboard')->with('msg', 'Jogo editado com sucesso!')->with('type', 'success');
    }

    // Anunciar licença (usuário comum)
    public function announce(Request $request){
        $user = Auth::user();

        if($user->type == 'publisher'){
            return redirect('/')->with('msg', 'Você não tem acesso a essa função!')->with('type', 'danger');
        }

        $license = License::findOrFail($request->license_id);
        
        if($license->user_id != $user->id){
            return redirect()->back()->with('msg', 'Você não tem acesso à essa licença!')->with('type', 'danger');
        }
        
        if($license->status != 'sold'){
            return redirect()->back()->with('msg', 'Esta licença já foi anunciada!')->with('type', 'danger');
        }

        if(!$request->has('enable_sale') && !$request->has('enable_rent')){
            return redirect()->back()->with('msg', 'Selecione pelo menos uma modalidade (Venda ou Aluguel).')->with('type', 'danger');
        }

        // Configurações de aluguel
        if($request->has('enable_rent')){
            $license->rent = true;
            $license->rent_price = $request->rent_price;
            $license->rent_time = $request->rental_days; 
        } else {
            $license->rent = false;
            $license->rent_price = null;
            $license->rent_time = null;
        }

        // Configurações de venda
        if($request->has('enable_sale')){
            $license->buy = true;
            $license->price = $request->sale_price;
        } else {
            $license->buy = false;
            $license->price = null;
        }

        $license->status = 'available';
        $license->save();

        return redirect('/dashboard')->with('msg', 'Jogo anunciado na loja com sucesso!')->with('type', 'success');
    }

    // Devolver jogo alugado
    public function rent_return($id){
        $user = Auth::user();
        $license = License::findOrFail($id);

        if($license->user_id != $user->id){
            return redirect('/');
        }

        // Devolve para o dono anterior
        if ($license->last_owner_id) {
            $license->user_id = $license->last_owner_id; 
        } 
        
        $license->last_owner_id = null; 
        $license->rent_expires_at = null; 
        $license->rent = false; 
        $license->buy = true;
        $license->status = 'sold'; 

        $license->save();

        return redirect('/')->with('msg', 'Jogo devolvido com sucesso!')->with('type', 'sucess');
    }
}
