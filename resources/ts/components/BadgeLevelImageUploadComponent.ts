import { UploadResult } from '../types/UploadResult'

export class BadgeLevelImageUploadComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        const container = this.root.querySelector<HTMLElement>('[data-badge-level-upload]')
        if (!container) {
            return
        }
        const badgeIdAttr = container.getAttribute('data-badge-id')
        if (!badgeIdAttr) {
            return
        }
        const badgeId = parseInt(badgeIdAttr, 10)
        if (Number.isNaN(badgeId)) {
            return
        }
        const modelJson = container.getAttribute('data-upload-model')
        if (!modelJson) {
            return
        }
        let baseModel: any
        try {
            baseModel = JSON.parse(modelJson)
        } catch {
            return
        }
        const previewLabel = container.getAttribute('data-ln-preview') || 'Preview'
        const deleteLabel = container.getAttribute('data-ln-delete') || 'Delete'
        const noneLabel = container.getAttribute('data-ln-none') || ''

        const buttons = this.root.querySelectorAll<HTMLButtonElement>('.badge-level-upload-btn')
        buttons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault()
                const levelAttr = btn.getAttribute('data-level')
                if (levelAttr === null) {
                    return
                }
                const level = parseInt(levelAttr, 10)
                if (Number.isNaN(level)) {
                    return
                }

                const ocUploadFn = (window as any).ocUpload
                if (typeof ocUploadFn !== 'function') {
                    window.alert('Upload not available')
                    return
                }

                const model = JSON.parse(JSON.stringify(baseModel))
                model.submitUrl = '/Badges.Badges/uploadLevelImageAjax/' + badgeId + '/' + level

                ocUploadFn(model, (uploadResult: UploadResult) => {
                    if (!uploadResult || !uploadResult.success) {
                        console.log('upload fail', uploadResult && uploadResult.message)
                        return
                    }
                    const newImage = Array.isArray(uploadResult.newFiles)
                        ? uploadResult.newFiles[0]
                        : uploadResult.newFiles
                    if (!newImage) {
                        return
                    }
                    const cell = this.root.querySelector<HTMLElement>('[data-level-image="' + level + '"]')
                    if (cell) {
                        const url = newImage.charAt(0) === '/' ? newImage : '/' + newImage
                        cell.innerHTML = '<a href="' + url + '" target="_blank">' + this.escapeHtml(previewLabel) + '</a>'

                        const delBtn = document.createElement('button')
                        delBtn.type = 'button'
                        delBtn.className = 'blue-button-small mt-5'
                        delBtn.textContent = deleteLabel
                        delBtn.addEventListener('click', () => {
                            void fetch('/Badges.Badges/deleteLevelImageAjax/' + badgeId + '/' + level, {
                                method: 'POST',
                                credentials: 'same-origin',
                            })
                                .then((resp) => resp.json())
                                .then((data: { status: string }) => {
                                    if (data.status !== 'OK') {
                                        return
                                    }
                                    if (noneLabel !== '') {
                                        cell.innerHTML = this.escapeHtml(noneLabel)
                                    } else {
                                        cell.innerHTML = ''
                                    }
                                })
                        })
                        cell.appendChild(delBtn)
                    }
                })
            })
        })
    }

    private escapeHtml(value: string): string {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
    }
}
