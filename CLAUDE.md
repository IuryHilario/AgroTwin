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
# Os testes usam SQLite em memória: o PHP precisa da extensão pdo_sqlite habilitada

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

Componentes Blade disponíveis (todos anônimos, com `@props` declarados — não existem mais classes em `app/View/Components`):
- `<x-ui.responsive-table-card>` — listagem: tabela no desktop, cards no mobile. Quando a lista vem vazia, renderiza um estado vazio; personalize com `vazioIcone`, `vazioTitulo`, `vazioTexto`, `vazioRota` e `vazioAcao`
- `<x-ui.celula>` — formata o valor de uma coluna da listagem conforme `'tipo'` (ver abaixo); usado pela tabela e pelos cards
- `<x-ui.acoes-linha>` — botões de ação da linha (3 visíveis, o resto no menu "Mais ações"); lê `$item->setFuncionalidades()`
- `<x-ui.selo tom="ok|alerta|erro|info|neutro">` — selo de status
- `<x-ui.section-header>` — cabeçalho das telas internas. `modulo` (propriedades, lavouras, insumos, sensores, alertas, recomendacoes, conta) define cor, ícone e rótulo — a mesma identidade na listagem, no cadastro e na edição. Aceita `etapa` ("Cadastro"/"Edição"), `subtitle`, `stats` (números rápidos), `acao` (botão principal; no mobile ele vira o botão flutuante) e `buttons`
- `<x-form.form>`, `<x-form.form-modal>`, `<x-form.input>`, `<x-form.select>` — formulários. Input e select repassam atributos extras (`step`, `min`, `max`, `readonly`…) ao elemento, aceitam `ajuda` (texto de apoio) e colocam `required` no próprio elemento (não só o asterisco). `<x-form.form :acoes="false">` tira os botões Limpar/Salvar
- `<x-auth.campo>` — campo das telas de autenticação (rótulo + input + olho de senha + erro)
- `<x-ui.stepper>` + `<x-ui.etapa>` + `<x-ui.resumo-etapas>` — formulário em etapas com validação por etapa (nativa do HTML + evento `etapa-validar`) e revisão automática. Guia completo em `docs/stepper-system.md`
- `<x-form.localidade>` — busca de município (Open-Meteo), GPS e mapa Leaflet com pino arrastável; grava `nu_latitude`, `nu_longitude` e `ds_localizacao`. O Leaflet é importado sob demanda (chunk separado), só quando o seletor aparece
- `<x-ui.previsao-clima>` — prévia do tempo no ponto marcado no formulário

Os componentes Alpine reutilizáveis (`stepper`, `seletorLocalidade`, `previsaoClima`) ficam em `resources/js/componentes/` e são registrados em `resources/js/app.js`, que carrega antes do Alpine. Script empilhado por página (`@push`) roda depois que o Alpine iniciou e não consegue registrar componente.

Colunas da listagem (`:arTableHead`): `['label' => …, 'key' => …]` mais, opcionalmente:
- `'tipo'` — `texto` (padrão; enum vira `label()`), `data`, `data_hora`, `numero` (com `'casas'` e `'sufixo'`), `status` (selo colorido para ativo/inativo/colhida/encerrada e warning/critical/info), `booleano` (com `'rotuloSim'`, `'rotuloNao'`, `'tomSim'`, `'tomNao'`)
- `'destaque' => true` — coluna principal em negrito
- Valor vazio aparece como "—"; datas e números usam o padrão brasileiro (`App\Utils\Util`)

Mensagens de retorno (`with('success'|'info'|'error', …)`) aparecem como toast no canto da tela, renderizado pelo `layouts/app`. Formulários AJAX não passam por ele: o `modal.js` mostra o próprio resultado.

### Layouts

