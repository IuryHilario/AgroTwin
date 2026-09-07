<?php

namespace App\Models;

use App\Entity\LavouraEntity;
use App\Entity\PropriedadeEntity;
use App\Entity\UsuarioEntity;
use App\Models\Propriedade\Update;
use App\Services\BaseService;
use App\Traits\UsesEntity;
use Illuminate\Database\Eloquent\Model;

class Propriedade extends Model
{
    use Update, UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(PropriedadeEntity::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function lavouras()
    {
        return $this->hasMany(Lavoura::class, 'id_propriedade', 'id_propriedade');
    }

    public function scopeByUsuario($query, $idUsuario)
    {
        return PropriedadeEntity::getPropriedadesByUsuario($query, $idUsuario);
    }

    /**
     * Propriedades ativas (fl_inativo = false) — usada nos seletores de
     * propriedade/lavoura (Dashboard, Relatórios, Recomendações) para não
     * oferecer propriedades inativadas para seleção.
     */
    public function scopeAtivas($query)
    {
        return $query->where('fl_inativo', false);
    }

    public static function getById($id)
    {
        return PropriedadeEntity::getPropriedadeById(self::query(), $id)->first();
    }

    public function getDetalhesFormatados()
    {
        $detalhes = [];

        $detalhes['id'] = $this->id_propriedade;
        $detalhes['nome'] = $this->ds_nome;
        $detalhes['tp_solo'] = $this->tp_solo ? $this->tp_solo->label() : 'Não informado';
        $detalhes['area_hectares'] = $this->nu_area_hectares ? number_format($this->nu_area_hectares, 2, ',', '.').' ha' : 'Não informada';
        $detalhes['localizacao'] = $this->ds_localizacao ?? 'Não informada';
        $detalhes['proprietario'] = UsuarioEntity::getNomeUsuarioById($this->id_usuario) ?? 'Não informado';
        $detalhes['total_lavouras'] = LavouraEntity::getLavourasByPropriedade(Lavoura::query(), $this->id_propriedade)->count();
        $detalhes['status'] = $this->fl_inativo ? 'Inativa' : 'Ativa';
        $detalhes['data_criacao'] = $this->created_at;
        $detalhes['data_atualizacao'] = $this->updated_at;

        return $detalhes;
    }

    public function setFuncionalidades()
    {
        $funcionalidades = [];

        $funcVisualizar = [
            'id' => 'detalhar',
            'nome' => 'Detalhar',
            'icone' => 'fa-eye',
            'link' => route('propriedade.show', $this->id_propriedade),
        ];
        $funcionalidades[] = $funcVisualizar;

        $funcEditar = [
            'id' => 'editar',
            'nome' => 'Editar',
            'icone' => 'fa-edit',
            'link' => route('propriedade.edit', $this->id_propriedade),
        ];
        $funcionalidades[] = $funcEditar;

        $funcionalidades[] = $this->fl_inativo
            ? [
                'id' => 'ativar',
                'nome' => 'Ativar',
                'icone' => 'fa-toggle-on',
                'link' => route('propriedade.inativar', $this->id_propriedade),
            ]
            : [
                'id' => 'inativar',
                'nome' => 'Inativar',
                'icone' => 'fa-toggle-off',
                'link' => route('propriedade.inativar', $this->id_propriedade),
            ];

        return $funcionalidades;
    }

    public function inserir(array $data, $idUsuario)
    {
        $data['id_usuario'] = $idUsuario;
        $service = new BaseService($this);

        return $service->_inserir($data);
    }

    public function alternarStatus(): void
    {
        $this->update(['fl_inativo' => ! $this->fl_inativo]);
    }
}
