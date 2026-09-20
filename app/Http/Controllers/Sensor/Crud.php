<?php

namespace App\Http\Controllers\Sensor;

use App\Http\Requests\Sensor\StoreSensorRequest;
use App\Http\Requests\Sensor\UpdateSensorRequest;

trait Crud
{
    public function store(StoreSensorRequest $request)
    {
        $this->SensorModel->inserir($request->validated(), $this->idUsuario);

        return redirect()->route('sensores.index')
                        ->with('success', 'Sensor inserido com sucesso!');
    }

    public function update(UpdateSensorRequest $request, $id)
    {
        $sensor = $this->buscarDoUsuario($this->model, $id);
        $sensor->alterar($this->idUsuario, $request->validated());

        return redirect()->route('sensores.index')
                        ->with('success', 'Sensor atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $sensor = $this->buscarDoUsuario($this->model, $id);
        $sensor->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sensor excluído com sucesso!']);
        }

        return redirect()->route('sensores.index')->with('success', 'Sensor excluído com sucesso!');
    }
}
