/**
 * Academic Template PDF Builder
 * Traditional academic CV style with red accent headings
 *
 * Design Elements:
 * - Red/Gray color scheme (#c41e3a header/accent, #374151 body)
 * - Centered header with horizontal rules
 * - Single column layout
 * - Formal contact information
 * - Red section dividers
 */

import {
    createAcademicHeader
} from '../builders/section-headers.js'

import {
    buildWorkExperienceSection,
    buildEducationSection,
    buildSkillsListSection,
    buildProjectsSection,
    buildCertificationsSection,
    buildMembershipsSection,
    buildInterestsSection,
    buildProfessionalSummarySection
} from '../builders/section-builders.js'

import {
    buildDocumentConfig
} from '../builders/style-presets.js'

import {
    decodeHtmlEntities,
    hasVisibleText,
    createDivider,
    mergeTemplateCustomization
} from '../builders/utils.js'

/**
 * Build academic template PDF document definition
 */
export function buildDocDefinition({ cvData, profile, config, cvUrl, qrCodeImage, templateId }) {
    const baseTemplate = {
        id: 'academic',
        colors: {
            header: '#c41e3a',
            body: '#374151',
            accent: '#c41e3a',
            muted: '#64748b',
            divider: '#c41e3a',
            link: '#b91c1c'
        }
    }
    const template = mergeTemplateCustomization(baseTemplate, config?.customization)

    const sections = config?.sections || {}
    const includePhoto = config?.includePhoto !== false
    const includeQRCode = config?.includeQRCode !== false

    const docConfig = buildDocumentConfig(template, 'conservative', config?.customization || {}, { font: 'Times' })
    const content = []

    // HEADER SECTION
    if (sections.profile !== false) {
        const headerContent = []
        if (profile?.full_name) {
            headerContent.push({
                text: decodeHtmlEntities(profile.full_name).toUpperCase(),
                fontSize: 22,
                bold: true,
                color: template.colors.header,
                alignment: 'center',
                margin: [0, 0, 0, 8]
            })
        }

        const contactParts = []
        if (profile?.location) contactParts.push(decodeHtmlEntities(profile.location))
        if (profile?.phone) contactParts.push(decodeHtmlEntities(profile.phone))
        if (profile?.email) contactParts.push(decodeHtmlEntities(profile.email))

        if (contactParts.length > 0) {
            headerContent.push({
                text: contactParts.join(' • '),
                fontSize: 10,
                color: template.colors.muted,
                alignment: 'center',
                margin: [0, 0, 0, 4]
            })
        }

        if (profile?.linkedin_url) {
            headerContent.push({
                text: 'LinkedIn',
                link: decodeHtmlEntities(profile.linkedin_url),
                fontSize: 9,
                color: template.colors.link,
                decoration: 'underline',
                alignment: 'center',
                margin: [0, 0, 0, 4]
            })
        }

        if (hasVisibleText(profile?.bio)) {
            headerContent.push({
                text: decodeHtmlEntities(profile.bio),
                fontSize: 10,
                color: template.colors.body,
                alignment: 'center',
                italics: true,
                margin: [0, 6, 0, 0]
            })
        }

        headerContent.push(createDivider(template.colors.divider, 1.5, [0, 12, 0, 8]))

        const hasPhoto = includePhoto && profile?.photo_base64 && /^data:image\/(jpeg|png);base64,/.test(profile.photo_base64)
        const hasQR = includeQRCode && cvUrl

        if (hasQR) {
            // QR in same position as photo: centered above header (matches "instead of photo")
            content.push({
                stack: [
                    { qr: cvUrl, fit: 110, alignment: 'center', margin: [0, 0, 0, 8] },
                    { text: 'View Online', link: cvUrl, fontSize: 8, color: template.colors.link, alignment: 'center', margin: [0, 0, 0, 12] },
                    ...headerContent
                ]
            })
        } else if (hasPhoto) {
            content.push({ stack: [{ image: 'profilePhoto', fit: [70, 70], alignment: 'center', margin: [0, 0, 0, 12] }, ...headerContent] })
        } else {
            content.push(...headerContent)
        }
    }

    if (sections.professionalSummary !== false && cvData.professional_summary) {
        content.push(...createAcademicHeader('Professional Summary', template))
        content.push(...buildProfessionalSummarySection(cvData.professional_summary, template, { fontSize: 11, showStrengths: true }))
    }

    if (sections.workExperience !== false && Array.isArray(cvData.work_experience) && cvData.work_experience.length > 0) {
        content.push(...createAcademicHeader('Professional Experience', template))
        content.push(...buildWorkExperienceSection(cvData.work_experience, template, { showDates: true, showDescription: true, showResponsibilities: true, fontSize: 11, layout: 'academic' }))
    }

    if (sections.education !== false && Array.isArray(cvData.education) && cvData.education.length > 0) {
        content.push(...createAcademicHeader('Education', template))
        content.push(...buildEducationSection(cvData.education, template, { showDates: true, showDescription: true, fontSize: 11, layout: 'academic' }))
    }

    if (sections.skills !== false && Array.isArray(cvData.skills) && cvData.skills.length > 0) {
        content.push(...createAcademicHeader('Skills & Competencies', template))
        content.push(...buildSkillsListSection(cvData.skills, template, { fontSize: 11, showLevel: false, groupByCategory: true }))
    }

    if (sections.projects !== false && Array.isArray(cvData.projects) && cvData.projects.length > 0) {
        content.push(...createAcademicHeader('Projects', template))
        content.push(...buildProjectsSection(cvData.projects, template, { showDates: true, showUrl: true, fontSize: 11 }))
    }

    if (sections.certifications !== false && Array.isArray(cvData.certifications) && cvData.certifications.length > 0) {
        content.push(...createAcademicHeader('Certifications', template))
        content.push(...buildCertificationsSection(cvData.certifications, template, { showDates: true, fontSize: 11 }))
    }

    if (sections.qualificationEquivalence !== false && Array.isArray(cvData.qualification_equivalence) && cvData.qualification_equivalence.length > 0) {
        content.push(...createAcademicHeader('Professional Qualification Equivalence', template))
        cvData.qualification_equivalence.forEach((qual, index) => {
            const qualContent = []
            if (qual.level_name) qualContent.push({ text: decodeHtmlEntities(qual.level_name), fontSize: 12, bold: true, color: template.colors.body })
            if (hasVisibleText(qual.description)) qualContent.push({ text: decodeHtmlEntities(qual.description), fontSize: 11, color: template.colors.body, margin: [0, 2, 0, 4] })
            if (Array.isArray(qual.supporting_evidence) && qual.supporting_evidence.length > 0) {
                const evidenceItems = qual.supporting_evidence.filter(e => hasVisibleText(e.evidence)).map(e => decodeHtmlEntities(e.evidence))
                if (evidenceItems.length > 0) qualContent.push({ ul: evidenceItems, fontSize: 10, color: template.colors.body, margin: [15, 0, 0, 0] })
            }
            content.push({ stack: qualContent, margin: [0, 0, 0, index < cvData.qualification_equivalence.length - 1 ? 8 : 0] })
        })
    }

    if (sections.memberships !== false && Array.isArray(cvData.professional_memberships) && cvData.professional_memberships.length > 0) {
        content.push(...createAcademicHeader('Professional Memberships', template))
        content.push(...buildMembershipsSection(cvData.professional_memberships, template, { showDates: true, fontSize: 11 }))
    }

    if (sections.interests !== false && Array.isArray(cvData.interests) && cvData.interests.length > 0) {
        content.push(...createAcademicHeader('Interests & Activities', template))
        content.push(...buildInterestsSection(cvData.interests, template, { fontSize: 11, showDescription: false, layout: 'inline' }))
    }

    const footer = (currentPage, pageCount) => {
        const pageText = `${profile?.full_name ? decodeHtmlEntities(profile.full_name) + ' - ' : ''}Page ${currentPage} of ${pageCount}`
        const mutedColor = template.colors.muted || '#64748b'
        const year = new Date().getFullYear()
        const items = [{ text: pageText, alignment: 'center', fontSize: 9, color: mutedColor }]
        const brandingParts = [
            { text: `Simple CV Builder Designed, Developed and Delivered by William Ellis. © ${year}` }
        ]
        if (config?.siteUrl) {
            brandingParts.push({ text: ' ', fontSize: 7 }, { text: 'simple-cv-builder.com', link: config.siteUrl, fontSize: 7 })
        }
        items.push({
            text: brandingParts,
            alignment: 'center',
            fontSize: 7,
            color: mutedColor,
            margin: [0, 4, 0, 0]
        })
        return { stack: items, margin: [0, 20, 0, 0] }
    }

    return {
        ...docConfig,
        pageMargins: [45, 60, 45, 60],
        content: content,
        footer: footer,
        info: { title: `${profile?.full_name || 'CV'} - Curriculum Vitae`, author: profile?.full_name || 'CV Builder User', subject: 'Curriculum Vitae', keywords: 'CV, Resume, Curriculum Vitae' }
    }
}
