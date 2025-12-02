<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    // Trait que contém regras de validação de senha usadas pelo Fortify
    use PasswordValidationRules;

    /**
     * Valida e cria um novo usuário registrado.
     *
     * @param  array<string, string>  $input  Dados enviados pelo formulário de registro
     */
    public function create(array $input): User
    {
        // Validação dos campos recebidos
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],                // Nome obrigatório
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Email válido e único
            'password' => $this->passwordRules(),                      // Regras de senha definidas pelo Fortify
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()    // Se termos estão habilitados no Jetstream
                        ? ['accepted', 'required']                     // Usuário deve aceitar os termos
                        : '',                                          // Caso contrário, ignora
        ])->validate();

        // Cria o usuário com os dados validados
        return User::create([
            'name' => $input['name'],                 // Nome do usuário
            'email' => $input['email'],               // Email do usuário
            'password' => Hash::make($input['password']), // Senha criptografada
            'type' => $input['type'],                 // Tipo do usuário (common ou publisher)
        ]);

        
        return redirect('/dashboard');
    }
}
