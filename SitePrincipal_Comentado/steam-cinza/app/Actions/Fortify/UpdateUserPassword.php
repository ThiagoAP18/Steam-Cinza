<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    // Importa o trait que contém as regras de validação de senha
    use PasswordValidationRules;

    /**
     * Valida e atualiza a senha do usuário.
     *
     * @param  User  $user  Usuário que está atualizando a senha
     * @param  array<string, string>  $input  Dados enviados pelo formulário (senha atual e nova senha)
     */
    public function update(User $user, array $input): void
    {
        // Valida os campos
        Validator::make(
            $input,
            [
                'current_password' => ['required', 'string', 'current_password:web'], // Verifica se a senha atual está correta
                'password' => $this->passwordRules(), // Valida a nova senha conforme as regras do trait
            ],
            [
                'current_password.current_password' => __('The provided password does not match your current password.'), // Mensagem personalizada de erro
            ]
        )->validateWithBag('updatePassword'); // Valida usando um "bag" específico para erros de senha

        // Atualiza a senha do usuário no banco
        $user->forceFill([
            'password' => Hash::make($input['password']), // Criptografa a nova senha
        ])->save();
    }
}
