export class BadgeRecalculateComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        const buttons = this.root.querySelectorAll<HTMLButtonElement>('.badge-recalculate-btn')
        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const badgeId = btn.getAttribute('data-badge-id')
                const userId = btn.getAttribute('data-user-id')
                if (!badgeId || !userId) {
                    return
                }
                btn.disabled = true

                void fetch('/Badges.Badges/recalculateAjax/' + badgeId + '/' + userId, {
                    method: 'POST',
                    credentials: 'same-origin',
                })
                    .then((resp) => {
                        if (!resp.ok) {
                            throw new Error('HTTP ' + resp.status)
                        }
                        return resp.json()
                    })
                    .then((data: { status: string; badgeId?: number; userId?: number; changed?: boolean }) => {
                        if (data.status !== 'OK') {
                            btn.disabled = false
                            return
                        }
                        const targetBadgeId = data.badgeId ?? parseInt(badgeId, 10)
                        const targetUserId = data.userId ?? parseInt(userId, 10)

                        const form = document.createElement('form')
                        form.method = 'POST'
                        form.action = '/Badges.MyBadges/badge/' + targetBadgeId + '/' + targetUserId

                        const input = document.createElement('input')
                        input.type = 'hidden'
                        input.name = 'badgesPopupFor'
                        input.value = String(targetBadgeId)
                        form.appendChild(input)

                        document.body.appendChild(form)
                        form.submit()
                    })
                    .catch(() => {
                        btn.disabled = false
                    })
            })
        })
    }
}
