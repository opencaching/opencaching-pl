interface GainerUser {
    userId: number
    username: string
    currVal: number
    levelDate: string
}

interface GainersData {
    badgeId: number
    level: number
    page: number
    perPage: number
    total: number
    users: GainerUser[]
}

export class BadgeGainersComponent {
    private readonly root: ParentNode
    private badgeId: number | null = null
    private readonly perPage = 20

    constructor(root: ParentNode = document) {
        this.root = root
    }

    init(): void {
        const rootEl = this.root.querySelector<HTMLElement>('#badge-gainers-root')
        if (!rootEl) {
            return
        }
        const badgeIdAttr = rootEl.getAttribute('data-badge-id')
        if (!badgeIdAttr) {
            return
        }
        this.badgeId = parseInt(badgeIdAttr, 10)
        if (Number.isNaN(this.badgeId)) {
            return
        }

        const rows = this.root.querySelectorAll<HTMLElement>('.badge-level-row')
        rows.forEach((row) => {
            row.addEventListener('click', () => {
                const levelAttr = row.getAttribute('data-level')
                if (levelAttr === null) {
                    return
                }
                const level = parseInt(levelAttr, 10)
                if (Number.isNaN(level)) {
                    return
                }
                void this.loadGainers(level, 1)
            })
        })
    }

    private cacheId(level: number, page: number): string {
        return 'gainers-cache-' + this.badgeId + '-' + level + '-' + page
    }

    private escapeHtml(value: string): string {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
    }

    private renderUsers(users: GainerUser[]): string {
        let html = ''
        for (const u of users) {
            html += '<tr>'
            html += '<td><a href="/viewprofile.php?userid=' + u.userId + '" target="_blank">' + this.escapeHtml(u.username) + '</a></td>'
            html += '<td class="text-center">' + u.currVal + '</td>'
            html += '<td>' + this.escapeHtml(u.levelDate) + '</td>'
            html += '</tr>'
        }
        if (users.length === 0) {
            html = '<tr><td colspan="3">No results</td></tr>'
        }
        return html
    }

    private renderPagination(total: number, page: number, level: number): string {
        const pages = Math.ceil(total / this.perPage)
        if (pages < 2) {
            return ''
        }
        const listSize = 5
        let leftPage = page - Math.floor(listSize / 2)
        let rightPage = page + Math.floor(listSize / 2)
        if (listSize % 2 === 0) {
            leftPage++
        }
        if (leftPage <= 0) {
            rightPage += 1 - leftPage
            leftPage = 1
        }
        let html = '<ul class="pagination">'
        if (leftPage > 1) {
            const destPrev = page - 1 <= 0 ? 1 : page - 1
            html += '<li><a href="#" data-level="' + level + '" data-page="1" title="first">&lt;&lt;</a></li>'
            html += '<li><a href="#" data-level="' + level + '" data-page="' + destPrev + '" title="prev">&lt;</a></li>'
        }
        for (let i = leftPage; i <= rightPage; i++) {
            if (i > pages) {
                break
            }
            if (i === page) {
                html += '<li><a class="active" href="#" data-level="' + level + '" data-page="' + i + '">' + i + '</a></li>'
            } else {
                html += '<li><a href="#" data-level="' + level + '" data-page="' + i + '">' + i + '</a></li>'
            }
        }
        if (pages > rightPage) {
            const destNext = page + 1 > pages ? pages : page + 1
            html += '<li><a href="#" data-level="' + level + '" data-page="' + destNext + '" title="next">&gt;</a></li>'
            html += '<li><a href="#" data-level="' + level + '" data-page="' + pages + '" title="last">&gt;&gt;</a></li>'
        }
        html += '</ul>'
        return html
    }

    private showCached(key: string): boolean {
        const cached = document.getElementById(key)
        if (!cached) {
            return false
        }
        const json = cached.getAttribute('data-json')
        if (!json) {
            return false
        }
        const data = JSON.parse(json) as GainersData
        const tbody = document.getElementById('gainers-tbody')
        const pag = document.getElementById('gainers-pagination')
        if (tbody) {
            tbody.innerHTML = this.renderUsers(data.users)
        }
        if (pag) {
            pag.innerHTML = this.renderPagination(data.total, data.page, data.level)
        }
        this.bindPagination(data.level)
        return true
    }

    private storeCache(key: string, data: GainersData): void {
        const div = document.createElement('div')
        div.id = key
        div.classList.add('hidden')
        div.setAttribute('data-json', JSON.stringify(data))
        const store = document.getElementById('gainers-cache-store')
        if (store) {
            store.appendChild(div)
        }
    }

    private async loadGainers(level: number, page: number): Promise<void> {
        const frame = document.getElementById('badge-gainers-frame')
        if (frame) {
            frame.classList.remove('hidden')
        }
        const key = this.cacheId(level, page)
        if (this.showCached(key)) {
            return
        }
        const tbody = document.getElementById('gainers-tbody')
        const pag = document.getElementById('gainers-pagination')
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="3">Loading...</td></tr>'
        }
        if (pag) {
            pag.innerHTML = ''
        }
        if (this.badgeId === null) {
            return
        }
        try {
            const formData = new FormData()
            formData.append('badge_id', String(this.badgeId))
            formData.append('level', String(level))
            formData.append('page', String(page))
            const resp = await fetch('/Badges.Badges/gainers', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            })
            const data = (await resp.json()) as { status: string; message?: string } & Partial<GainersData>
            if (data.status !== 'OK') {
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="3">' + this.escapeHtml(data.message || 'Error') + '</td></tr>'
                }
                return
            }
            const fullData = data as GainersData
            this.storeCache(key, fullData)
            if (tbody) {
                tbody.innerHTML = this.renderUsers(fullData.users)
            }
            if (pag) {
                pag.innerHTML = this.renderPagination(fullData.total, fullData.page, fullData.level)
            }
            this.bindPagination(fullData.level)
        } catch (e) {
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="3">Error</td></tr>'
            }
        }
    }

    private bindPagination(level: number): void {
        const links = document.querySelectorAll('#gainers-pagination a[data-page]')
        links.forEach((link) => {
            const anchor = link as HTMLAnchorElement
            anchor.onclick = (ev: Event) => {
                ev.preventDefault()
                const target = ev.currentTarget as HTMLElement
                const pAttr = target.getAttribute('data-page')
                const lAttr = target.getAttribute('data-level')
                const p = pAttr ? parseInt(pAttr, 10) : 1
                const lvl = lAttr ? parseInt(lAttr, 10) : level
                if (Number.isNaN(p) || Number.isNaN(lvl)) {
                    return
                }
                void this.loadGainers(lvl, p)
            }
        })
    }
}
