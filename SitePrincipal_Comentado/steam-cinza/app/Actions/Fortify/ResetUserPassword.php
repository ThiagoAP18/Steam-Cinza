<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    // Importa o trait que contém as regras de validação de senha
    use PasswordValidationRules;

    /**
     * Valida e redefine a senha esquecida do usuário.
     *
     * @param  User  $user  Usuário que está redefinindo a senha
     * @param  array<string, string>  $input  Dados enviados pelo formulário (nova senha)
     */
    public function reset(User $user, array $input): void
    {
        // Valida o campo de senha usando as regras do PasswordValidationRules
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        // Atualiza a senha do usuário e salva no banco
        $user->forceFill([
            'password' => Hash::make($input['password']), // Criptografa a senha
        ])->save();
    }
}
