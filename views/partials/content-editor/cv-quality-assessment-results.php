<?php
/**
 * CV Quality Assessment Results Display
 * Included by ai-tools-panel.php when an assessment exists
 * $assessment variable must be set by the parent
 */
if (!isset($assessment)) return;
?>
<!-- Assessment Results -->
<div class="space-y-6">
    <!-- Overall Score -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Overall Score</h2>
        <div class="flex items-center justify-center">
            <div class="text-center">
                <div class="score-circle <?php 
                    $score = $assessment['overall_score'];
                    if ($score >= 80) echo 'score-excellent';
                    elseif ($score >= 60) echo 'score-good';
                    elseif ($score >= 40) echo 'score-fair';
                    else echo 'score-poor';
                ?>">
                    <?php echo $score; ?>
                </div>
                <div class="score-label text-gray-600">Out of 100</div>
            </div>
        </div>
    </div>

    <!-- Assessment Explanation (Collapsible) -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <button onclick="toggleExplanation('assessment-explanation')" class="w-full flex items-center justify-between text-left">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                How This Assessment Works
            </h2>
            <svg id="assessment-explanation-icon" class="w-5 h-5 text-blue-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="assessment-explanation" class="hidden mt-4">
            <p class="text-gray-700 mb-4">
                This AI-powered assessment analyzes your CV across multiple dimensions to help you improve its quality and effectiveness. The assessment evaluates your CV's structure, content, formatting, and alignment with job requirements (if a job description was provided).
            </p>
            <div class="mt-4">
                <h3 class="font-semibold text-gray-900 mb-2">Score Ranges:</h3>
                <ul class="space-y-1 text-sm text-gray-700">
                    <li class="flex items-center">
                        <span class="inline-block w-16 text-right mr-3 font-medium text-green-700">80-100:</span>
                        <span>Excellent - Your CV performs very well in this area</span>
                    </li>
                    <li class="flex items-center">
                        <span class="inline-block w-16 text-right mr-3 font-medium text-blue-700">60-79:</span>
                        <span>Good - Solid performance with room for minor improvements</span>
                    </li>
                    <li class="flex items-center">
                        <span class="inline-block w-16 text-right mr-3 font-medium text-yellow-700">40-59:</span>
                        <span>Fair - Needs improvement to be competitive</span>
                    </li>
                    <li class="flex items-center">
                        <span class="inline-block w-16 text-right mr-3 font-medium text-red-700">0-39:</span>
                        <span>Poor - Significant improvements required</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Detailed Scores -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Detailed Scores</h2>
            <button onclick="toggleExplanation('scores-explanation')" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors" title="Learn more about these scores">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">What do these scores mean?</span>
                <svg id="scores-explanation-icon" class="w-4 h-4 ml-1 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
        <?php $hasKeywordScore = $assessment['keyword_match_score'] !== null; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 <?php echo $hasKeywordScore ? 'lg:grid-cols-4' : 'lg:grid-cols-3'; ?> gap-4 mb-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-gray-900 mb-1"><?php echo $assessment['ats_score']; ?></div>
                <div class="text-sm font-medium text-gray-900 mb-2">ATS Compatibility</div>
                <div class="text-xs text-gray-600">Keywords, structure, and content parsing</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-gray-900 mb-1"><?php echo $assessment['content_score']; ?></div>
                <div class="text-sm font-medium text-gray-900 mb-2">Content Quality</div>
                <div class="text-xs text-gray-600">Relevance, impact, and specificity</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-gray-900 mb-1"><?php echo $assessment['formatting_score']; ?></div>
                <div class="text-sm font-medium text-gray-900 mb-2">Content Consistency</div>
                <div class="text-xs text-gray-600">Date formatting and completeness</div>
            </div>
            <?php if ($hasKeywordScore): ?>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 mb-1"><?php echo $assessment['keyword_match_score']; ?></div>
                    <div class="text-sm font-medium text-gray-900 mb-2">Keyword Match</div>
                    <div class="text-xs text-gray-600">Alignment with the job description requirements</div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Score Explanations (Collapsible) -->
        <div id="scores-explanation" class="hidden border-t border-gray-200 pt-4 mt-4">
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> These scores focus on aspects you can control through your content. Visual formatting (fonts, colors, spacing) is handled by your CV template and doesn't affect your scores. Focus on improving your content quality, consistency, and keyword usage.
                </p>
            </div>
            <h3 class="font-semibold text-gray-900 mb-3">What Each Score Means:</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">ATS Compatibility (<?php echo $assessment['ats_score']; ?>/100)</h4>
                    <p class="text-sm text-gray-700 mb-2">
                        Measures how well Applicant Tracking Systems can read and parse your CV content. Focuses on aspects you can control through your content.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1 ml-4 list-disc">
                        <li>Use of relevant keywords throughout your content</li>
                        <li>Clear section structure and headings</li>
                        <li>Complete and consistent information</li>
                        <li>How well your content can be automatically parsed</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-2 italic">Note: Visual formatting is handled by your CV template and doesn't affect this score.</p>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Content Quality (<?php echo $assessment['content_score']; ?>/100)</h4>
                    <p class="text-sm text-gray-700 mb-2">
                        Evaluates the substance and impact of your CV content, including how well you present your achievements and experiences.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1 ml-4 list-disc">
                        <li>Use of quantifiable achievements and metrics</li>
                        <li>Relevance of content to your target role</li>
                        <li>Clarity and specificity of descriptions</li>
                        <li>Demonstration of impact and results</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-1">Content Consistency (<?php echo $assessment['formatting_score']; ?>/100)</h4>
                    <p class="text-sm text-gray-700 mb-2">
                        Assesses consistency and completeness of your CV content that you control.
                    </p>
                    <ul class="text-xs text-gray-600 space-y-1 ml-4 list-disc">
                        <li>Consistent date formatting across all entries</li>
                        <li>Complete information in all sections</li>
                        <li>Consistent description style and detail level</li>
                        <li>No missing dates or unexplained gaps</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-2 italic">Note: Visual formatting (fonts, colors, spacing) is handled by your CV template.</p>
                </div>
                
                <?php if ($assessment['keyword_match_score'] !== null): ?>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-1">Keyword Match (<?php echo $assessment['keyword_match_score']; ?>/100)</h4>
                        <p class="text-sm text-gray-700 mb-2">
                            Measures how well your CV aligns with the specific job description you provided (if applicable).
                        </p>
                        <ul class="text-xs text-gray-600 space-y-1 ml-4 list-disc">
                            <li>Inclusion of relevant keywords from the job description</li>
                            <li>Matching skills and qualifications</li>
                            <li>Alignment with job requirements</li>
                            <li>Relevance of experience to the role</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Strengths -->
    <?php if (!empty($assessment['strengths'])): ?>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Strengths</h2>
            <ul class="space-y-2">
                <?php foreach ($assessment['strengths'] as $strength): ?>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700"><?php echo e($strength); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Weaknesses -->
    <?php if (!empty($assessment['weaknesses'])): ?>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Areas for Improvement</h2>
            <ul class="space-y-2">
                <?php foreach ($assessment['weaknesses'] as $weakness): ?>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700"><?php echo e($weakness); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Enhanced Recommendations -->
    <?php if (!empty($assessment['enhanced_recommendations'])): ?>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Actionable Recommendations</h2>
            <div class="space-y-4">
                <?php foreach ($assessment['enhanced_recommendations'] as $index => $rec): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1"><?php echo e($rec['issue'] ?? 'Improvement needed'); ?></h3>
                                <p class="text-gray-700 text-sm mb-3"><?php echo e($rec['suggestion'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($rec['examples'])): ?>
                            <div class="mb-3">
                                <p class="text-xs font-medium text-gray-600 mb-2">Examples:</p>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-2">
                                    <?php foreach ($rec['examples'] as $example): ?>
                                        <li><?php echo e($example); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php 
                        $aiImprovement = $rec['ai_generated_improvement'] ?? null;
                        if ($aiImprovement) {
                            $placeholderPatterns = [
                                '/\[Improved.*?\]/i',
                                '/\[.*?based on.*?CV.*?\]/i',
                                '/\[.*?text.*?\]/i',
                                '/placeholder/i',
                            ];
                            $isPlaceholder = false;
                            foreach ($placeholderPatterns as $pattern) {
                                if (preg_match($pattern, $aiImprovement)) {
                                    $isPlaceholder = true;
                                    break;
                                }
                            }
                            if (!$isPlaceholder && strlen(trim($aiImprovement)) < 50) {
                                $isPlaceholder = true;
                            }
                            
                            if (!$isPlaceholder):
                        ?>
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs font-medium text-blue-900 mb-2">AI-Generated Improvement:</p>
                                <div class="text-sm text-gray-800 bg-white p-3 rounded border border-blue-100 mb-3">
                                    <?php echo nl2br(e($aiImprovement)); ?>
                                </div>
                                <?php if ($rec['can_apply'] ?? false): ?>
                                    <button onclick="applyImprovement(<?php echo $index; ?>, '<?php echo e($rec['improvement_type'] ?? ''); ?>')" 
                                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Apply This Improvement
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php 
                            endif;
                        } 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif (!empty($assessment['recommendations'])): ?>
        <!-- Fallback to basic recommendations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recommendations</h2>
            <ul class="space-y-3">
                <?php foreach ($assessment['recommendations'] as $recommendation): ?>
                    <li class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700"><?php echo e($recommendation); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Assessment Date -->
    <div class="text-sm text-gray-500 text-center">
        Assessment generated on <?php echo date('F j, Y \a\t g:i A', strtotime($assessment['created_at'])); ?>
    </div>
</div>

<script>
// Store enhanced recommendations for apply functionality
window.enhancedRecommendations = <?php echo json_encode($assessment['enhanced_recommendations'] ?? []); ?>;
window.currentVariantId = '<?php echo e($variantId ?? ''); ?>';
window.currentCsrfToken = '<?php echo e($csrf ?? csrfToken()); ?>';

window.applyImprovement = async function(index, improvementType) {
    const rec = window.enhancedRecommendations[index];
    if (!rec || !rec.ai_generated_improvement) {
        alert('No improvement available to apply.');
        return;
    }
    
    if (!confirm('This will update your CV with the AI-generated improvement. Continue?')) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Applying...';
    
    try {
        const formData = new FormData();
        formData.append('<?php echo CSRF_TOKEN_NAME; ?>', window.currentCsrfToken);
        formData.append('improvement_type', improvementType);
        formData.append('improvement_content', rec.ai_generated_improvement);
        if (window.currentVariantId) {
            formData.append('cv_variant_id', window.currentVariantId);
        }
        
        const response = await fetch('/api/apply-cv-improvement.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Improvement applied successfully! Redirecting to your CV...');
            const variantParam = window.currentVariantId ? '?variant_id=' + encodeURIComponent(window.currentVariantId) : '';
            window.location.href = '/cv.php' + variantParam;
        } else {
            alert('Error: ' + (result.error || 'Failed to apply improvement'));
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        alert('Error: ' + error.message);
        button.disabled = false;
        button.innerHTML = originalText;
    }
};
</script>
