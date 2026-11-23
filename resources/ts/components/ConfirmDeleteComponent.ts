export class ConfirmDeleteComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        this.root.addEventListener('click', (event) => {
            const target = event.target as HTMLElement | null
            if (!target) {
                return
            }
            const button = target.closest('button[type="submit"][data-confirm], input[type="submit"][data-confirm]') as HTMLElement | null
            if (!button) {
                return
            }
            const formId = button.getAttribute('form')
            const form = formId
                ? (document.getElementById(formId) as HTMLFormElement | null)
                : (button.closest('form') as HTMLFormElement | null)
            if (!form) {
                return
            }
            const message = button.getAttribute('data-confirm')
            if (message === null) {
                return
            }
            if (!window.confirm(message)) {
                event.preventDefault()
                event.stopPropagation()
            }
        }, true)
    }
}
