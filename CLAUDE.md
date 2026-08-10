# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> Para contexto completo, consulte `.claude/SKILL.md` (padrões de desenvolvimento), `.claude/MAPEAMENTO.md` (status das features) e `.claude/DOCUMENTACAO.md` (arquitetura detalhada).

## Comandos

```bash
# Ambiente de desenvolvimento completo (server + queue + logs + vite em paralelo)
composer run dev

# Individualmente:
php artisan serve          # Servidor Laravel em :8000
npm run dev                # Compilação de assets via Vite

# Testes
php artisan test                              # Todos os testes
php artisan test --filter=NomeDoTest          # Teste específico
composer run test                             # Limpa config cache antes de rodar

# Formatação de código
./vendor/bin/pint                             # Formatar todos os arquivos PHP
./vendor/bin/pint app/Http/Controllers/       # Formatar diretório específico

# Banco de dados
php artisan migrate
php artisan migrate:fresh                     # Recriar DB (somente dev!)
php artisan db:seed

# Build de produção
npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Arquitetura

AgroTwin é um sistema Laravel 12 MVC de gestão agrícola multi-tenant. Cada usuário vê apenas seus próprios dados — **nunca usar `Model::all()` sem filtrar por `id_usuario`**.

### Padrão obrigatório: Entity + Model + Service + Controller

Todo módulo segue esta estrutura em camadas:

```
app/Entity/LavouraEntity.php        → Constantes TABLE, PRIMARY_KEY, FILLABLE, CASTS + helpers de query estáticos
app/Models/Lavoura.php              → Eloquent model; usa UsesEntity + traits (Core, Dto, Insert, Update)
app/Models/Lavoura/Insert.php       → Trait com método inserir() — inclui id_usuario = Auth::id()
app/Models/Lavoura/Update.php       → Trait com método alterar()
app/Services/BaseService.php        → _inserir() / _alterar() / delete() genéricos
app/Http/Controllers/LavouraController.php   → Shell: declara $model, $resourceName, injeta model
app/Http/Controllers/Lavoura/Tela.php        → Trait: métodos de view (telaInserir, telaEditar…)
app/Http/Controllers/Lavoura/Crud.php        → Trait: métodos de mutação (store, update, destroy)
app/Http/Requests/Lavoura/StoreLavouraRequest.php  → Form Request com rules()
routes/rotasWeb/lavouras.php        → Rotas do módulo; incluído via require em routes/web.php
```

O trait `UsesEntity` (em `app/Traits/UsesEntity.php`) lê as constantes da Entity e configura `$table`, `$primaryKey`, `$fillable` e `$casts` do Eloquent em runtime.

O trait `CrudOperations` (em `app/Traits/CrudOperations.php`) fornece um `show()` genérico que responde tanto a requisições normais quanto AJAX (retorna JSON com HTML renderizado).

### Regras de segurança obrigatórias

```php
// ✅ CORRETO — filtrar sempre por usuário logado
$lavouras = Lavoura::where('id_usuario', Auth::id())->get();

// Para recursos aninhados em propriedade:
$lavoura = Lavoura::whereHas('propriedade', function ($q) {
    $q->where('id_usuario', Auth::id());
})->findOrFail($id);

