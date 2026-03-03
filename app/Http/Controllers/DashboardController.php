<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Propriedade;
use App\Models\User;
use App\Entity\PropriedadeEntity;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard principal
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        // Busca todas as propriedades do usuário logado
        $propriedades = Propriedade::byUsuario($usuario->id_usuario)->get();

        // Se não há propriedades, redireciona para criar uma
        if ($propriedades->isEmpty()) {
            return redirect()->route('propriedade.inserir')
                ->with('info', 'Você precisa cadastrar pelo menos uma propriedade para acessar o dashboard.');
        }

        // Verifica se uma propriedade específica foi selecionada
        $selectedPropriedadeId = $request->input('id_propriedade');

        // Se não foi especificada uma propriedade ou a especificada não existe/não pertence ao usuário
        if (!$selectedPropriedadeId || !$propriedades->where('id_propriedade', $selectedPropriedadeId)->first()) {
            $selectedPropriedadeId = $propriedades->first()->id_propriedade;
        }

        // Busca a propriedade selecionada
        $selectedPropriedade = $propriedades->where('id_propriedade', $selectedPropriedadeId)->first();

        // Aqui você pode adicionar lógica para buscar dados específicos da propriedade selecionada
        // Como dados de sensores, alertas, etc.
        $dadosDashboard = $this->getDadosDashboard($selectedPropriedade);

        return view('dashboard.index', [
            'propriedades' => $propriedades,
            'selectedPropriedade' => $selectedPropriedade,
            'getPropriedadeById' => $selectedPropriedadeId,
            'dadosDashboard' => $dadosDashboard
        ]);
    }

    /**
     * Busca dados específicos para o dashboard da propriedade selecionada
     */
    private function getDadosDashboard($propriedade)
    {
        // Aqui você implementará a lógica para buscar:
        // - Dados dos sensores
        // - Alertas ativos
        // - Recomendações da IA
        // - Histórico de medições
        // etc.

        return [
            'sensores' => $this->getDadosSensores($propriedade),
            'alertas' => $this->getAlertasAtivos($propriedade),
            'recomendacoes' => $this->getRecomendacoesIA($propriedade),
            'ultimasLeituras' => $this->getUltimasLeituras($propriedade)
        ];
    }

    /**
     * Busca dados dos sensores da propriedade
     */
    private function getDadosSensores($propriedade)
    {
        // Mock de dados - substituir pela implementação real
        return [
            'umidade' => 41,
            'ph' => 6.2,
            'temperatura' => 24,
            'npk' => '12-5-10'
        ];
    }

    /**
     * Busca alertas ativos da propriedade
     */
    private function getAlertasAtivos($propriedade)
    {
        // Mock de dados - substituir pela implementação real
        return [
            [
                'tipo' => 'critical',
                'titulo' => 'Sensor 02 Desconectado',
                'tempo' => 'Há 2 horas',
                'icone' => 'fas fa-exclamation-circle'
            ],
            [
                'tipo' => 'warning',
                'titulo' => 'Umidade baixa - Setor Norte',
                'tempo' => 'Há 4 horas',
                'icone' => 'fas fa-tint'
            ],
            [
                'tipo' => 'info',
                'titulo' => 'pH fora do ideal',
                'tempo' => 'Há 6 horas',
                'icone' => 'fas fa-flask'
            ]
        ];
    }

    /**
     * Busca recomendações da IA para a propriedade
     */
    private function getRecomendacoesIA($propriedade)
    {
        // Mock de dados - substituir pela implementação real
        return [
            'tipo' => 'Correção do Solo',
            'descricao' => 'Aplicar 2kg/ha de calcário dolomítico para correção do pH. O solo está ligeiramente ácido (6.2) e pode afetar a absorção de nutrientes.',
            'prioridade' => 'medium'
        ];
    }

    /**
     * Busca as últimas leituras dos sensores
     */
    private function getUltimasLeituras($propriedade)
    {
        // Mock de dados - substituir pela implementação real
        return [
            'ultima_atualizacao' => now()->format('H:i'),
            'status_geral' => 'healthy'
        ];
    }
}
