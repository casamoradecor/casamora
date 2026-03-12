document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');

    // Máscara do CEP
    cepInput.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        v = v.replace(/^(\d{5})(\d)/, '$1-$2');
        e.target.value = v.slice(0, 9);
    });

    // Busca CEP (ViaCEP) + Aciona o frete.js
    cepInput.addEventListener('blur', function() {
        let cep = this.value.replace(/\D/g, '');

        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('logradouro').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('localidade').value = data.localidade;
                        document.getElementById('uf').value = data.uf;
                        document.getElementById('numero').focus();

                        // Aciona a busca de frete do frete.js passando o subtotal global
                        if (typeof buscarFreteCheckout === 'function') {
                            buscarFreteCheckout(cep, window.subtotalBase);
                        }
                    } else {
                        if (typeof showMoraToast === 'function') {
                            showMoraToast('CEP não encontrado. Verifique o número digitado.', 'error');
                        } else {
                            alert('CEP não encontrado.');
                        }
                    }
                })
                .catch(error => console.error('Erro na API ViaCEP:', error));
        }
    });
});