// ✅ CORRETO — id_usuario sempre do Auth, nunca do request
$data['id_usuario'] = Auth::id();
```

### Convenção de nomenclatura de colunas

| Prefixo | Uso | Exemplo |
|---------|-----|---------|
| `ds_` | Strings descritivas | `ds_nome`, `ds_localizacao` |
| `tp_` | Tipos/enums | `tp_insumo`, `tp_solo` |
| `nu_` | Números | `nu_area_hectares`, `nu_quantidade` |
| `dt_` | Datas | `dt_plantio`, `dt_validade` |
| `id_` | Chaves estrangeiras | `id_usuario`, `id_propriedade` |

Chaves primárias seguem `id_<singular>` (ex.: `id_lavoura`, `id_insumo`). Tabelas são plurais em snake_case.

### Enums disponíveis

Todos os enums (em `app/Enums/`) são backed string enums e implementam:
- `label(): string` — label legível em português
- `toSelectArray(bool $blank = false): array` — pronto para `<select>` no Blade

Enums existentes: `TipoInsumo`, `TipoUnidadeMedida`, `TipoMovimentacao`, `TipoSolo`, `TipoStatus`, `TipoSensor`, `TipoMetodoAplicacao`, `TipoFinalidade`.

### Rotas

`routes/web.php` inclui via `require` os arquivos de `routes/rotasWeb/` (um por módulo). Todas as rotas autenticadas ficam dentro do grupo `auth`. Nomes seguem `<modulo>.<acao>` (ex.: `insumos.estoque.store`).

### Frontend

O layout (`resources/views/layouts/app.blade.php`) usa Tailwind CSS v4 (via `@tailwindcss/vite`, importado em `resources/css/app.css`) e Alpine.js 3 (CDN) como única stack de estilo/interatividade — sem Bootstrap ou Material Design Lite. Font Awesome 6 (ícones) e Chart.js seguem via CDN. Classes de componente reutilizadas em muitas telas (`.btn`, `.btn-primary`, `.card`, `.form-control` etc.) são definidas uma única vez em `app.css` via `@layer components` — reaproveite-as em vez de repetir sequências de utilitários. Alpine.js controla a sidebar e os modais (`x-show`/`x-transition` em `<x-ui.modal-funcional>`); a lógica de fetch/injeção de HTML dos modais fica em `resources/js/modal.js`.

Componentes Blade disponíveis:
- `<x-ui.responsive-table-card>` — tabela que colapsa para cards no mobile
- `<x-ui.section-header>` — cabeçalho de página com título e ações
- `<x-form.input>`, `<x-form.select>`, `<x-form.button>`, `<x-form.form>` — primitivos de formulário

### Banco de dados

- **MariaDB** — todos os dados relacionais (usuários, propriedades, lavouras, insumos, sensores)
- **InfluxDB** — séries temporais de sensores (ainda não integrado; variáveis: `INFLUXDB_URL`, `INFLUXDB_TOKEN`, `INFLUXDB_BUCKET`, `INFLUXDB_ORG`)

Migrations customizadas ficam em `database/migrations/custom/`.

### Como adicionar um novo módulo

1. Criar `app/Entity/NomeEntity.php` com `TABLE`, `PRIMARY_KEY`, `FILLABLE`, `CASTS`
2. Criar `app/Models/Nome.php` usando `UsesEntity` + traits `Core`, `Dto`, `Insert`, `Update`
3. Criar `app/Http/Controllers/NomeController.php` com traits `Nome\Tela` e `Nome\Crud`
4. Criar Form Requests em `app/Http/Requests/Nome/`
5. Criar `routes/rotasWeb/nomes.php` e incluir no `routes/web.php`
6. Criar views em `resources/views/nomes/`
7. Migration em `database/migrations/custom/`

### Checklist antes de commitar

- [ ] Toda query filtra por `id_usuario` (multi-tenant)
- [ ] `id_usuario` vem de `Auth::id()`, nunca do `$request`
- [ ] Usa Entity para helpers de query
- [ ] Usa Form Request para validação
- [ ] `@csrf` em todos os forms
- [ ] Sem N+1 queries (usar `with()` para eager loading)
- [ ] Sem `dd()` ou `console.log` esquecidos

### Status atual (referência)

Módulos completos: Autenticação, Propriedades, Insumos (CRUD + estoque + aplicação).
Parciais: Lavouras (sem delete), Sensores (cadastro somente), Dashboard (dados mock).
Não iniciados: MQTT, IA/Recomendações, Alertas, Relatórios avançados, API REST.
