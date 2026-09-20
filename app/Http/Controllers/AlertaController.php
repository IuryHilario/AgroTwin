<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Lavoura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertaController extends Controller
{
    protected $model = Alerta::class;

    protected $resourceName = 'alertas';

    /**
     * Lista os alertas do usuário com filtros de situação, severidade e
     * lavoura. Sem filtro a tela abre nos não lidos, que é o que interessa
     * quando se entra aqui depois de uma notificação.
     */
    public function index(Request $request)
    {
        $filtros = [
            'situacao' => $request->input('situacao', 'nao_lidos'),
            'severidade' => $request->input('severidade', ''),
            'lavoura' => $request->input('lavoura', ''),
        ];

        $consulta = $this->consultaDeAlertas()->with('lavoura');

        if ($filtros['situacao'] === 'nao_lidos') {
            $consulta->where('fl_lida', false);
        } elseif ($filtros['situacao'] === 'lidos') {
            $consulta->where('fl_lida', true);
        }

        if ($filtros['severidade'] !== '') {
            $consulta->where('tp_severidade', $filtros['severidade']);
        }

        if ($filtros['lavoura'] !== '') {
            $consulta->where('id_lavoura', $filtros['lavoura']);
        }

        return view('alertas.index', [
            'alertas' => $consulta->paginate(static::POR_PAGINA)->withQueryString(),
            'filtros' => $filtros,
            'lavouras' => Lavoura::whereHas('propriedade', fn ($q) => $q->where('id_usuario', Auth::id()))
                ->orderBy('ds_cultura')
                ->get(['id_lavoura', 'ds_cultura']),
            'stats' => $this->estatisticas(),
        ]);
    }

    public function marcarLido($id)
    {
        $alerta = $this->consultaDeAlertas()->where('id_alerta', $id)->firstOrFail();
        $alerta->marcarComoLido();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Alerta marcado como lido.']);
        }

        return back()->with('success', 'Alerta marcado como lido.');
    }

    public function marcarTodosLidos()
    {
        // Sem o orderBy da consulta de leitura: nem todo banco aceita ORDER BY em UPDATE.
        $quantidade = $this->baseDoUsuario()->where('fl_lida', false)->update(['fl_lida' => true]);

        $mensagem = $quantidade === 1
            ? '1 alerta marcado como lido.'
            : "{$quantidade} alertas marcados como lidos.";

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $mensagem]);
        }

        return back()->with('success', $mensagem);
    }

    protected function estatisticas(): array
    {
        return [
            ['valor' => $this->consultaDeAlertas()->count(), 'rotulo' => 'registrados'],
            ['valor' => $this->consultaDeAlertas()->where('tp_severidade', 'critical')->where('fl_lida', false)->count(), 'rotulo' => 'críticos'],
            ['valor' => $this->consultaDeAlertas()->where('fl_lida', false)->count(), 'rotulo' => 'não lidos', 'destaque' => true],
        ];
    }

    /** Alertas das lavouras do usuário, do mais recente para o mais antigo. */
    private function consultaDeAlertas()
    {
        return $this->baseDoUsuario()->orderByDesc('dt_alerta');
    }

    private function baseDoUsuario()
    {
        return Alerta::whereHas('lavoura.propriedade', fn ($query) => $query->where('id_usuario', Auth::id()));
    }
}
