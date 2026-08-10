// Select de Lavoura dependente da Propriedade escolhida, usado nas telas de
// inserir/editar Sensor. Fica desabilitado até uma Propriedade ser
// selecionada; ao selecionar, mostra só as lavouras daquela propriedade.
document.addEventListener('DOMContentLoaded', () => {
    const propriedadeSelect = document.getElementById('id_propriedade');
    const lavouraSelect = document.getElementById('id_lavoura');

    if (!propriedadeSelect || !lavouraSelect) {
        return;
    }

    const placeholderOption = lavouraSelect.querySelector('option[value=""]');
    const lavouraOptions = Array.from(lavouraSelect.querySelectorAll('option[value]:not([value=""])'));

    function filtrarLavouras(idPropriedade, manterSelecaoAtual) {
        const valorAtual = manterSelecaoAtual ? lavouraSelect.value : '';
        let algumaVisivel = false;

        lavouraOptions.forEach((option) => {
            const pertenceAPropriedade = idPropriedade !== '' && option.dataset.propriedade === idPropriedade;
            option.hidden = !pertenceAPropriedade;
            option.disabled = !pertenceAPropriedade;

            if (pertenceAPropriedade) {
                algumaVisivel = true;
            }
        });

        if (idPropriedade === '') {
            lavouraSelect.disabled = true;
            lavouraSelect.value = '';
            placeholderOption.textContent = 'Selecione uma propriedade primeiro';
            return;
        }

        lavouraSelect.disabled = false;
        placeholderOption.textContent = algumaVisivel
            ? 'Selecione uma lavoura'
            : 'Nenhuma lavoura cadastrada nesta propriedade';

        const selecaoAindaValida = lavouraOptions.some((option) => option.value === valorAtual && !option.hidden);
        lavouraSelect.value = selecaoAindaValida ? valorAtual : '';
    }

    // Estado inicial: preserva a lavoura já selecionada (ex.: ao editar um sensor existente).
    filtrarLavouras(propriedadeSelect.value, true);

    propriedadeSelect.addEventListener('change', () => {
        filtrarLavouras(propriedadeSelect.value, false);
    });
});
