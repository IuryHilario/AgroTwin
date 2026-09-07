<?php

namespace App\Http\Controllers\Propriedade;

use App\Http\Controllers\WeatherController;
use App\Http\Requests\Propriedade\StorePropriedadeRequest;
use App\Models\Propriedade as PropriedadeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait Crud
{
    public function store(StorePropriedadeRequest $request)
    {
        $this->propriedadeModel->inserir($request->validated(), Auth::id());

        return redirect()->route('propriedade.index')
            ->with('success', 'Propriedade criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $propriedade = PropriedadeModel::where('id_propriedade', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        $propriedade->alterar(Auth::id(), $request->all());

        return redirect()->route('propriedade.index')
            ->with('success', 'Propriedade atualizada com sucesso!');
    }

    public function show($id)
    {
        $propriedade = PropriedadeModel::where('id_propriedade', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        $weatherData = [
            'success' => false,
            'message' => 'Cidade indisponível para consultas do tempo.',
        ];

        if (! empty($propriedade->ds_localizacao)) {
            $weatherController = new WeatherController;
            $weatherRequest = new Request;
            $weatherRequest->merge(['city' => $propriedade->ds_localizacao]);

            $weatherResponse = $weatherController->getWeather($weatherRequest);
            $weatherData = $weatherResponse->getData(true);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('propriedade.detalhar', compact('propriedade', 'weatherData'))->render(),
            ]);
        }

        return view('propriedade.detalhar', compact('propriedade', 'weatherData'));
    }

    public function inativar($id)
    {
        $propriedade = PropriedadeModel::where('id_propriedade', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        $propriedade->alternarStatus();

        $mensagem = $propriedade->fl_inativo ? 'Inativado com Sucesso!!' : 'Ativado com Sucesso!!';

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $mensagem]);
        }

        return redirect()->route('propriedade.index')->with('success', $mensagem);
    }
}
