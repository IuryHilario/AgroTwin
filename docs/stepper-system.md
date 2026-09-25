# Formulário em etapas (stepper)

Componente reutilizável para dividir um formulário em etapas, com validação de
cada etapa antes de avançar. Primeiro uso: **Nova/Editar Propriedade**
(`resources/views/propriedade/inserir.blade.php`), em três etapas —
Propriedade, Localidade e Finalização.

## Peças

| Arquivo | Papel |
|---|---|
| `resources/views/components/ui/stepper.blade.php` | Cabeçalho de progresso, área das etapas, erro e navegação (Voltar / Próximo / enviar) |
| `resources/views/components/ui/etapa.blade.php` | Uma etapa: título, descrição e grid de 12 colunas para os campos |
| `resources/views/components/ui/resumo-etapas.blade.php` | Revisão automática do que foi preenchido, com "Alterar" por etapa |
| `resources/js/componentes/stepper.js` | Estado e regras (Alpine), registrado como `stepper` em `resources/js/app.js` |

Componentes que costumam acompanhar, também reutilizáveis:

| Arquivo | Papel |
|---|---|
| `resources/views/components/form/localidade.blade.php` + `resources/js/componentes/localidade.js` | Busca de município, GPS e mapa (Leaflet) → latitude/longitude |
| `resources/views/components/ui/previsao-clima.blade.php` + `resources/js/componentes/previsao-clima.js` | Prévia do tempo (Open-Meteo) no ponto marcado |

> Os componentes Alpine precisam ser registrados em `resources/js/app.js`, que
> carrega **antes** do Alpine. Um script empilhado pela página (`@push('scripts')`)
> roda depois que o Alpine já iniciou e perde o evento `alpine:init`.

## Uso

```blade
<x-form.form action="{{ route('coisa.store') }}" :acoes="false">
    <x-ui.stepper
        class="col-span-12"
        :etapas="['Dados', 'Detalhes', 'Revisão']"
        :navegacao-livre="$emEdicao"
        rotulo-enviar="Cadastrar"
    >
        <x-ui.etapa :numero="1" titulo="Dados" descricao="O essencial.">
            <div class="col-span-12">
                <x-form.input name="ds_nome" label="Nome" required />
            </div>
        </x-ui.etapa>

        <x-ui.etapa :numero="2" titulo="Detalhes">
            ...
        </x-ui.etapa>

        <x-ui.etapa :numero="3" titulo="Revisão">
            <div class="col-span-12">
                <x-ui.resumo-etapas />
            </div>
        </x-ui.etapa>
    </x-ui.stepper>
</x-form.form>
```

- `:acoes="false"` no `<x-form.form>` tira os botões Limpar/Salvar — a navegação é do stepper.
- `etapas` aceita strings ou `['titulo' => 'Dados', 'icone' => 'fa-house']`.
- `navegacao-livre` libera todas as etapas no cabeçalho desde o início (edição, com tudo preenchido).
- O envio continua pelo caminho normal (`ajax-form` do `modal.js`); nada muda no controller.

## Validação

Antes de avançar, a etapa atual passa por duas camadas:

1. **Validação nativa do HTML** dos campos da etapa — `required`, `min`, `max`,
   `step`, `pattern`, `type="email"`... Funciona sem código extra. O
   `<x-form.input>` e o `<x-form.select>` passam `required` para o elemento.
2. **Regras próprias**, pelo evento cancelável `etapa-validar`, disparado no
   painel da etapa:

   ```js
   painelDaEtapa.addEventListener('etapa-validar', (evento) => {
       if (!condicao) {
           evento.preventDefault();
           evento.detail.erro = 'Mensagem mostrada acima dos botões.';
       }
   });
   ```

   É assim que o `<x-form.localidade obrigatorio>` exige um ponto no mapa.

No envio, **todas** as etapas são revalidadas (alguém pode ter voltado e apagado
um campo); a primeira com problema é aberta com o erro visível. O stepper
desliga a validação nativa do `<form>` (`noValidate`) justamente para isso: sem
isso o navegador tentaria focar um campo inválido numa etapa escondida e
bloquearia o envio sem mostrar nada.

**Erros do servidor** também levam à etapa certa:

- validação AJAX (422): o `modal.js` dispara `validacao-servidor` no formulário
  depois de marcar os campos, e o stepper abre a etapa do primeiro;
- página recarregada com `$errors`: ao iniciar, o stepper procura `.is-invalid`
  ou `.invalid-feedback` e abre essa etapa.

## Teclado e acessibilidade

- **Enter** num campo avança a etapa em vez de enviar o formulário pela metade.
  Na última etapa, Enter envia. Campos dentro de `[data-stepper-ignorar-enter]`
  tratam o Enter sozinhos (ex.: a busca de município, que escolhe a sugestão).
- O cabeçalho usa `aria-current="step"`; etapas ainda não liberadas ficam desabilitadas.
- Ao trocar de etapa, o foco vai para o título dela (`[data-etapa-titulo]`).

## Revisão automática (`<x-ui.resumo-etapas>`)

Lista, por etapa, cada campo com `name` e `<label>` e o valor atual — select
mostra o texto da opção, número sai no formato brasileiro, checkbox vira
Sim/Não. Senhas e campos ocultos ficam de fora.

- `data-resumo-rotulo="Coordenadas"` num elemento qualquer inclui esse elemento
  (valor do `data-resumo-valor` ou do texto).
- `data-resumo-ignorar` exclui o elemento e tudo dentro dele.

## Eventos

| Evento | Onde | Para quê |
|---|---|---|
| `etapa-validar` | painel da etapa | regras próprias antes de avançar (cancelável) |
| `etapa-alterada` | raiz do stepper | `detail: { etapa, anterior }` — reagir à troca |
| `validacao-servidor` | `<form>` | disparado pelo `modal.js` com os campos inválidos |
| `localidade-alterada` | sobe até o `<form>` | o seletor de localidade mudou o ponto |

## Observação sobre `x-show`

O Alpine mostra um elemento com `x-show` no próximo `requestAnimationFrame`.
Numa aba que não está desenhando (janela minimizada, painel oculto), a etapa só
aparece quando a aba volta a renderizar — comportamento do Alpine, não do
stepper. Em testes automatizados pelo navegador, force um quadro antes de medir.
