// Use dynamic import with cache busting
const CACHE_BUSTER = new Date().getTime()
let templateModule = null

// Load the template module dynamically with cache busting
async function loadTemplateModule() {
    if (!templateModule) {
        templateModule = await import(`/templates/index.js?v=${CACHE_BUSTER}`)
    }
    return templateModule
}

const subscriptionContext = window.SubscriptionContext || {}
const allowedTemplateIds = new Set(subscriptionContext.allowedTemplateIds || [])
const pdfEnabled = subscriptionContext.pdfEnabled !== false

function filterTemplatesForPlan(templates) {
    if (allowedTemplateIds.size === 0) {
        return templates
    }
    return templates.filter((template) => allowedTemplateIds.has(template.id))
}

async function listTemplatesForPlan() {
    const module = await loadTemplateModule()
    return filterTemplatesForPlan(module.listTemplates())
}

async function getImageAsBase64FromBlob(blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.onloadend = () => resolve(reader.result)
        reader.onerror = reject
        reader.readAsDataURL(blob)
    })
}

async function getImageAsBase64(url) {
    try {
        const response = await fetch(url)
        if (!response.ok) {
            throw new Error(`Failed to fetch image: ${response.status}`)
        }
        const blob = await response.blob()

        if (blob.type === 'image/webp' || blob.type.includes('webp')) {
            return new Promise((resolve, reject) => {
                const img = new Image()
                img.crossOrigin = 'anonymous'
                img.onload = () => {
                    const canvas = document.createElement('canvas')
                    canvas.width = Math.min(img.width, 800)
                    canvas.height = Math.min(img.height, 800)
                    const ctx = canvas.getContext('2d')
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height)
                    const convertedDataUrl = canvas.toDataURL('image/jpeg', 0.85)
                    resolve(convertedDataUrl)
                }
                img.onerror = () => {
                    getImageAsBase64FromBlob(blob).then(resolve).catch(reject)
                }
                img.src = URL.createObjectURL(blob)
            })
        }

        return getImageAsBase64FromBlob(blob)
    } catch (error) {
        console.error('Error loading image for PDF:', error)
        return null
    }
}

async function buildDocDefinition(cvData, profile, config, templateId, cvUrl, qrCodeImage) {
    if (!pdfEnabled) {
        throw new Error('PDF export is not available for your current plan.')
    }
    const module = await loadTemplateModule()
    const targetTemplateId = templateId || module.DEFAULT_TEMPLATE_ID

    if (allowedTemplateIds.size > 0 && !allowedTemplateIds.has(targetTemplateId)) {
        throw new Error('This template is not available for your current plan.')
    }

    const pdfRenderer = module.getPdfRenderer(targetTemplateId)

    if (!pdfRenderer || typeof pdfRenderer.buildDocDefinition !== 'function') {
        throw new Error(`PDF renderer not registered for template: ${targetTemplateId}`)
    }

    // Load the actual builder function (async loader)
    const builderFunction = await pdfRenderer.buildDocDefinition()

    return builderFunction({
        cvData,
        profile,
        config,
        cvUrl,
        qrCodeImage,
        templateId: targetTemplateId
    })
}

const pdfGenerator = {
    listTemplates: () => listTemplatesForPlan(),
    buildDocDefinition,
    getImageAsBase64,
    isHeaderPushedDown: (cvData) => {
        const hasProfile = !!(
            cvData &&
            cvData.profile &&
            (cvData.profile.location || cvData.profile.email || cvData.profile.phone || cvData.profile.bio)
        )
        const hasSummary = !!(
            cvData &&
            cvData.professional_summary &&
            (cvData.professional_summary.description ||
                (cvData.professional_summary.strengths && cvData.professional_summary.strengths.length))
        )
        return hasProfile && !hasSummary
    }
}

window.PdfGenerator = pdfGenerator
