export class BadgeGpxComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        const btn = this.root.querySelector<HTMLButtonElement>('[data-toggle-gpx]')
        const panel = this.root.querySelector<HTMLElement>('#badgeGpxOptions')
        if (!btn || !panel) {
            return
        }

        btn.addEventListener('click', (event) => {
            event.preventDefault()
            panel.classList.toggle('is-open')
        })
    }
}