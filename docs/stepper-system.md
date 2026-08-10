# Sistema de Stepper - AgroTwin

## Visão Geral

O sistema de stepper foi criado de forma modular e reutilizável para facilitar a criação de formulários multi-etapas em todo o projeto AgroTwin.

## Componentes Criados

### 1. Componente Stepper Principal
**Arquivo:** `resources/views/components/ui/stepper.blade.php`

### 2. Componente de Conteúdo do Step
**Arquivo:** `resources/views/components/ui/step-content.blade.php`

### 3. JavaScript do Stepper
**Arquivo:** `resources/js/stepper.js`

## Como Usar

### Estrutura Básica

```blade
<x-ui.stepper
    :steps="[
        ['title' => 'Etapa 1'],
        ['title' => 'Etapa 2'],
        ['title' => 'Etapa 3'],
        ['title' => 'Revisão']
    ]"
    formId="meuFormulario"
>
    <form id="meuFormulario" action="/salvar" method="POST">
        @csrf

        <!-- Etapa 1 -->
        <x-ui.step-content
            step="1"
            title="Título da Etapa 1"
            icon="fas fa-info-circle"
            active="true"
        >
            <!-- Conteúdo do formulário -->
            <div class="form-row">
                <x-form.input name="campo1" label="Campo 1" required />
            </div>
        </x-ui.step-content>

        <!-- Etapa 2 -->
        <x-ui.step-content
            step="2"
            title="Título da Etapa 2"
            icon="fas fa-cog"
        >
            <!-- Conteúdo do formulário -->
        </x-ui.step-content>

        <!-- Etapa de Revisão (opcional) -->
        <x-ui.step-content
            step="3"
            title="Revisão dos Dados"
            icon="fas fa-eye"
        >
            <div id="review-content">
                <!-- Será preenchido automaticamente -->
            </div>
        </x-ui.step-content>
    </form>
</x-ui.stepper>
```

### Inicialização JavaScript

```javascript
// No final da view, adicione:
@push('scripts')
    @vite(['resources/js/stepper.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new FormStepper({
                formId: 'meuFormulario',
                totalSteps: 3,
                validateOnNext: true,
                autoReview: true,
                reviewStepId: 3
            });
        });
    </script>
@endpush
```

## Opções de Configuração

### FormStepper Options

- **formId** (string): ID do formulário
- **totalSteps** (number): Número total de etapas
- **validateOnNext** (boolean): Valida campos ao avançar
- **autoReview** (boolean): Ativa revisão automática
- **reviewStepId** (number): ID da etapa de revisão

## Funcionalidades

### 1. Validação Automática
- Campos obrigatórios são validados automaticamente
- Validação por tipo de campo (number, email, etc.)
- Feedback visual com classes CSS

### 2. Navegação Inteligente
- Só permite navegar para etapas válidas
- Indicadores visuais de progresso
- Botões inteligentes (Próximo/Finalizar)

### 3. Revisão Automática
- Gera automaticamente uma tela de revisão
- Mostra todos os campos preenchidos
- Formatação inteligente por tipo de campo

### 4. Responsivo
- Adapta-se a diferentes tamanhos de tela
- Layout otimizado para mobile

## Classes CSS Principais

```css
.stepper-container     /* Container principal */
.stepper-header       /* Cabeçalho com navegação */
.stepper-nav          /* Navegação dos steps */
.step                 /* Item individual do step */
.step.active          /* Step ativo */
.step.completed       /* Step completado */
.stepper-body         /* Corpo do stepper */
.step-content         /* Conteúdo de cada etapa */
.stepper-controls     /* Controles (botões) */
.form-row             /* Linha do formulário */
.form-row.full        /* Linha completa */
```

## Exemplo Completo - Lavouras

```blade
<!-- Exemplo para cadastro de lavouras -->
<x-ui.stepper
    :steps="[
        ['title' => 'Informações Básicas'],
        ['title' => 'Datas e Cronograma'],
        ['title' => 'Localização'],
        ['title' => 'Revisão']
    ]"
    formId="lavouraForm"
>
    <form id="lavouraForm" action="{{ route('lavouras.store') }}" method="POST">
        @csrf

        <x-ui.step-content step="1" title="Informações da Lavoura" icon="fas fa-seedling" active="true">
            <div class="form-row">
                <x-form.input name="ds_nome" label="Nome da Lavoura" required />
                <x-form.input name="nu_area_plantada" label="Área Plantada (ha)" type="number" step="0.01" required />
            </div>
        </x-ui.step-content>

        <x-ui.step-content step="2" title="Cronograma" icon="fas fa-calendar">
            <div class="form-row">
                <x-form.input name="dt_plantio" label="Data de Plantio" type="date" required />
                <x-form.input name="dt_colheita" label="Previsão de Colheita" type="date" required />
            </div>
        </x-ui.step-content>

        <x-ui.step-content step="3" title="Propriedade" icon="fas fa-map-marked-alt">
            <div class="form-row full">
                <x-form.select name="id_propriedade" label="Propriedade" :options="$propriedades" required />
            </div>
        </x-ui.step-content>

        <x-ui.step-content step="4" title="Revisão" icon="fas fa-eye">
            <div id="review-content"></div>
        </x-ui.step-content>
    </form>
</x-ui.stepper>
```

## Vantagens do Sistema

1. **Reutilizável**: Pode ser usado em qualquer formulário
2. **Modular**: Componentes separados e organizados
3. **Flexível**: Configuração através de props e opções
4. **Validação**: Sistema robusto de validação
5. **UX**: Interface moderna e intuitiva
6. **Responsivo**: Funciona em todos os dispositivos
7. **Manutenível**: Código limpo e bem estruturado

## Próximos Passos

Para usar o stepper em outros formulários:

1. Copie a estrutura básica
2. Adapte os steps para seu caso de uso
3. Configure as opções do JavaScript
4. Adicione validações específicas se necessário
5. Teste em diferentes dispositivos

O sistema está pronto para ser usado em toda a aplicação!
