export class BadgePopupComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        const el = this.root.querySelector<HTMLElement>('#badge-popup')
        if (!el) {
            return
        }
        const $ = (window as any).$
        if (typeof $ !== 'function' || !$.fn || !$.fn.dialog) {
            return
        }
        const title = el.getAttribute('data-title') || 'Badge'
        $(el).dialog({
            title: title,
            modal: true,
            width: 550,
            close: () => {
                el.remove()
            },
        })
    }
}
