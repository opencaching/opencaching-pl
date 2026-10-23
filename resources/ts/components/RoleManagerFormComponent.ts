export class RoleManagerFormComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        this.root
            .querySelectorAll<HTMLFormElement>('.role-manager-form')
            .forEach((form) => this.bindForm(form))
    }

    private bindForm(form: HTMLFormElement): void {
        form.onsubmit = (event: SubmitEvent) => {
            event.preventDefault()

            const submitter = event.submitter instanceof HTMLButtonElement
                ? event.submitter
                : null
            const isRemoveAll = submitter?.dataset.lnAction === 'remove_all'

            if (isRemoveAll) {
                const confirmMessage = form.dataset.lnConfirmRemoveAll || ''
                if (confirmMessage && !window.confirm(confirmMessage)) {
                    return
                }
            }

            void fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json'
                }
            })
                .catch(() => undefined)
                .finally(() => window.location.reload())
        }
    }
}
