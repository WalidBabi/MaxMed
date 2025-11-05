const defaultHeaders = () => {
    const csrf = document.querySelector('meta[name="csrf-token"]');
    return {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf.getAttribute('content') } : {})
    };
};

const noop = () => {};

const defaultOptions = {
    method: 'POST',
    beforeSend: noop,
    onSuccess: noop,
    onError: noop,
    onFinally: noop
};

const mergeOptions = (base, overrides = {}) => ({
    ...base,
    ...overrides,
    headers: {
        ...defaultHeaders(),
        ...(overrides.headers || {})
    }
});

const normalizeForms = selectorOrNodes => {
    if (!selectorOrNodes) {
        return [];
    }

    if (typeof selectorOrNodes === 'string') {
        return Array.from(document.querySelectorAll(selectorOrNodes));
    }

    if (selectorOrNodes instanceof Element && selectorOrNodes.tagName === 'FORM') {
        return [selectorOrNodes];
    }

    if (selectorOrNodes instanceof NodeList || Array.isArray(selectorOrNodes)) {
        return Array.from(selectorOrNodes).filter(node => node.tagName === 'FORM');
    }

    return [];
};

const resolveMessage = (error) => {
    if (!error) return 'An unexpected error occurred.';
    if (typeof error === 'string') return error;
    if (error.message) return error.message;
    if (error.errors) {
        const firstKey = Object.keys(error.errors)[0];
        if (firstKey) {
            const value = error.errors[firstKey];
            if (Array.isArray(value)) {
                return value[0];
            }
            return value;
        }
    }
    return 'Unable to process the request.';
};

const toggleLoadingState = (submitBtn, enable) => {
    if (!submitBtn) return;

    if (enable) {
        submitBtn.disabled = true;
        if (!submitBtn.dataset.originalHtml) {
            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
        }
        if (submitBtn.dataset.loadingText) {
            submitBtn.innerHTML = submitBtn.dataset.loadingText;
        }
    } else {
        submitBtn.disabled = false;
        if (submitBtn.dataset.originalHtml) {
            submitBtn.innerHTML = submitBtn.dataset.originalHtml;
            delete submitBtn.dataset.originalHtml;
        }
    }
};

const parseResponsePayload = async (response) => {
    const contentType = response.headers.get('content-type');

    if (contentType && contentType.includes('application/json')) {
        return response.json();
    }

    // Fallback to text for non-JSON endpoints
    const text = await response.text();
    return text ? { message: text } : {};
};

export function ajaxForm(selectorOrNodes, options = {}) {
    const forms = normalizeForms(selectorOrNodes);
    if (forms.length === 0) return;

    const settings = mergeOptions(defaultOptions, options);

    const submitHandler = async (event) => {
        event.preventDefault();

        const form = event.currentTarget;
        const submitBtn = form.querySelector('[type="submit"]');

        if (form.dataset.confirm) {
            const confirmed = window.confirm(form.dataset.confirm);
            if (!confirmed) {
                return;
            }
        }

        const attrMethod = form.getAttribute('method') || settings.method;
        const method = attrMethod ? attrMethod.toUpperCase() : 'POST';
        const url = form.getAttribute('action') || window.location.href;

        const body = new FormData(form);
        const headers = { ...settings.headers };

        // For GET requests, convert to query string and append to URL
        let requestUrl = url;
        let requestBody = body;

        if (method === 'GET') {
            const params = new URLSearchParams();
            for (const [key, value] of body.entries()) {
                params.append(key, value);
            }
            requestUrl = `${url}${url.includes('?') ? '&' : '?'}${params.toString()}`;
            requestBody = undefined;
        } else {
            // Allow file uploads by omitting explicit Content-Type
            Object.keys(headers).forEach(key => {
                if (key.toLowerCase() === 'content-type') {
                    delete headers[key];
                }
            });
        }

        const requestInit = {
            method,
            headers,
            body: requestBody
        };

        try {
            settings.beforeSend({ form, submitBtn });
            toggleLoadingState(submitBtn, true);

            const response = await fetch(requestUrl, requestInit);
            const payload = await parseResponsePayload(response);

            if (!response.ok || (payload && payload.success === false)) {
                throw payload || { message: 'Request failed.' };
            }

            settings.onSuccess({ form, submitBtn, response, payload });
        } catch (error) {
            const message = resolveMessage(error);
            if (window?.toast?.error) {
                window.toast.error(message);
            } else {
                console.error(message, { error });
            }
            settings.onError({ form, submitBtn, error, message });
        } finally {
            toggleLoadingState(submitBtn, false);
            settings.onFinally({ form, submitBtn });
        }
    };

    forms.forEach(form => {
        // Prevent double binding
        form.removeEventListener('submit', submitHandler);
        form.addEventListener('submit', submitHandler);
    });
}

