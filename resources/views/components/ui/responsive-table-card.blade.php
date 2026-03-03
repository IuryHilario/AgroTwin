{{-- Componente responsivo para exibir tabelas e cards --}}

@php
    use App\Utils\Util;
@endphp

{{-- Tabela: visível apenas em telas grandes --}}
<div class="table-wrapper">
    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width">
        <thead>
            <tr>
                @foreach ($arTableHead as $column)
                    <th class="mdl-data-table__cell--non-numeric">{{ $column['label'] }}</th>
                @endforeach

                @if($showActions)
                    <th class="mdl-data-table__cell--non-numeric text-center">Ações</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach ($arValores as $item)
                <tr>
                    @foreach ($arTableHead as $column)
                        <td class="mdl-data-table__cell--non-numeric">
                            @php
                                $value = data_get($item, $column['key'], '-');
                                if (isset($column['function']) && method_exists(Util::class, $column['function'])) {
                                    $value = Util::{$column['function']}($value);
                                } elseif (isset($column['format']) && $column['format'] === 'date') {
                                    $value = Util::formatDate($value);
                                }
                            @endphp

                            {{-- Se for enum, mostra o label, senão mostra o valor normalmente --}}
                            @if(is_object($value) && method_exists($value, 'label'))
                                {{ $value->label() }}
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                    @if($showActions)
                        <td class="mdl-data-table__cell--non-numeric text-center">
                            @php
                                $acoes = [];
                                if (is_array($item) && isset($item['acoes'])) {
                                    $acoes = $item['acoes'];
                                } elseif (method_exists($item, 'setFuncionalidades')) {
                                    $acoes = $item->setFuncionalidades();
                                }
                            @endphp
                            @foreach($acoes as $acao)
                                @if(isset($acao['link']))
                                    <a href="{{ $acao['link'] }}"
                                       class="mdl-button mdl-js-button mdl-button--icon"
                                       title="{{ $acao['nome'] }}"
                                       data-action="{{ $acao['id'] ?? 'show' }}">
                                        @if(str_starts_with($acao['icone'], 'fa-'))
                                            <i class="fa {{ $acao['icone'] }}"></i>
                                        @else
                                            <i class="material-icons">{{ $acao['icone'] }}</i>
                                        @endif
                                    </a>
                                @else
                                    <span class="mdl-button mdl-js-button mdl-button--icon" title="{{ $acao['nome'] }}">
                                        @if(str_starts_with($acao['icone'], 'fa-'))
                                            <i class="fa {{ $acao['icone'] }}"></i>
                                        @else
                                            <i class="material-icons">{{ $acao['icone'] }}</i>
                                        @endif
                                    </span>
                                @endif
                            @endforeach
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


{{-- Cards: visíveis apenas em telas pequenas --}}
<div class="cards-wrapper">
    @foreach ($arValores as $item)
        <div class="responsive-card">
            <div class="responsive-card-body">
                @foreach ($arTableHead as $column)
                    <div class="responsive-card-row">
                        <span class="responsive-card-label">{{ $column['label'] }}:</span>
                        <span class="responsive-card-value">
                            @php
                                $value = data_get($item, $column['key'], '-');
                                if (isset($column['function']) && method_exists(Util::class, $column['function'])) {
                                    $value = Util::{$column['function']}($value);
                                }
                            @endphp
                            @if(is_object($value) && method_exists($value, 'label'))
                                {{ $value->label() }}
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
            @if($showActions)
                <div class="responsive-card-actions">
                    @php
                        $acoes = [];
                        if (is_array($item) && isset($item['acoes'])) {
                            $acoes = $item['acoes'];
                        } elseif (method_exists($item, 'setFuncionalidades')) {
                            $acoes = $item->setFuncionalidades();
                        }
                        $maxAcoes = 100;
                        $acoesVisiveis = array_slice($acoes, 0, $maxAcoes);
                    @endphp
                    @foreach($acoesVisiveis as $acao)
                        @if(isset($acao['link']))
                            <a href="{{ $acao['link'] }}"
                               class="mdl-button mdl-js-button mdl-button--icon"
                               title="{{ $acao['nome'] }}"
                               data-action="{{ $acao['id'] ?? 'show' }}">
                                @if(str_starts_with($acao['icone'], 'fa-'))
                                    <i class="fa {{ $acao['icone'] }}"></i>
                                @else
                                    <i class="material-icons">{{ $acao['icone'] }}</i>
                                @endif
                            </a>
                        @else
                            <span class="mdl-button mdl-js-button mdl-button--icon" title="{{ $acao['nome'] }}">
                                @if(str_starts_with($acao['icone'], 'fa-'))
                                    <i class="fa {{ $acao['icone'] }}"></i>
                                @else
                                    <i class="material-icons">{{ $acao['icone'] }}</i>
                                @endif
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
