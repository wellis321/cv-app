import { render as renderProfessionalBluePreview } from './default/preview.js'
import { buildDocDefinition as buildProfessionalBluePdf } from './default/pdf.js'

const DEFAULT_TEMPLATE_ID = 'professional'

const templateRegistry = {
    professional: {
        id: 'professional',
        name: 'Professional Blue',
        description: 'Clean layout with blue accent accents and structured typography.',
        colors: {
            header: '#1f2937',
            body: '#374151',
            accent: '#2563eb',
            muted: '#6b7280',
            divider: '#d1d5db',
            link: '#2563eb'
        },
        sectionDivider: {
            color: '#d1d5db',
            width: 1,
            margin: [0, 4, 0, 6]
        },
        preview: {
            render: renderProfessionalBluePreview
        },
        pdf: {
            buildDocDefinition: buildProfessionalBluePdf
        }
    },
    minimal: {
        id: 'minimal',
        name: 'Minimal',
        description: 'Simplified monochrome layout with understated section dividers.',
        colors: {
            header: '#111827',
            body: '#374151',
            accent: '#111827',
            muted: '#6b7280',
            divider: '#d1d5db',
            link: '#1f2937'
        },
        sectionDivider: {
            color: '#e5e7eb',
            width: 0.75,
            margin: [0, 6, 0, 10]
        },
        preview: {
            render: renderProfessionalBluePreview
        },
        pdf: {
            buildDocDefinition: buildProfessionalBluePdf
        }
    }
}

export function getTemplateMeta(templateId) {
    return templateRegistry[templateId] || templateRegistry[DEFAULT_TEMPLATE_ID]
}

export function getPreviewRenderer(templateId) {
    const meta = getTemplateMeta(templateId)
    return meta ? meta.preview : null
}

export function getPdfRenderer(templateId) {
    const meta = getTemplateMeta(templateId)
    return meta ? meta.pdf : null
}

export function listTemplates() {
    return Object.values(templateRegistry).map(({ id, name, description }) => ({ id, name, description }))
}

export { DEFAULT_TEMPLATE_ID }