export function ajaxAction(triggerSelector, options = {}) {
    const settings = mergeOptions(defaultOptions, options);

    document.addEventListener('click', async event => {
        const trigger = event.target.closest(triggerSelector);
        if (!trigger) return;

        const url = trigger.dataset.url;
        if (!url) return;

        event.preventDefault();

        if (trigger.dataset.confirm) {
            const confirmed = window.confirm(trigger.dataset.confirm);
            if (!confirmed) {
                return;
            }
        }

        const method = (trigger.dataset.method || settings.method).toUpperCase();
        const headers = { ...settings.headers };
        let body;

        if (options.bodyBuilder) {
            const payload = options.bodyBuilder(trigger) || {};
            body = JSON.stringify(payload);
            headers['Content-Type'] = headers['Content-Type'] || 'application/json';
        }

        try {
            settings.beforeSend({ trigger });
            toggleLoadingState(trigger, true);

            const response = await fetch(url, {
                method,
                headers,
                body
            });

            const payload = await parseResponsePayload(response);

            if (!response.ok || (payload && payload.success === false)) {
                throw payload || { message: 'Request failed.' };
            }

            settings.onSuccess({ trigger, response, payload });
        } catch (error) {
            const message = resolveMessage(error);
            if (window?.toast?.error) {
                window.toast.error(message);
            } else {
                console.error(message, { error });
            }
            settings.onError({ trigger, error, message });
        } finally {
            toggleLoadingState(trigger, false);
            settings.onFinally({ trigger });
        }
    });
}

const showMessage = (type, message) => {
    if (!message) return;
    if (window?.toast && typeof window.toast[type] === 'function') {
        window.toast[type](message);
    } else {
        console[type === 'error' ? 'error' : 'log'](message);
    }
};

const handleSuccessDirectives = ({
    source,
    payload
}) => {
    const dataset = source.dataset || {};

    if (dataset.successDispatch) {
        dataset.successDispatch
            .split(',')
            .map(evt => evt.trim())
            .filter(Boolean)
            .forEach(evt => {
                const detail = dataset.successDispatchDetail ?? payload;
                window.dispatchEvent(new CustomEvent(evt, { detail }));
            });
    }

    if (dataset.successReplace) {
        const targets = document.querySelectorAll(dataset.successReplace);
        targets.forEach(target => {
            if (payload?.status_html) {
                target.innerHTML = payload.status_html;
            } else if (payload?.html) {
                target.innerHTML = payload.html;
            }
        });
    }

    if (dataset.successAppend && payload?.html) {
        const targets = document.querySelectorAll(dataset.successAppend);
        targets.forEach(target => {
            target.insertAdjacentHTML('beforeend', payload.html);
        });
    }

    if (dataset.successPrepend && payload?.html) {
        const targets = document.querySelectorAll(dataset.successPrepend);
        targets.forEach(target => {
            target.insertAdjacentHTML('afterbegin', payload.html);
        });
    }

    if (dataset.successRemove) {
        const targets = document.querySelectorAll(dataset.successRemove);
        targets.forEach(target => target.remove());
    }

    if (dataset.successHide) {
        const targets = document.querySelectorAll(dataset.successHide);
        targets.forEach(target => {
            target.classList.add('hidden');
        });
    }

    if (dataset.successShow) {
        const targets = document.querySelectorAll(dataset.successShow);
        targets.forEach(target => {
            target.classList.remove('hidden');
        });
    }

    if (payload?.redirect) {
        window.location.href = payload.redirect;
    }
};

export function registerAjaxUtilities({
    formSelector = '[data-ajax="form"]',
    actionSelector = '[data-ajax-action]'
} = {}) {
    ajaxForm(formSelector, {
        onSuccess: ({ form, payload }) => {
            const message = payload?.message || form.dataset.successMessage;
            showMessage('success', message);
            handleSuccessDirectives({ source: form, payload });

            if (form.dataset.resetForm !== 'false') {
                form.reset?.();
            }
        },
        onError: ({ form, message }) => {
            const fallback = form?.dataset?.errorMessage;
            showMessage('error', message || fallback || 'Unable to process request.');
        }
    });

    ajaxAction(actionSelector, {
        onSuccess: ({ trigger, payload }) => {
            const message = payload?.message || trigger.dataset.successMessage;
            showMessage('success', message);
            handleSuccessDirectives({ source: trigger, payload });
        },
        onError: ({ trigger, message }) => {
            const fallback = trigger?.dataset?.errorMessage;
            showMessage('error', message || fallback || 'Unable to process request.');
        }
    });
}

// Expose globally for dynamic components that mount later
window.ajaxForm = ajaxForm;
window.ajaxAction = ajaxAction;
window.registerAjaxUtilities = registerAjaxUtilities;

