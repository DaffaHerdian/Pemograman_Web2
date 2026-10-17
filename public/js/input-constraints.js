(() => {
    'use strict';

    const personCharacters = /^[\p{L}\p{M} .'’\-]*$/u;
    const selector = 'input[data-input-kind], input[type="number"]';

    const kindFor = (input) => input.dataset.inputKind || (input.step && input.step !== '1' ? 'decimal' : 'integer');
    const messageFor = (input) => input.title || (kindFor(input) === 'person-name'
        ? 'Nama tidak boleh mengandung angka atau simbol lainnya.'
        : kindFor(input) === 'decimal' ? 'Masukkan angka; gunakan titik untuk desimal, tanpa huruf atau tanda +/−.'
        : 'Kolom ini hanya boleh diisi angka, tanpa huruf atau simbol.');
    const allowed = (input, value) => {
        switch (kindFor(input)) {
            case 'person-name': return personCharacters.test(value);
            case 'digits':
            case 'integer': return /^[0-9]*$/.test(value);
            default: return /^[0-9]*(?:\.[0-9]*)?$/.test(value);
        }
    };
    const feedbackFor = (input) => {
        if (input._constraintFeedback) return input._constraintFeedback;
        const feedback = document.createElement('p');
        feedback.id = `input-feedback-${document.querySelectorAll('[data-input-feedback]').length + 1}`;
        feedback.dataset.inputFeedback = '';
        feedback.className = 'mt-1 text-xs text-danger';
        feedback.setAttribute('role', 'status');
        feedback.hidden = true;
        input.insertAdjacentElement('afterend', feedback);
        input.setAttribute('aria-describedby', `${input.getAttribute('aria-describedby') || ''} ${feedback.id}`.trim());
        input._constraintFeedback = feedback;
        return feedback;
    };
    const showFeedback = (input) => {
        const feedback = feedbackFor(input);
        feedback.textContent = messageFor(input);
        feedback.hidden = false;
    };

    document.querySelectorAll(selector).forEach((input) => {
        input.inputMode = kindFor(input) === 'person-name' ? 'text' : kindFor(input) === 'decimal' ? 'decimal' : 'numeric';
    });
    document.addEventListener('beforeinput', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches(selector) || event.isComposing || !event.data) return;
        if (!allowed(input, event.data)) {
            event.preventDefault();
            showFeedback(input);
        }
    });
    document.addEventListener('paste', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches(selector)) return;
        const text = event.clipboardData?.getData('text') ?? '';
        if (!allowed(input, text)) {
            event.preventDefault();
            showFeedback(input);
        }
    });
    document.addEventListener('keydown', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || input.type !== 'number' || event.ctrlKey || event.metaKey || event.altKey) return;
        if (['e', 'E', '+', '-'].includes(event.key) || (kindFor(input) === 'integer' && ['.', ','].includes(event.key))) {
            event.preventDefault();
            showFeedback(input);
        }
    });
    document.addEventListener('input', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches(selector)) return;
        input.setCustomValidity('');
        if (!allowed(input, input.value)) {
            input.setCustomValidity(messageFor(input));
            showFeedback(input);
        } else if (input._constraintFeedback) {
            input._constraintFeedback.hidden = true;
        }
    });
    document.addEventListener('invalid', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches(selector)) return;
        if (input.validity.patternMismatch || input.validity.badInput) {
            input.setCustomValidity(messageFor(input));
            showFeedback(input);
        }
    }, true);
})();
