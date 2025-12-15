/**
 * Escapes HTML special characters.
 * @param {string} value
 * @returns {string}
 */
function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

/**
 * Formats a date string as MM/YYYY.
 * @param {string} dateStr
 * @returns {string}
 */
function formatCvPreviewDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (Number.isNaN(date.getTime())) return dateStr;
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${month}/${year}`;
}

/**
 * Renders the preview for the "Professional Blue" template.
 * @param {HTMLElement} container
 * @param {object} context
 * @param {object} context.cvData
 * @param {object} context.profile
 * @param {object} context.sections
 * @param {boolean} context.includePhoto
 * @param {boolean} context.includeQr
 * @param {object} context.template
 */
export function render(container, { cvData, profile, sections, includePhoto, includeQr, template }) {
    if (!container) {
        console.error('Preview container not provided');
        return;
    }

    const palette = (template && template.colors) || {};
    const headingColor = palette.header || '#1f2937';
    const bodyColor = palette.body || '#374151';
    const accentColor = palette.accent || '#2563eb';
    const mutedColor = palette.muted || '#6b7280';

    const addSectionHeading = (title) => `<h2 class="text-xl font-bold mb-3" style="color:${headingColor};">${title}</h2>`;

    let html = '';

    if (sections.profile) {
        html += `<div class="mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2" style="color:${headingColor};">${escapeHtml(profile.full_name || 'Your Name')}</h1>`;

        if (profile.location) {
            html += `<p class="text-sm mb-1" style="color:${bodyColor};">${escapeHtml(profile.location)}</p>`;
        }

        const contactBits = [];
        if (profile.email) contactBits.push(`<span style="color:${bodyColor};">${escapeHtml(profile.email)}</span>`);
        if (profile.phone) contactBits.push(`<span style="color:${bodyColor};">${escapeHtml(profile.phone)}</span>`);
        if (profile.linkedin_url) contactBits.push(`<a href="${escapeHtml(profile.linkedin_url)}" target="_blank" style="color:${accentColor}; text-decoration: underline;">LinkedIn</a>`);

        if (contactBits.length > 0) {
            html += `<p class="text-xs mt-2" style="color:${bodyColor};">${contactBits.join(' <span style="color:${mutedColor};">|</span> ')}</p>`;
        }

        if (profile.bio && String(profile.bio).trim()) {
            html += `<p class="text-sm mt-3" style="color:${bodyColor};">${escapeHtml(profile.bio)}</p>`;
        }

        html += `</div>`;

        if (includePhoto && profile.photo_url) {
            html += `<img src="${escapeHtml(profile.photo_url)}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover border-2 border-gray-300">`;
        }

        html += `</div></div>`;
    }

    if (sections.summary && cvData.professional_summary) {
        const summary = cvData.professional_summary;
        html += '<section class="mb-6">' + addSectionHeading('Professional Summary');
        if (summary.description) {
            html += `<p class="text-sm leading-relaxed mb-3" style="color:${bodyColor};">${escapeHtml(summary.description)}</p>`;
        }
        if (summary.strengths && summary.strengths.length > 0) {
            html += `<h3 class="font-semibold text-sm mb-2" style="color:${headingColor};">Key Strengths:</h3><ul class="list-disc list-inside space-y-1 text-sm" style="color:${bodyColor};">`;
            summary.strengths.forEach((item) => {
                html += `<li>${escapeHtml(item.strength)}</li>`;
            });
            html += '</ul>';
        }
        html += '</section>';
    }

    if (sections.work && Array.isArray(cvData.work_experience) && cvData.work_experience.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Work Experience');
        cvData.work_experience.forEach((item) => {
            html += `<div class="mb-5">
                <div class="flex justify-between items-start gap-4 mb-2">
                    <div>
                        <h3 class="text-lg font-semibold" style="color:${headingColor};">${escapeHtml(item.position)}</h3>
                        <p class="text-sm" style="color:${bodyColor};">${escapeHtml(item.company_name)}</p>
                    </div>`;
            if (!item.hide_date) {
                html += `<div class="text-sm whitespace-nowrap" style="color:${mutedColor};">${formatCvPreviewDate(item.start_date)}${item.end_date ? ' - ' + formatCvPreviewDate(item.end_date) : ' - Present'}</div>`;
            }
            html += '</div>';

            if (item.description) {
                html += `<p class="text-sm leading-relaxed mb-3" style="color:${bodyColor};">${escapeHtml(item.description)}</p>`;
            }

            if (item.responsibility_categories && item.responsibility_categories.length > 0) {
                item.responsibility_categories.forEach((category) => {
                    if (category.items && category.items.length > 0) {
                        html += `<div class="mb-3">
                            <h4 class="font-semibold text-sm mb-1" style="color:${headingColor};">${escapeHtml(category.name)}:</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm" style="color:${bodyColor};">`;
                        category.items.forEach((resp) => {
                            html += `<li>${escapeHtml(resp.content)}</li>`;
                        });
                        html += '</ul></div>';
                    }
                });
            }

            html += '</div>';
        });
        html += '</section>';
    }

    if (sections.education && Array.isArray(cvData.education) && cvData.education.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Education');
        cvData.education.forEach((item) => {
            html += `<div class="mb-4">
                <h3 class="font-semibold text-base" style="color:${headingColor};">${escapeHtml(item.degree || '')}</h3>
                <p class="text-sm" style="color:${bodyColor};">${escapeHtml(item.institution || '')}</p>`;
            if (item.field_of_study) {
                html += `<p class="text-sm" style="color:${mutedColor};">${escapeHtml(item.field_of_study)}</p>`;
            }
            html += `<p class="text-xs mt-1" style="color:${mutedColor};">${formatCvPreviewDate(item.start_date)}${item.end_date ? ' - ' + formatCvPreviewDate(item.end_date) : ' - Present'}</p>`;
            html += '</div>';
        });
        html += '</section>';
    }

    if (sections.skills && Array.isArray(cvData.skills) && cvData.skills.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Skills');
        const grouped = {};
        cvData.skills.forEach((skill) => {
            const key = skill.category || 'Other';
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(skill);
        });
        Object.keys(grouped).forEach((key) => {
            html += `<div class="mb-3">
                <h3 class="font-semibold text-sm mb-1" style="color:${headingColor};">${escapeHtml(key)}:</h3>
                <div class="flex flex-wrap gap-1.5 text-xs" style="color:${bodyColor};">`;
            grouped[key].forEach((skill) => {
                html += `<span class="px-2 py-0.5 rounded bg-gray-100">${escapeHtml(skill.name)}${skill.level ? ` (${escapeHtml(skill.level)})` : ''}</span>`;
            });
            html += '</div></div>';
        });
        html += '</section>';
    }

    if (sections.projects && Array.isArray(cvData.projects) && cvData.projects.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Projects');
        cvData.projects.forEach((project) => {
            html += `<div class="mb-4">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-lg font-semibold" style="color:${headingColor};">${escapeHtml(project.title)}</h3>`;
            if (project.url) {
                html += `<a href="${escapeHtml(project.url)}" target="_blank" class="text-sm" style="color:${accentColor};">View →</a>`;
            }
            html += '</div>';

            if (project.start_date) {
                html += `<div class="text-sm mb-2" style="color:${mutedColor};">${formatCvPreviewDate(project.start_date)}${project.end_date ? ' - ' + formatCvPreviewDate(project.end_date) : ''}</div>`;
            }

            if (project.description) {
                html += `<p class="text-sm leading-relaxed" style="color:${bodyColor};">${escapeHtml(project.description)}</p>`;
            }
            html += '</div>';
        });
        html += '</section>';
    }

    if (sections.certifications && Array.isArray(cvData.certifications) && cvData.certifications.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Certifications');
        cvData.certifications.forEach((item) => {
            html += `<div class="mb-3">
                <h3 class="font-semibold text-sm" style="color:${headingColor};">${escapeHtml(item.name)}</h3>
                <p class="text-sm" style="color:${bodyColor};">${escapeHtml(item.issuer || '')}</p>`;
            const obtained = item.date_obtained ? `Obtained: ${formatCvPreviewDate(item.date_obtained)}` : '';
            const expires = item.expiry_date ? ` | Expires: ${formatCvPreviewDate(item.expiry_date)}` : '';
            if (obtained || expires) {
                html += `<p class="text-xs mt-1" style="color:${mutedColor};">${obtained}${expires}</p>`;
            }
            html += '</div>';
        });
        html += '</section>';
    }

    if (sections.memberships && Array.isArray(cvData.memberships) && cvData.memberships.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Professional Memberships');
        cvData.memberships.forEach((item) => {
            html += `<div class="mb-3">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <h3 class="font-semibold text-sm" style="color:${headingColor};">${escapeHtml(item.organisation)}</h3>`;
            if (item.role) {
                html += `<p class="text-sm" style="color:${bodyColor};">${escapeHtml(item.role)}</p>`;
            }
            html += `</div>
                    <div class="text-sm" style="color:${mutedColor};">${formatCvPreviewDate(item.start_date)}${item.end_date ? ' - ' + formatCvPreviewDate(item.end_date) : ' - Present'}</div>
                </div>
            </div>`;
        });
        html += '</section>';
    }

    if (sections.interests && Array.isArray(cvData.interests) && cvData.interests.length > 0) {
        html += '<section class="mb-6">' + addSectionHeading('Interests & Activities');
        html += '<div class="flex flex-wrap gap-2 text-sm" style="color:' + bodyColor + ';">';
        cvData.interests.forEach((interest) => {
            html += `<span class="px-2 py-1 rounded bg-gray-100">${escapeHtml(interest.name)}</span>`;
        });
        html += '</div></section>';
    }

    if (includeQr) {
        html += `<div class="mt-8 text-right">
            <p class="text-xs" style="color:${mutedColor};">QR code will be included in the PDF.</p>
        </div>`;
    }

    container.innerHTML = html || '<p class="text-gray-500">Select at least one section to preview.</p>';
}
