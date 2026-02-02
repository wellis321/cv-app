/**
 * Job Applications JavaScript Module
 * Extracted from job-applications.php for reuse in content editor
 */

// This file will be created by extracting the JobApplications object from job-applications.php
// For now, we'll load it dynamically from the job-applications page when needed

if (typeof window.JobApplications === 'undefined') {
    // Load the script from job-applications.php
    fetch('/job-applications.php?redirect=false&js_only=true')
        .then(response => response.text())
        .then(html => {
            // Extract and execute the JobApplications script
            const scriptMatch = html.match(/const JobApplications\s*=\s*\{[\s\S]*?\n\s*\};/);
            if (scriptMatch) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[0];
                document.head.appendChild(script);
            }
        })
        .catch(error => {
            console.error('Error loading job applications:', error);
        });
}
