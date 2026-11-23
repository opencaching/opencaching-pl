export class BadgeGpxComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        const panel = this.root.querySelector<HTMLElement>('#badgeGpxOptions')
        if (!panel) {
            return
        }

        document.addEventListener('click', (event) => {
            const target = event.target as HTMLElement | null
            if (!target) {
                return
            }
            const btn = target.closest('[data-toggle-gpx]')
            if (!btn) {
                return
            }
            event.preventDefault()
            console.log('test')
            panel.classList.toggle('collapsed')
        })
    }
}