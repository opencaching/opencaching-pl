import { UploadResult } from '../types/UploadResult'

export class BadgeImageUploadComponent {
    constructor(private readonly root: ParentNode = document) {}

    init(): void {
        const container = this.root.querySelector<HTMLElement>('[data-badge-image-upload]')
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

        const img = this.root.querySelector<HTMLImageElement>('[data-badge-image]')
        const buttons = this.root.querySelectorAll<HTMLButtonElement>('.badge-image-upload-btn')

        const ensureDeleteButton = (): HTMLButtonElement => {
            let delBtn = this.root.querySelector<HTMLButtonElement>('.badge-image-delete-btn')
            if (delBtn) {
                return delBtn
            }
            delBtn = document.createElement('button')
            delBtn.type = 'button'
            delBtn.className = 'blue-button-small badge-image-delete-btn mt-10'
            delBtn.textContent = deleteLabel
            const uploadBtn = this.root.querySelector<HTMLButtonElement>('.badge-image-upload-btn')
            if (uploadBtn && uploadBtn.parentNode) {
                uploadBtn.parentNode.insertBefore(delBtn, uploadBtn.nextSibling)
            } else if (container.parentNode) {
                container.parentNode.appendChild(delBtn)
            }
            return delBtn
        }

        const bindDelete = (delBtn: HTMLButtonElement): void => {
            delBtn.onclick = () => {
                const ocUploadFn = (window as any).fetch
                void fetch('/Badges.Badges/deleteImageAjax/' + badgeId, {
                    method: 'POST',
                    credentials: 'same-origin',
                })
                    .then((resp) => resp.json())
                    .then((data: { status: string }) => {
                        if (data.status !== 'OK') {
                            return
                        }
                        if (img) {
                            img.src = ''
                            img.classList.add('hidden')
                        }
                        delBtn.remove()
                    })
            }
        }

        // If an image already exists, show delete button
        if (img && img.getAttribute('src') && img.getAttribute('src') !== '') {
            bindDelete(ensureDeleteButton())
        }

        buttons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault()
                const ocUploadFn = (window as any).ocUpload
                if (typeof ocUploadFn !== 'function') {
                    window.alert('Upload not available')
                    return
                }

                const model = JSON.parse(JSON.stringify(baseModel))
                model.submitUrl = '/Badges.Badges/uploadImageAjax/' + badgeId

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
                    const url = newImage.charAt(0) === '/' ? newImage : '/' + newImage
                    if (img) {
                        img.src = url
                        img.classList.remove('hidden')
                    }
                    bindDelete(ensureDeleteButton())
                })
            })
        })
    }
}
