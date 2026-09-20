<?php

namespace App\Http\Controllers;

use App\Models\Recomendacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecomendacaoController extends Controller
{
    protected $model = Recomendacao::class;

    protected $resourceName = 'recomendacoes';

    protected $singular = 'recomendacao';

    public function index(Request $request)
    {
        $busca = trim((string) $request->input('busca'));

        $consulta = Recomendacao::doUsuario(Auth::id())->with('lavoura');

        if ($busca !== '') {
            $consulta->where('ds_recomendacao', 'like', "%{$busca}%");
        }

        return view('recomendacoes.index', [
            'recomendacoes' => $consulta->paginate(static::POR_PAGINA)->withQueryString(),
            'busca' => $busca,
            'stats' => $this->estatisticas(),
        ]);
    }

    /**
     * Sobrescreve o show() genérico: a relação com o usuário passa por
     * lavoura->propriedade (sem FK direta em "recomendacoes"), então o escopo
     * padrão por `id_usuario` ou por `propriedade` não serve aqui.
     */
    public function show($id)
    {
        $recomendacao = Recomendacao::doUsuario(Auth::id())
            ->where('id_recomendacao', $id)
            ->firstOrFail();

        return $this->responderView('recomendacoes.detalhar', ['recomendacao' => $recomendacao], $recomendacao);
    }

    protected function estatisticas(): array
    {
        return [
            ['valor' => Recomendacao::doUsuario(Auth::id())->count(), 'rotulo' => 'recomendações'],
            [
                'valor' => Recomendacao::doUsuario(Auth::id())->where('dt_recomendacao', '>=', now()->subDays(7))->count(),
                'rotulo' => 'nos últimos 7 dias',
                'destaque' => true,
            ],
        ];
    }
}