- `layouts/app.blade.php` — shell autenticado (sidebar, topbar, tema). Use via `@extends` + `@section('content')`
- `layouts/index.blade.php` — estende o `app`, aplica o container `max-w-[1400px]` e o botão flutuante de criar. Listagens e formulários de tela cheia usam este, com `@section('page-content')` e um bloco `@php $fabRoute / $fabText @endphp`
- `layouts/auth.blade.php` — telas públicas (login, cadastro, esqueci/redefinir senha). Split-screen com o painel escuro à esquerda; as telas preenchem `@section('cabecalho')`, `subtitulo`, `formulario` e `rodape`

### Linguagem visual

Duas superfícies distintas, e a escolha entre elas é intencional:

- **Painel escuro (`.painel-estacao`)** — usado no topo do dashboard e dos relatórios, e no lado esquerdo das telas de autenticação. Escuro nos dois temas, com malha de pontos; é a leitura "de instrumento" da estação.
- **Superfícies claras** (`.surface`, `.card`) — o resto da interface, que acompanha o tema claro/escuro.

Complementos:
- `.font-readout` (IBM Plex Mono, dígitos tabulares) em valores de sensor, faixas e horários. Inter continua na interface.
- `.medidor-trilho` / `.medidor-faixa` / `.medidor-marcador` — medidor que posiciona a leitura dentro da faixa configurada para a lavoura.
- `.entrada` — entrada escalonada das telas de autenticação (combine com `style="animation-delay: …"`); respeita `prefers-reduced-motion`.

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

- [ ] Toda query filtra por `id_usuario` (multi-tenant) — em controller, use `buscarDoUsuario()`/`consultaDoUsuario()` do trait `EscopoDoUsuario`, nunca `Model::findOrFail($id)` puro
- [ ] `id_usuario` vem de `Auth::id()`, nunca do `$request`
- [ ] Ação que muda estado é POST/PUT/DELETE, nunca GET
- [ ] Usa Entity para helpers de query
- [ ] Usa Form Request para validação
- [ ] `@csrf` em todos os forms
- [ ] Sem N+1 queries (usar `with()` para eager loading)
- [ ] Sem `dd()` ou `console.log` esquecidos

### Status atual (referência)

**Prontos:** Autenticação (login, cadastro, recuperação de senha, throttle de 5 tentativas/min),
Propriedades, Lavouras, Insumos (CRUD + estoque + aplicação + relatório com PDF e e-mail),
Sensores (CRUD + token de dispositivo + detecção de sensor mudo pela última leitura), Dashboard (dados reais, avaliados contra as faixas da
lavoura), Alertas, Recomendações por regras condicionais, Irrigação (automática e manual, com
histórico), Relatórios de sensores — diagnóstico do solo com índice de saúde, situação por parâmetro,
tendência e ações sugeridas (`DiagnosticoSoloService`), com exportação em PDF —, API REST de ingestão (autenticada por
token + rate limiting), Previsão do tempo via Open-Meteo (`ClimaService`, por latitude/longitude, cache de
30 min: tempo atual, chance de chuva nas próximas 6h, 5 dias com chuva prevista e ET0) no dashboard e nos
detalhes da propriedade, cadastro de propriedade em etapas com seletor de localidade no mapa.

**Parciais:**
- Testes — cobrem API de sensor, alertas (serviço e tela), irrigação, recomendações, diagnóstico dos
  relatórios, limites por cultura, busca/paginação, isolamento multi-tenant, cadastro de propriedade e os
  serviços do Open-Meteo (com `Http::fake`); não cobrem auth nem o JavaScript dos componentes
- Firmware — `firmware/esp32-solo-7em1/` existe; o `esp32-irrigacao/` citado no README foi removido no commit `7005192`

**Não iniciados:** MQTT (hoje é REST direto), InfluxDB (hoje é MariaDB), TLS no dispositivo,
Deep Sleep no ESP32, papéis admin/usuário, 2FA, log de auditoria, Machine Learning, app mobile.

> MQTT e InfluxDB constam na proposta original do TCC. O desvio para REST/MariaDB está
> justificado no README — confirme com o orientador antes de investir tempo neles.

### Padrões transversais das telas

