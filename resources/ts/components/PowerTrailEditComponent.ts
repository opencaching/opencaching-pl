interface FounderData {
    user_id: number
    username: string
}

interface AdminAjaxResponse {
    status?: string
    message?: string
    newCachesCount?: number
    wis?: number
    dateCreated?: string
    founder?: FounderData
}

interface TinyMceEditor {
    getContent(): string
    getContainer(): HTMLElement | null
    focus(): void
}

interface TinyMceGlobal {
    get(id: string): TinyMceEditor | null
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

        this.root
            .querySelectorAll<HTMLElement>('.powerTrail-wis')
            .forEach((container) => this.bindWis(container))

        this.root
            .querySelectorAll<HTMLElement>('.powerTrail-date-created')
            .forEach((container) => this.bindDateCreated(container))

        this.root
            .querySelectorAll<HTMLElement>('.powerTrail-description')
            .forEach((container) => this.bindDescription(container))

        this.root
            .querySelectorAll<HTMLElement>('.powerTrail-founders')
            .forEach((container) => this.bindFounders(container))
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

    private bindWis(container: HTMLElement): void {
        const label = container.querySelector<HTMLElement>('.powerTrail-wis-label')
        const changeButton = container.querySelector<HTMLButtonElement>('.powerTrail-wis-change-button')
        const form = container.querySelector<HTMLFormElement>('.powerTrail-wis-form')
        const input = form?.querySelector<HTMLInputElement>('input[name="wis"]')
        const submitButton = form?.querySelector<HTMLButtonElement>('button[type="submit"]')

        if (!label || !changeButton || !form || !input) {
            return
        }

        changeButton.onclick = () => {
            changeButton.hidden = true
            form.hidden = false
            input.focus()
        }

        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            if (submitButton) {
                submitButton.disabled = true
            }

            postForm(form)
                .then((result) => {
                    const wis = result.wis ?? Number(input.value)
                    label.textContent = String(wis)
                    input.value = String(wis)
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

    private bindDateCreated(container: HTMLElement): void {
        const label = container.querySelector<HTMLElement>('.powerTrail-date-created-label')
        const changeButton = container.querySelector<HTMLButtonElement>('.powerTrail-date-created-change-button')
        const form = container.querySelector<HTMLFormElement>('.powerTrail-date-created-form')
        const input = form?.querySelector<HTMLInputElement>('input[name="dateCreated"]')
        const submitButton = form?.querySelector<HTMLButtonElement>('button[type="submit"]')

        if (!label || !changeButton || !form || !input) {
            return
        }

        changeButton.onclick = () => {
            label.hidden = true
            changeButton.hidden = true
            form.hidden = false
            input.focus()
        }

        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            if (submitButton) {
                submitButton.disabled = true
            }

            postForm(form)
                .then((result) => {
                    const dateCreated = result.dateCreated ?? input.value
                    label.textContent = dateCreated
                    input.value = dateCreated
                })
                .catch((error: unknown) => showError(error, form))
                .finally(() => {
                    if (submitButton) {
                        submitButton.disabled = false
                    }
                    form.hidden = true
                    label.hidden = false
                    changeButton.hidden = false
                })
        }
    }

    private bindDescription(container: HTMLElement): void {
        const editButton = container.querySelector<HTMLButtonElement>('.powerTrail-description-edit-button')
        const targetId = container.dataset.descriptionTarget
        const target = targetId ? document.getElementById(targetId) : null
        const form = target?.querySelector<HTMLFormElement>('.powerTrail-description-form') ?? null
        const panel = target?.querySelector<HTMLElement>('.powerTrail-description-panel') ?? null
        const textarea = target?.querySelector<HTMLTextAreaElement>('textarea') ?? null
        const hiddenInput = form?.querySelector<HTMLInputElement>('input[name="description"]')
        const saveButton = form?.querySelector<HTMLButtonElement>('.powerTrail-description-save-button')
        const hideButton = target?.querySelector<HTMLButtonElement>('.powerTrail-description-hide-button') ?? null
        const message = target?.querySelector<HTMLElement>('.powerTrail-description-message') ?? null

        if (!editButton || !target || !form || !panel || !textarea || !hiddenInput) {
            return
        }

        const getEditor = (): TinyMceEditor | null => {
            const tinymce = (window as Window & { tinymce?: TinyMceGlobal }).tinymce
            return tinymce?.get(textarea.id) ?? null
        }

        const setMessage = (text: string, isError = false): void => {
            if (!message) {
                return
            }

            message.textContent = text
            message.classList.toggle('text-danger', isError)
            message.classList.toggle('text-success', !isError && text !== '')
        }

        const openPanel = (): void => {
            panel.style.overflow = 'hidden'
            panel.style.maxHeight = `${panel.scrollHeight}px`
            panel.classList.add('is-open')
            panel.setAttribute('aria-hidden', 'false')
            editButton.disabled = true
            setMessage('')

            window.setTimeout(() => {
                if (!panel.classList.contains('is-open')) {
                    return
                }

                panel.style.maxHeight = `${panel.scrollHeight}px`
                panel.style.overflow = 'visible'
                const editor = getEditor()
                editor?.getContainer()?.classList.add('w-100')
                editor?.focus()
                window.dispatchEvent(new Event('resize'))
            }, 350)
        }

        const closePanel = (): void => {
            panel.style.overflow = 'hidden'
            panel.style.maxHeight = '0px'
            panel.classList.remove('is-open')
            panel.setAttribute('aria-hidden', 'true')
            editButton.disabled = false
            setMessage('')
        }

        editButton.onclick = openPanel
        if (hideButton) {
            hideButton.onclick = closePanel
        }

        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            const editor = getEditor()
            hiddenInput.value = editor ? editor.getContent() : textarea.value

            if (saveButton) {
                saveButton.disabled = true
            }

            postForm(form)
                .then((result) => {
                    setMessage(result.message || '')
                })
                .catch((error: unknown) => {
                    setMessage(
                        error instanceof Error
                            ? error.message
                            : form.dataset.lnErrorMessage || 'Error',
                        true
                    )
                })
                .finally(() => {
                    if (saveButton) {
                        saveButton.disabled = false
                    }
                })
        }
    }

    private bindFounders(container: HTMLElement): void {
        const list = container.querySelector<HTMLElement>('.powerTrail-founders-list')
        const editButton = container.querySelector<HTMLButtonElement>('.powerTrail-founders-edit-button')
        const finishButton = container.querySelector<HTMLButtonElement>('.powerTrail-founders-finish-button')
        const addForm = container.querySelector<HTMLFormElement>('.powerTrail-add-founder-form')
        const input = addForm?.querySelector<HTMLInputElement>('input[name="username"]')
        const addSubmitButton = addForm?.querySelector<HTMLButtonElement>('button[type="submit"]')

        if (!list || !editButton || !finishButton || !addForm || !input) {
            return
        }

        container
            .querySelectorAll<HTMLFormElement>('.powerTrail-remove-founder-form')
            .forEach((form) => this.bindRemoveFounderForm(form, container))

        const setEditing = (editing: boolean): void => {
            container.classList.toggle('powerTrail-founders--editing', editing)
            editButton.hidden = editing
            addForm.hidden = !editing
            finishButton.hidden = !editing
            container
                .querySelectorAll<HTMLButtonElement>('.powerTrail-founder-remove')
                .forEach((button) => {
                    button.hidden = !editing
                })

            if (editing) {
                input.focus()
            } else {
                input.value = ''
                this.setFoundersMessage(container, '')
            }
        }

        editButton.onclick = () => setEditing(true)
        finishButton.onclick = () => setEditing(false)

        addForm.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            if (addSubmitButton) {
                addSubmitButton.disabled = true
            }

            postForm(addForm)
                .then((result) => {
                    if (result.founder) {
                        this.appendFounder(container, result.founder)
                    }
                    input.value = ''
                    this.setFoundersMessage(container, result.message || '')
                })
                .catch((error: unknown) => {
                    this.setFoundersMessage(
                        container,
                        error instanceof Error
                            ? error.message
                            : addForm.dataset.lnErrorMessage || 'Error',
                        true
                    )
                })
                .finally(() => {
                    if (addSubmitButton) {
                        addSubmitButton.disabled = false
                    }
                    input.focus()
                })
        }
    }

