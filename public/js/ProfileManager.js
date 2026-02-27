class ProfileManager {
    constructor() {
        this.initElements();
        this.initListeners();
    }

    initElements() {
        this.checkboxToggle = document.getElementById('toggle-password-fields');
        this.passwordFields = document.getElementById('password-fields');
        this.toggleButtons = document.querySelectorAll('.toggle-password');

        this.cpfInput = document.getElementById('cpf_perfil');
        this.newPass = document.getElementById('new_password');
        this.confPass = document.getElementById('confirm_password');

        this.requirements = {
            len: document.getElementById('req-length'),
            num: document.getElementById('req-number'),
            spec: document.getElementById('req-special'),
            match: document.getElementById('req-match')
        };
    }

    initListeners() {
        this.checkboxToggle?.addEventListener('change', () => this.toggleFields());

        this.toggleButtons.forEach(btn => {
            btn.addEventListener('click', (e) => this.toggleVisibility(e.currentTarget));
        });

        // Apenas máscara para o CPF, sem validação de "certo/errado" no JS
        this.cpfInput?.addEventListener('input', (e) => this.maskCPF(e));

        // Validações de senha permanecem em tempo real
        this.newPass?.addEventListener('input', () => this.validatePasswordSecurity());
        this.confPass?.addEventListener('input', () => this.validatePasswordSecurity());
    }

    toggleFields() {
        this.passwordFields.style.display = this.checkboxToggle.checked ? 'grid' : 'none';
    }

    toggleVisibility(button) {
        const input = button.parentElement.querySelector('input');
        input.type = input.type === "password" ? "text" : "password";
        button.style.color = input.type === "text" ? "#4B3621" : "#888";
    }

    maskCPF(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 3 && v.length <= 6) v = v.replace(/(\d{3})(\d+)/, "$1.$2");
        else if (v.length > 6 && v.length <= 9) v = v.replace(/(\d{3})(\d{3})(\d+)/, "$1.$2.$3");
        else if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, "$1.$2.$3-$4");
        e.target.value = v.slice(0, 14);
    }

    validatePasswordSecurity() {
        const v = this.newPass.value;
        const c = this.confPass.value;

        if (!this.requirements.len) return; // Segurança caso os elementos não existam

        this.requirements.len.className = v.length >= 8 ? 'valid' : 'invalid';
        this.requirements.num.className = /[0-9]/.test(v) ? 'valid' : 'invalid';
        this.requirements.spec.className = /[!@#$%^&*(),.?":{}|<>]/.test(v) ? 'valid' : 'invalid';

        if (v === c && v !== "") {
            this.requirements.match.className = 'valid';
            this.requirements.match.innerText = "As senhas coincidem";
        } else {
            this.requirements.match.className = 'invalid';
            this.requirements.match.innerText = "As senhas não coincidem";
        }
    }
}