<?php
/**
 * Prompt Best Practices Guide
 * Guide on writing effective CV rewrite prompts
 */

require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'CV Prompt Best Practices | Simple CV Builder';
$metaDescription = 'Learn how to write effective prompts for AI CV rewriting to get better results.';
$canonicalUrl = APP_URL . '/resources/ai/prompt-best-practices.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle,
        'metaDescription' => $metaDescription,
        'canonicalUrl' => $canonicalUrl,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900">CV Prompt Best Practices</h1>
                <p class="mt-2 text-lg text-gray-600">Learn how to write effective instructions for AI CV rewriting</p>
            </div>

            <!-- Introduction -->
            <div class="mb-8 bg-blue-50 border-l-4 border-blue-400 p-6 rounded-r-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">What Are Prompt Instructions?</h2>
                <p class="text-gray-700">
                    When you generate a new CV variant using AI, the system uses a "prompt" - a set of instructions that tell the AI how to rewrite your CV. 
                    You can customise part of these instructions to get results that better match your preferences and style.
                </p>
            </div>

            <!-- Best Practices -->
            <div class="space-y-8">
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Best Practices</h2>
                    
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">1. Be Specific and Clear</h3>
                            <p class="text-gray-700 mb-3">
                                Vague instructions lead to vague results. Be specific about what you want.
                            </p>
                            <div class="bg-gray-50 rounded p-4 space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-red-600 mb-1">❌ Bad:</p>
                                    <p class="text-sm text-gray-600">"Make it better"</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-600 mb-1">✅ Good:</p>
                                    <p class="text-sm text-gray-600">"Emphasize quantifiable achievements and include specific metrics (percentages, dollar amounts, timeframes) wherever possible"</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">2. Emphasize Enhancement, Not Reduction</h3>
                            <p class="text-gray-700 mb-3">
                                The AI should add detail and context, not simplify your content.
                            </p>
                            <div class="bg-gray-50 rounded p-4 space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-red-600 mb-1">❌ Bad:</p>
                                    <p class="text-sm text-gray-600">"Keep descriptions short and concise"</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-600 mb-1">✅ Good:</p>
                                    <p class="text-sm text-gray-600">"Expand descriptions with relevant context, achievements, and impact. Include specific examples of work performed and results achieved"</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">3. Use Action-Oriented Language</h3>
                            <p class="text-gray-700 mb-3">
                                Guide the AI to use strong action verbs and active voice.
                            </p>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-sm text-gray-600">
                                    <strong>Example:</strong> "Use strong action verbs (e.g., 'Led', 'Developed', 'Implemented', 'Achieved') and active voice throughout. Avoid passive constructions."
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">4. Specify Industry Keywords</h3>
                            <p class="text-gray-700 mb-3">
                                If you're targeting a specific industry, mention relevant terminology.
                            </p>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-sm text-gray-600">
                                    <strong>Example:</strong> "Include industry-specific terminology such as 'Agile methodology', 'Scrum', 'CI/CD', and 'DevOps' where relevant to the experience described."
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">5. Request Quantifiable Results</h3>
                            <p class="text-gray-700 mb-3">
                                Ask the AI to include metrics and numbers where appropriate.
                            </p>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-sm text-gray-600">
                                    <strong>Example:</strong> "Wherever possible, include quantifiable results such as percentages, dollar amounts, timeframes, team sizes, or volume metrics. For example: 'Increased sales by 25%' or 'Managed a team of 10 people'."
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">6. Maintain Professional Tone</h3>
                            <p class="text-gray-700 mb-3">
                                Always emphasize maintaining a professional, confident tone.
                            </p>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-sm text-gray-600">
                                    <strong>Example:</strong> "Maintain a confident, professional tone. Use positive language and avoid negative phrasing. Focus on achievements and contributions."
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Common Pitfalls -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Common Pitfalls to Avoid</h2>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">1. Asking for Fictional Content</h3>
                            <p class="text-sm text-gray-700">
                                Never ask the AI to invent experiences, skills, or qualifications you don't have. Always emphasize maintaining factual accuracy.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">2. Being Too Vague</h3>
                            <p class="text-sm text-gray-700">
                                Instructions like "make it better" or "improve it" don't give the AI enough direction. Be specific about what "better" means.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">3. Requesting Simplification</h3>
                            <p class="text-sm text-gray-700">
                                Avoid asking to "simplify" or "reduce" content. Instead, ask to "enhance with relevant detail" or "add context and achievements."
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">4. Ignoring Job Description Alignment</h3>
                            <p class="text-sm text-gray-700">
                                Remember that the AI uses both your instructions and the job description. Your instructions should complement, not contradict, the job requirements.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Examples -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Example Instructions</h2>
                    
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">For Technical Roles</h3>
                            <pre class="bg-gray-50 rounded p-4 text-sm text-gray-700 whitespace-pre-wrap font-mono">Emphasize technical skills and specific technologies used. Include version numbers, frameworks, and tools where relevant. Highlight problem-solving achievements and technical challenges overcome. Use industry-standard terminology.</pre>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">For Management Roles</h3>
                            <pre class="bg-gray-50 rounded p-4 text-sm text-gray-700 whitespace-pre-wrap font-mono">Focus on leadership achievements, team management, and strategic impact. Include team sizes, budget responsibilities, and organizational improvements. Emphasize results achieved through team coordination and strategic planning.</pre>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">For Sales/Marketing Roles</h3>
                            <pre class="bg-gray-50 rounded p-4 text-sm text-gray-700 whitespace-pre-wrap font-mono">Emphasize revenue impact, growth metrics, and campaign results. Include specific numbers: percentage increases, revenue amounts, conversion rates, and campaign reach. Highlight customer acquisition and retention achievements.</pre>
                        </div>
                    </div>
                </section>

                <!-- Model-Specific Tips -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Tips for Different AI Models</h2>
                    
                    <div class="bg-white rounded-lg shadow p-6 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Ollama (Local Models)</h3>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Be more explicit and detailed in instructions</li>
                                <li>Repeat important points for emphasis</li>
                                <li>Use clear, simple language</li>
                                <li>May require more specific examples</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">OpenAI (GPT Models)</h3>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Can handle more nuanced instructions</li>
                                <li>Better at understanding context and tone</li>
                                <li>May benefit from more creative phrasing</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Anthropic (Claude)</h3>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Excellent at following detailed instructions</li>
                                <li>Good at maintaining consistency</li>
                                <li>Responds well to structured, numbered instructions</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Getting Started -->
                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Getting Started</h2>
                    
                    <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-r-lg">
                        <ol class="list-decimal list-inside space-y-2 text-gray-700">
                            <li>Start with the default instructions and generate a test CV variant</li>
                            <li>Review the results and identify what you'd like to change</li>
                            <li>Add specific instructions addressing those areas</li>
                            <li>Test again and refine as needed</li>
                            <li>Save your preferred instructions for future use</li>
                        </ol>
                        <p class="mt-4 text-sm text-gray-600">
                            <a href="/cv-prompt-settings.php" class="text-green-700 hover:text-green-900 underline font-medium">Go to Prompt Settings →</a>
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
</body>
</html>

