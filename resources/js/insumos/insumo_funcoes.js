/**
 * Funções específicas para insumos
 * Agora utiliza o sistema genérico de modais
 */

// Funções legadas - mantidas para compatibilidade
// O novo sistema genérico em modal.js substitui essas funções

function criarEstoque(id) {
    console.warn('Função criarEstoque() é legada. Use o sistema genérico de modais.');
    // Redireciona para o novo sistema
    if (window.modalManager) {
        const url = `${window.location.origin}/insumos/estoque/${id}/movimentacao`;
        window.modalManager.loadModal(url);
    }
}

function criarAplicacao(id) {
    console.warn('Função criarAplicacao() é legada. Use o sistema genérico de modais.');
    // Redireciona para o novo sistema
    if (window.modalManager) {
        const url = `${window.location.origin}/insumos/aplicacao/${id}/criar`;
        window.modalManager.loadModal(url);
    }
}

// Exporta para compatibilidade global
window.criarEstoque = criarEstoque;
window.criarAplicacao = criarAplicacao;
