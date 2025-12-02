<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Trait que permite autenticação via tokens de API (Sanctum)
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    // Permite a criação de instâncias via factories em testes
    use HasFactory;

    // Trait Jetstream para gerenciar fotos de perfil
    use HasProfilePhoto;

    // Permite envio de notificações ao usuário
    use Notifiable;

    // Implementa autenticação de dois fatores (2FA)
    use TwoFactorAuthenticatable;

    /**
     * Atributos que podem ser preenchidos via mass assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',      // Nome do usuário
        'email',     // Email do usuário
        'password',  // Senha do usuário
        'type'       // Tipo do usuário (publisher ou common)
    ];

    /**
     * Atributos ocultos ao converter o model para array ou JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',                  // Esconde a senha
        'remember_token',            // Token de lembrar sessão
        'two_factor_recovery_codes', // Códigos de recuperação do 2FA
        'two_factor_secret',         // Segredo do 2FA
    ];

    /**
     * Acessores adicionados automaticamente ao array do model.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url', // Gera URL automática da foto de perfil
    ];

    /**
     * Define como alguns atributos devem ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converte data de verificação para Carbon
            'password' => 'hashed',            // Aplica hashing automático à senha
        ];
    }

    // Relação: um usuário possui várias licenças
    public function licenses(){
        return $this->hasMany(license::class);
    }

    // Relação: um usuário pode ter vários jogos publicados
    public function pubilshedGames(){
        return $this->hasMany(Game::class, 'user_id');
    }
}