    private bindRemoveFounderForm(form: HTMLFormElement, container: HTMLElement): void {
        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            if (!window.confirm(form.dataset.lnConfirmMessage || '')) {
                return
            }

            // Start the request before detaching the form, then remove the
            // founder immediately regardless of the AJAX result.
            const request = postForm(form)
            form.closest<HTMLElement>('.powerTrail-founder')?.remove()
            this.updateFoundersEmptyState(container)

            request
                .then((result) => {
                    this.setFoundersMessage(container, result.message || '')
                })
                .catch((error: unknown) => {
                    this.setFoundersMessage(
                        container,
                        error instanceof Error
                            ? error.message
                            : form.dataset.lnErrorMessage || 'Error',
                        true
                    )
                })
        }
    }

    private appendFounder(container: HTMLElement, founder: FounderData): void {
        const list = container.querySelector<HTMLElement>('.powerTrail-founders-list')
        if (!list) {
            return
        }

        container.querySelector('.powerTrail-founders-empty')?.remove()

        const form = document.createElement('form')
        form.className = 'powerTrail-founder powerTrail-remove-founder-form d-inline-flex align-items-center mr-5'
        form.method = 'post'
        form.action = container.dataset.removeAction || ''
        form.dataset.lnConfirmMessage = container.dataset.lnConfirmMessage || ''
        form.dataset.lnErrorMessage = container.dataset.lnRemoveError || ''

        const link = document.createElement('a')
        link.href = `/viewprofile.php?userid=${encodeURIComponent(String(founder.user_id))}`
        link.target = '_blank'
        link.rel = 'noopener noreferrer'
        link.textContent = founder.username

        const userId = document.createElement('input')
        userId.type = 'hidden'
        userId.name = 'userId'
        userId.value = String(founder.user_id)

        const removeButton = document.createElement('button')
        removeButton.type = 'submit'
        removeButton.className = 'red-button-small px-5 ml-5 powerTrail-founder-remove'
        removeButton.title = container.dataset.lnRemoveLabel || 'Remove'
        removeButton.textContent = '\u00d7'
        removeButton.hidden = !container.classList.contains('powerTrail-founders--editing')

        form.append(link, userId, removeButton)
        list.append(form)
        this.bindRemoveFounderForm(form, container)
    }

    private updateFoundersEmptyState(container: HTMLElement): void {
        const list = container.querySelector<HTMLElement>('.powerTrail-founders-list')
        if (!list) {
            return
        }

        const hasFounders = list.querySelector('.powerTrail-founder') !== null
        const empty = container.querySelector<HTMLElement>('.powerTrail-founders-empty')

        if (hasFounders) {
            empty?.remove()
            return
        }

        if (!empty) {
            const emptyMessage = document.createElement('span')
            emptyMessage.className = 'powerTrail-founders-empty'
            emptyMessage.textContent = container.dataset.lnNoFounders || ''
            list.append(emptyMessage)
        }
    }

    private setFoundersMessage(container: HTMLElement, message: string, isError = false): void {
        const element = container.querySelector<HTMLElement>('.powerTrail-founders-message')
        if (!element) {
            return
        }

        element.textContent = message
        element.classList.toggle('text-danger', isError)
        element.classList.toggle('text-success', !isError && message !== '')
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