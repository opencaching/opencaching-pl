interface AdminAjaxResponse {
    status?: string
    message?: string
    newCachesCount?: number
}

async function postForm(form: HTMLFormElement): Promise<AdminAjaxResponse> {
    const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' }
    })
    const result = await response.json() as AdminAjaxResponse
    if (!response.ok || result.status !== 'OK') {
        throw new Error(result.message || form.dataset.lnErrorMessage || 'Error')
    }

    return result
}

function showError(error: unknown, form: HTMLFormElement): void {
    window.alert(form.dataset.lnErrorMessage || (error instanceof Error ? error.message : 'Error'))
}

export class PowerTrailEditComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        this.root
            .querySelectorAll<HTMLFormElement>('.powerTrail-remove-cache-form')
            .forEach((form) => this.bindRemoveCacheForm(form))

        this.root
            .querySelectorAll<HTMLElement>('.powerTrail-status')
            .forEach((container) => this.bindStatus(container))
    }

    private bindRemoveCacheForm(form: HTMLFormElement): void {
        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            const confirmMessage = form.dataset.lnConfirmMessage
                || ''
            if (!window.confirm(confirmMessage)) {
                return
            }

            const submitButton = form.querySelector<HTMLButtonElement>('button[type="submit"]')
            if (submitButton) {
                submitButton.disabled = true
            }

            postForm(form)
                .then((result) => {
                    form.closest('tr')?.remove()
                    this.updateCacheCount(result.newCachesCount)
                })
                .catch((error: unknown) => {
                    showError(error, form)
                    if (submitButton) {
                        submitButton.disabled = false
                    }
                })
        }
    }

    private bindStatus(container: HTMLElement): void {
        const label = container.querySelector<HTMLElement>('.powerTrail-status-label')
        const changeButton = container.querySelector<HTMLButtonElement>('.powerTrail-status-change-button')
        const form = container.querySelector<HTMLFormElement>('.powerTrail-status-form')
        const select = container.querySelector<HTMLSelectElement>('select[name="status"]')
        const submitButton = form?.querySelector<HTMLButtonElement>('button[type="submit"]')

        if (!label || !changeButton || !form || !select) {
            return
        }

        changeButton.onclick = () => {
            changeButton.hidden = true
            form.hidden = false
        }

        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            if (submitButton) {
                submitButton.disabled = true
            }

            postForm(form)
                .then(() => {
                    label.textContent = select.selectedOptions[0].textContent?.trim() || ''
                })
                .catch((error: unknown) => showError(error, form))
                .finally(() => {
                    if (submitButton) {
                        submitButton.disabled = false
                    }
                    form.hidden = true
                    changeButton.hidden = false
                })
        }
    }

    private updateCacheCount(count?: number): void {
        if (count === undefined) {
            return
        }

        this.root
            .querySelectorAll<HTMLElement>('.powerTrail-cache-count')
            .forEach((element) => {
                element.textContent = String(count)
            })
    }
}