{{-- Usando include do Blade (forma recomendada) --}}
@include('propriedade.inserir', [
    'title' => 'Editar Propriedade',
    'action' => route('propriedade.update', $propriedade->id_propriedade),
    'method' => 'PUT',
    'icon' => 'fas fa-edit',
    'isEdit' => true
])
