<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;
use App\Models\License;
use App\Models\User;

class CheckRentals extends Command
{
    /**
     * Nome e assinatura do comando que será executado via terminal.
     *
     * @var string
     */
    protected $signature = 'app:check-rentals';

    /**
     * Descrição do comando, exibida no Artisan.
     *
     * @var string
     */
    protected $description = 'Verifica aluguéis vencidos e devolve os jogos aos donos originais';

    /**
     * Método principal executado quando o comando é chamado.
     */
    public function handle()
    {
        // Mensagem inicial no terminal
        $this->info('Iniciando verificação de aluguéis vencidos...');

        // Busca todas as licenças alugadas cujo prazo já expirou
        $overdued = License::where('rent', true)                 // A licença está alugada
                           ->whereNotNull('rent_expires_at')     // Possui data de expiração
                           ->where('rent_expires_at', '<=', now()) // Já passou do prazo
                           ->get();

        // Se nenhuma licença estiver vencida, apenas informa e encerra
        if ($overdued->count() == 0) {
            $this->info('Nenhum aluguel vencido encontrado.');
            return;
        }

        // Para cada licença expirada encontrada
        foreach ($overdued as $license) {
            $this->info("Devolvendo licença ID: {$license->id}...");

            // Se há registro do dono anterior, devolve para ele
            if ($license->last_owner_id) {
                $license->user_id = $license->last_owner_id; 
            } 
            
            // Reseta campos da licença
            $license->last_owner_id = null;        // Remove dono temporário
            $license->rent_expires_at = null;      // Remove data de expiração
            $license->rent = false;                // Marca como não alugada
            $license->buy = true;                  // Marca como "comprada" novamente
            $license->status = 'sold';             // Atualiza status

            // Salva alterações no banco de dados
            $license->save();
        }

        // Mensagem final informando quantas licenças foram processadas
        $this->info("Processo finalizado. {$overdued->count()} jogos devolvidos.");
    }
}