- **Escopo por usuário** — `App\Traits\EscopoDoUsuario` (usado pelo `Controller` base): `consultaDoUsuario($model)`
  devolve a query já filtrada e `buscarDoUsuario($model, $id)` devolve o registro ou 404. Sensores e lavouras
  herdam o dono da propriedade; propriedades e insumos têm `id_usuario` próprio.
- **Listagens** — o `index()` do `Controller` base pagina (`POR_PAGINA = 15`), aplica busca por `$colunasBusca`
  e faz eager loading por `$eagerLoad`. Os números do cabeçalho vêm de `estatisticas()` no controller, não da
  view: com paginação, contar na coleção daria só a página atual.
- **Variável singular das views** — `$singular` no controller (`'sensor'`, `'recomendacao'`…). Não existe
  singularização automática: "sensores"/"recomendacoes" não viram "sensor"/"recomendacao" por regra.
- **Ações de estado** — rota POST/DELETE + `data-action`/`data-url` no elemento; o `modal.js` pede confirmação
  e envia com `X-CSRF-TOKEN`. Casos existentes: `excluir`, `inativar`, `ativar`, `marcar-como-lido`,
  `marcar-todos-lidos`, `irrigar`, `parar-irrigacao`.
- **Alerta é episódio, não leitura** — `AlertaService` abre um alerta quando o parâmetro sai da faixa,
  acumula `nu_ocorrencias` enquanto continuar fora e preenche `dt_normalizado` quando volta (com 5% de
  histerese, senão um valor oscilando na borda reabriria o episódio a cada leitura). O e-mail sai só na
  abertura. Uma linha por leitura significaria ~48 alertas/dia por parâmetro fora da faixa.
  `RecomendacaoService` segue a mesma ideia com uma janela de 12h para texto idêntico.
- **Números em gráfico** — sempre pelo `formatarNumero` (`Intl.NumberFormat pt-BR`, 1 casa). O Chart.js
  calcula as marcas do eixo dividindo o intervalo e entrega coisas como `5.1000000000000005`; concatenar
  o valor cru com a unidade jogava isso na tela.
- **Faixas ideais** — `App\Support\FaixasSugeridas` traz valores de referência por cultura para a tela de
  limites, com preenchimento em um clique. Sem faixa configurada, alertas, dashboard e relatório ficam cegos.
- **Telas de modal abertas direto** — `responderView()` devolve JSON no AJAX e, na navegação normal,
  embrulha a mesma view em `layouts.pagina-modal`. Sem isso, abrir `/lavouras/1/limites` (ou qualquer
  `detalhar`) pela URL entregava o HTML do modal sem layout nem CSS.
- **Clima (Open-Meteo)** — `LocalidadeService` (geocodificação de município) e `ClimaService` (previsão),
  sem chave de API, chamados pelo servidor (`/localidades/buscar` e `/localidades/clima`, com throttle) e
  nunca direto do navegador. `LocalidadeService::buscar()` devolve `[]` quando não acha e `null` quando a
  API não respondeu — a falha fica só 2 min no cache, para não se passar por "município inexistente".
  Propriedade antiga sem coordenadas cai na geocodificação de `ds_localizacao`. Nos testes, o `TestCase`
  chama `Http::preventStrayRequests()`: nada sai para a internet sem `Http::fake()`.
- **Componentes de listagem** — `<x-ui.responsive-table-card>` recebe o paginador e a busca; `<x-ui.celula>`
  formata a coluna pelo `tipo` (inclui `conexao`, `validade` e `estoque`); `<x-ui.paginacao>` e `<x-ui.busca>`
  aparecem sozinhos a partir dessas props.

### Pontos de atenção conhecidos

- `MAIL_MAILER=log` em desenvolvimento: recuperação de senha, alertas e relatório por e-mail só caem em `storage/logs/laravel.log`
- O token do sensor fica em texto puro na coluna `sensores.token` — decisão consciente, para poder consultá-lo no banco ao configurar o ESP32 sem arriscar invalidá-lo por engano
