<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers; // Interface que define a ação de deletar usuários

class DeleteUser implements DeletesUsers
{
    /**
     * Deleta o usuário fornecido.
     */
    public function delete(User $user): void
    {
        // Remove a foto de perfil do usuário
        $user->deleteProfilePhoto();

        // Remove todos os tokens de API associados ao usuário
        $user->tokens->each->delete();

        // Deleta o usuário do banco de dados
        $user->delete();
    }
}
