<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetarSenhasHomolog extends Command
{
    // Comando: php artisan homolog:resetar-senhas
    // Uso típico: depois de restaurar um backup de produção no banco de homolog,
    // para poder logar com qualquer usuário sem saber a senha real de produção.

    protected $signature = 'homolog:resetar-senhas {--senha=admin : Senha que será definida para todos os usuários}';

    protected $description = 'Define a mesma senha para todos os usuários — só roda fora de produção (proteção contra rodar sem querer em prod)';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Comando bloqueado: o ambiente atual está configurado como "production". Isso só pode rodar em homolog/local.');
            return self::FAILURE;
        }

        $senha = $this->option('senha');
        $total = User::query()->update(['password' => Hash::make($senha)]);

        $this->info("Senha de {$total} usuário(s) redefinida(s) para \"{$senha}\".");

        return self::SUCCESS;
    }
}
