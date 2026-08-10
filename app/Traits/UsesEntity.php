<?php

namespace App\Traits;

trait UsesEntity
{
    /**
     * Configura o model com base em uma Entity.
     *
     * @param string $entityClass Nome da classe da Entity
     * @return $this
     * @throws \Exception
     */
    public function setEntity(string $entityClass)
    {
        if (!class_exists($entityClass)) {
            throw new \Exception("Entity {$entityClass} não encontrada.");
        }

        // Verifica se a Entity define as constantes mínimas esperadas
        if (!defined("{$entityClass}::TABLE") || !defined("{$entityClass}::PRIMARY_KEY")) {
            throw new \Exception("Entity {$entityClass} precisa definir as constantes TABLE e PRIMARY_KEY.");
        }

        // Usa valores já configurados no model como fallback para evitar atribuição nula
        $this->table = $entityClass::TABLE ?? ($this->table ?? null);
        $this->primaryKey = $entityClass::PRIMARY_KEY ?? ($this->primaryKey ?? 'id');
        $this->incrementing = true;
        $this->keyType = 'int';

        // Aplica fillable e casts definidos na Entity (opcional)
        if (defined("{$entityClass}::FILLABLE")) {
            $this->fillable = $entityClass::FILLABLE;
        }
        if (defined("{$entityClass}::CASTS")) {
            $this->casts = $entityClass::CASTS;
        }

        return $this;
    }
}
