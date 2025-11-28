<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;
use App\Models\License;
use App\Models\User;

class CheckRentals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-rentals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica aluguéis vencidos e devolve os jogos aos donos originais';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificação de aluguéis vencidos...');

        $overdued = License::where('rent', true)
                           ->whereNotNull('rent_expires_at')
                           ->where('rent_expires_at', '<=', now())
                           ->get();

        if ($overdued->count() == 0) {
            $this->info('Nenhum aluguel vencido encontrado.');
            return;
        }

        foreach ($overdued as $license) {
            $this->info("Devolvendo licença ID: {$license->id}...");

            if ($license->last_owner_id) {
                $license->user_id = $license->last_owner_id; 
            } 
            
            $license->last_owner_id = null; 
            $license->rent_expires_at = null; 
            $license->rent = false; 
            $license->buy = true;
            $license->status = 'sold'; 

            $license->save();
        }

        $this->info("Processo finalizado. {$overdued->count()} jogos devolvidos.");
    }
}