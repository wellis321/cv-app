<?php
/**
 * Ollama Setup Guide
 * Instructions for setting up Ollama with Llama 3 for local AI CV features
 */

require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'Setup Ollama with Llama 3 | Simple CV Builder';
$metaDescription = 'Learn how to set up Ollama with Llama 3 on your computer to use local AI for CV rewriting and quality assessment.';
$canonicalUrl = APP_URL . '/resources/ai/setup-ollama.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle,
        'metaDescription' => $metaDescription,
        'canonicalUrl' => $canonicalUrl,
        'structuredDataType' => 'article',
        'structuredData' => [
            'title' => $pageTitle,
            'description' => $metaDescription,
            'url' => $canonicalUrl,
            'datePublished' => '2025-01-01',
            'dateModified' => date('Y-m-d'),
        ],
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Setup Ollama with Llama 3</h1>
                <p class="text-lg text-gray-600">
                    Use your own local AI model for CV rewriting and quality assessment. Free, private, and runs entirely on your computer.
                </p>
            </div>

            <!-- Two Column Layout: Sidebar + Content -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sticky Sidebar Navigation -->
                <aside class="lg:w-64 flex-shrink-0">
                    <div class="sticky top-24">
                        <nav class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h2 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Contents</h2>
                            <ul class="space-y-1">
                                <li>
                                    <a href="#video-tutorials" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Video Tutorials</a>
                                </li>
                                <li>
                                    <a href="#what-is-local-ai" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">What is Local AI?</a>
                                </li>
                                <li>
                                    <a href="#why-use-local-ai" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Why Use Local AI?</a>
                                </li>
                                <li>
                                    <a href="#step-1-install" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Step 1: Install Ollama</a>
                                </li>
                                <li>
                                    <a href="#model-comparison" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Model Comparison</a>
                                </li>
                                <li>
                                    <a href="#step-2-download" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Step 2: Download Model</a>
                                </li>
                                <li>
                                    <a href="#step-3-verify" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Step 3: Verify Setup</a>
                                </li>
                                <li>
                                    <a href="#step-4-configure" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Step 4: Configure</a>
                                </li>
                                <li>
                                    <a href="#troubleshooting" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Troubleshooting</a>
                                </li>
                                <li>
                                    <a href="#next-steps" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-md transition-colors">Next Steps</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 min-w-0">

                    <!-- Video Tutorials Section -->
                    <div id="video-tutorials" class="mb-8 bg-white rounded-lg shadow p-6 border-2 border-purple-200 scroll-mt-24">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Video Tutorials</h2>
                        <p class="text-gray-700 mb-4">Prefer to watch a video? Here are helpful resources and search terms to find step-by-step video tutorials:</p>
                        <div class="space-y-3">
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                <h3 class="font-medium text-gray-900 mb-1 flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Find Video Tutorials on YouTube
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">Search YouTube for step-by-step video guides. Recommended search terms:</p>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-4 mb-3">
                                    <li>"ollama install tutorial"</li>
                                    <li>"llama 3 setup ollama"</li>
                                    <li>"ollama llama 3.2 installation"</li>
                                    <li>"install ollama [macOS/Windows/Linux]"</li>
                                    <li>"ollama beginner tutorial"</li>
                                </ul>
                                <a href="https://www.youtube.com/results?search_query=ollama+llama+3+install+tutorial" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
                                    Search YouTube Tutorials →
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                <h3 class="font-medium text-gray-900 mb-1 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    Official Ollama Resources
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">The official Ollama website with guides, examples, and documentation.</p>
                                <a href="https://ollama.com" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
                                    Visit ollama.com →
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="mt-4 p-3 rounded border border-blue-200 bg-blue-50 flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm text-gray-700">
                                <strong>Tip:</strong> Many video tutorials cover the same steps as this written guide, but watching someone do it can be helpful if you're a visual learner. Look for recent videos (2024-2025) to ensure they cover the latest Ollama and Llama 3 versions. Popular channels include tutorials from developers, tech educators, and AI enthusiasts.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

                    <!-- What is Local AI Explanation -->
                    <div id="what-is-local-ai" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">What is "Local AI"?</h2>
                <p class="text-gray-700 mb-4">
                    <strong>"Local AI"</strong> means running an AI model directly on your own computer, instead of sending your data to a cloud service. 
                    Think of it like having a personal assistant that works entirely on your device - no internet connection needed after setup, and your information stays private.
                </p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">How it works:</h3>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 text-sm">
                        <li>You install Ollama (a free application) on your computer</li>
                        <li>You download an AI model (like Llama 3) to your computer</li>
                        <li>The AI runs entirely on your device - your CV data never leaves your computer</li>
                        <li>You can use it anytime, even without internet</li>
                    </ol>
                </div>
            </div>

                    <!-- Benefits Box -->
                    <div id="why-use-local-ai" class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-8 scroll-mt-24">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Why Use Local AI Instead of Cloud Services?</h2>
                <p class="text-gray-600 mb-4 text-sm">Here are the key advantages of running AI on your own computer:</p>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Free:</strong> No API costs or subscription fees - use it as much as you want</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Private:</strong> Your CV data never leaves your computer - complete privacy</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Fast:</strong> No internet required after initial setup - works offline</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        <span><strong>Unlimited:</strong> No rate limits or usage restrictions - use it as often as you need</span>
                    </li>
                </ul>
            </div>

                    <!-- Step 1: Install Ollama -->
                    <div id="step-1-install" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                1
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Install Ollama</h2>
                        </div>
                
                <p class="text-gray-700 mb-4">Ollama is a free, open-source tool that runs AI models locally on your computer.</p>
                
                <!-- Tabs -->
                <div class="mb-4 border-b border-gray-200">
                    <nav class="flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('mac')" id="tab-mac" class="tab-button border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600">
                            macOS
                        </button>
                        <button onclick="switchTab('windows')" id="tab-windows" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Windows
                        </button>
                        <button onclick="switchTab('linux')" id="tab-linux" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Linux
                        </button>
                    </nav>
                </div>

                <!-- macOS Tab Content -->
                <div id="content-mac" class="tab-content">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Download and Install Ollama</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700">
                                <li>Visit <a href="https://ollama.com/download" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 underline">ollama.com/download</a> in your web browser</li>
                                <li>Click the "Download for macOS" button</li>
                                <li>Open your Downloads folder and double-click the downloaded file (it will be named something like <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Ollama-darwin.dmg</code>)</li>
                                <li>A window will open showing the Ollama app icon. Drag the Ollama icon to your Applications folder</li>
                                <li>Open your Applications folder (press <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Cmd+Shift+A</code> or click Applications in Finder)</li>
                                <li>Double-click Ollama to open it. You may see a security warning - if so:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Go to System Settings (or System Preferences on older macOS)</li>
                                        <li>Click "Privacy & Security"</li>
                                        <li>Click "Open Anyway" next to the Ollama message</li>
                                        <li>Confirm you want to open Ollama</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Open Terminal (Where You'll Type Commands)</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Press <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Cmd + Space</code> to open Spotlight Search</li>
                                <li>Type "Terminal" and press Enter</li>
                                <li>A black or white window will open - this is where you'll type commands</li>
                                <li>You should see a prompt that looks like: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">yourname@yourmac ~ %</code></li>
                            </ol>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Verify Ollama is Installed</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In the Terminal window, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama --version</code></li>
                                <li>Press Enter</li>
                                <li>If you see a version number (like "ollama version is 0.x.x"), Ollama is installed correctly!</li>
                                <li>If you see "command not found", make sure Ollama is running (check your Applications or the menu bar)</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Windows Tab Content -->
                <div id="content-windows" class="tab-content hidden">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Download and Install Ollama</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700">
                                <li>Visit <a href="https://ollama.com/download" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 underline">ollama.com/download</a> in your web browser</li>
                                <li>Click the "Download for Windows" button</li>
                                <li>Open your Downloads folder and double-click the downloaded file (it will be named something like <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">OllamaSetup.exe</code>)</li>
                                <li>If Windows asks for permission, click "Yes" or "Run"</li>
                                <li>Follow the installation wizard:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Click "Next" on the welcome screen</li>
                                        <li>Accept the license agreement and click "Next"</li>
                                        <li>Choose installation location (default is fine) and click "Install"</li>
                                        <li>Wait for installation to complete</li>
                                        <li>Click "Finish" - Ollama will start automatically</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Open Command Prompt or PowerShell (Where You'll Type Commands)</h3>
                            <div class="space-y-3">
                                <p class="text-gray-700"><strong>Method 1 - Using Search:</strong></p>
                                <ol class="list-decimal list-inside space-y-2 text-gray-700 ml-4">
                                    <li>Click the Start button (Windows icon) or press the Windows key</li>
                                    <li>Type "cmd" or "PowerShell"</li>
                                    <li>Click on "Command Prompt" or "Windows PowerShell" from the results</li>
                                    <li>A black window will open - this is where you'll type commands</li>
                                    <li>You should see a prompt that looks like: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">C:\Users\YourName></code></li>
                                </ol>
                                <p class="text-gray-700 mt-4"><strong>Method 2 - Using Run:</strong></p>
                                <ol class="list-decimal list-inside space-y-2 text-gray-700 ml-4">
                                    <li>Press <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Windows + R</code></li>
                                    <li>Type <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">cmd</code> and press Enter</li>
                                </ol>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Verify Ollama is Installed</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In the Command Prompt or PowerShell window, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama --version</code></li>
                                <li>Press Enter</li>
                                <li>If you see a version number (like "ollama version is 0.x.x"), Ollama is installed correctly!</li>
                                <li>If you see "'ollama' is not recognised", close and reopen the command prompt, or restart your computer</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Linux Tab Content -->
                <div id="content-linux" class="tab-content hidden">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Install Ollama</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700">
                                <li>Open your Terminal application (usually found in Applications → Accessories or press <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Ctrl+Alt+T</code>)</li>
                                <li>Copy and paste this command into the terminal:
                                    <pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto mt-2"><code>curl -fsSL https://ollama.com/install.sh | sh</code></pre>
                                </li>
                                <li>Press Enter</li>
                                <li>You may be asked for your password (this is your user account password). Type it and press Enter (you won't see the password as you type - this is normal for security)</li>
                                <li>Wait for the installation to complete</li>
                                <li>Or visit <a href="https://ollama.com/download" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 underline">ollama.com/download</a> for distribution-specific instructions</li>
                            </ol>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Verify Ollama is Installed</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In the Terminal, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama --version</code></li>
                                <li>Press Enter</li>
                                <li>If you see a version number, Ollama is installed correctly!</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

                    <!-- Model Comparison Section -->
                    <div id="model-comparison" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Which Model Should You Use?</h2>
                <p class="text-gray-700 mb-6">Different models offer different balances of quality, speed, and resource requirements. Choose the one that best fits your computer and needs.</p>
                
                <div class="bg-gray-100 rounded-xl p-6">
                    <div class="space-y-4">
                        <!-- Llama 3.2 (3B) -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200 p-5 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Llama 3.2 (3B)</h3>
                                <p class="text-sm text-gray-600">Best for: Most users, balanced performance</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Recommended</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Pros
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Fast responses (10-30 seconds)</li>
                                    <li>Low memory usage (~4GB RAM)</li>
                                    <li>Small download (~2GB)</li>
                                    <li>Good quality for CV tasks</li>
                                    <li>Works on most computers</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Cons
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Less nuanced than larger models</li>
                                    <li>May miss subtle context</li>
                                    <li>Shorter responses</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <p class="text-gray-700"><strong>System Requirements:</strong> 8GB+ RAM, ~4GB disk space</p>
                            <p class="text-gray-700 mt-1"><strong>Command:</strong> <code class="bg-white px-2 py-1 rounded">ollama pull llama3.2</code></p>
                        </div>
                    </div>

                        <!-- Llama 3.1 (8B) -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200 p-5 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Llama 3.1 (8B)</h3>
                                <p class="text-sm text-gray-600">Best for: Better quality, more powerful computers</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Pros
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Higher quality responses</li>
                                    <li>Better understanding of context</li>
                                    <li>More nuanced suggestions</li>
                                    <li>Better for complex CVs</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Cons
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Slower responses (30-60 seconds)</li>
                                    <li>High memory usage (~16GB RAM)</li>
                                    <li>Large download (~9GB)</li>
                                    <li>May struggle on older computers</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <p class="text-gray-700"><strong>System Requirements:</strong> 16GB+ RAM, ~9GB disk space</p>
                            <p class="text-gray-700 mt-1"><strong>Command:</strong> <code class="bg-white px-2 py-1 rounded">ollama pull llama3.1:8b</code></p>
                        </div>
                    </div>

                        <!-- Llama 3.3 -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200 p-5 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Llama 3.3</h3>
                                <p class="text-sm text-gray-600">Best for: Latest features and improvements</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Latest</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Pros
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Latest improvements and fixes</li>
                                    <li>Better performance than 3.2</li>
                                    <li>Multiple size variants available</li>
                                    <li>Active development</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Cons
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Size varies by variant (2-10GB)</li>
                                    <li>Less tested than 3.2</li>
                                    <li>May have occasional bugs</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <p class="text-gray-700"><strong>System Requirements:</strong> Varies by variant (8-16GB+ RAM)</p>
                            <p class="text-gray-700 mt-1"><strong>Command:</strong> <code class="bg-white px-2 py-1 rounded">ollama pull llama3.3</code></p>
                        </div>
                    </div>

                        <!-- Mistral 7B -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200 p-5 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Mistral 7B</h3>
                                <p class="text-sm text-gray-600">Best for: Alternative high-quality option</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Pros
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Excellent quality</li>
                                    <li>Good balance of speed/quality</li>
                                    <li>Well-optimized</li>
                                    <li>Strong performance</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Cons
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Requires 12GB+ RAM</li>
                                    <li>Larger download (~4GB)</li>
                                    <li>Slower than 3.2</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <p class="text-gray-700"><strong>System Requirements:</strong> 12GB+ RAM, ~4GB disk space</p>
                            <p class="text-gray-700 mt-1"><strong>Command:</strong> <code class="bg-white px-2 py-1 rounded">ollama pull mistral</code></p>
                        </div>
                    </div>

                        <!-- Phi-3 -->
                        <div class="overflow-hidden rounded-lg bg-white shadow-lg border border-gray-200 p-5 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Phi-3 (Microsoft)</h3>
                                <p class="text-sm text-gray-600">Best for: Very fast, low-resource computers</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Pros
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Very fast responses (5-15 seconds)</li>
                                    <li>Very low memory (~2GB RAM)</li>
                                    <li>Tiny download (~2GB)</li>
                                    <li>Works on older computers</li>
                                    <li>Good for basic tasks</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Cons
                                </h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-5">
                                    <li>Lower quality than larger models</li>
                                    <li>Less nuanced responses</li>
                                    <li>May struggle with complex CVs</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <p class="text-gray-700"><strong>System Requirements:</strong> 4GB+ RAM, ~2GB disk space</p>
                            <p class="text-gray-700 mt-1"><strong>Command:</strong> <code class="bg-white px-2 py-1 rounded">ollama pull phi3</code></p>
                        </div>
                    </div>

                    <!-- Recommendation Box -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Recommendation
                        </h3>
                        <p class="text-sm text-gray-700">
                            <strong>Start with Llama 3.2</strong> - it offers the best balance of quality, speed, and resource usage for CV tasks. 
                            If you have a powerful computer (16GB+ RAM) and want higher quality, try <strong>Llama 3.1 (8B)</strong>. 
                            If you have limited resources, <strong>Phi-3</strong> is a good lightweight option.
                        </p>
                    </div>
                    </div>
                </div>
            </div>

                    <!-- Step 2: Download Your Chosen Model -->
                    <div id="step-2-download" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                2
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Download Your Chosen Model</h2>
                        </div>
                
                <p class="text-gray-700 mb-4">Once you've chosen a model from the comparison above, download it using the commands below.</p>
                
                <!-- Tabs for Step 2 -->
                <div class="mb-4 border-b border-gray-200">
                    <nav class="flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab2('mac')" id="tab2-mac" class="tab-button-2 border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600">
                            macOS
                        </button>
                        <button onclick="switchTab2('windows')" id="tab2-windows" class="tab-button-2 border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Windows
                        </button>
                        <button onclick="switchTab2('linux')" id="tab2-linux" class="tab-button-2 border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Linux
                        </button>
                    </nav>
                </div>

                <!-- macOS Step 2 Content -->
                <div id="content2-mac" class="tab-content-2">
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">Check Available Disk Space First</h3>
                        <p class="text-gray-700 text-sm mb-2">Before downloading, make sure you have enough free disk space:</p>
                        <ol class="list-decimal list-inside space-y-1 text-gray-700 text-sm ml-4">
                            <li>Click the Apple menu (top left) → "About This Mac"</li>
                            <li>Click "Storage" tab</li>
                            <li>Check your available space - you'll need at least <strong>5-10GB free</strong> depending on the model</li>
                        </ol>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Recommended: Llama 3.2 (3B parameters)</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Best balance of quality and speed. Works well on most computers with 8GB+ RAM.</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~2GB download, ~4GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Open Terminal (press <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Cmd + Space</code>, type "Terminal", press Enter)</li>
                                <li>In the Terminal window, type exactly: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.2</code></li>
                                <li>Press Enter</li>
                                <li>You'll see download progress. This will take several minutes depending on your internet speed</li>
                                <li>When you see "pulling complete", the model is ready!</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.2</p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Alternative: Llama 3.1 (8B parameters)</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Higher quality results, but requires more RAM (16GB+ recommended).</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~4.7GB download, ~9GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In Terminal, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.1:8b</code></li>
                                <li>Press Enter and wait for download to complete</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.1:8b</p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Latest: Llama 3.3</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Most recent version with latest improvements.</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~2-5GB download (varies by variant), ~4-10GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In Terminal, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.3</code></li>
                                <li>Press Enter and wait for download to complete</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Windows Step 2 Content -->
                <div id="content2-windows" class="tab-content-2 hidden">
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">Check Available Disk Space First</h3>
                        <p class="text-gray-700 text-sm mb-2">Before downloading, make sure you have enough free disk space:</p>
                        <ol class="list-decimal list-inside space-y-1 text-gray-700 text-sm ml-4">
                            <li>Open File Explorer (press <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">Windows + E</code>)</li>
                            <li>Right-click on "This PC" or "My Computer" in the left sidebar</li>
                            <li>Click "Properties"</li>
                            <li>Check your available space - you'll need at least <strong>5-10GB free</strong> depending on the model</li>
                        </ol>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Recommended: Llama 3.2 (3B parameters)</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Best balance of quality and speed. Works well on most computers with 8GB+ RAM.</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~2GB download, ~4GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Open Command Prompt or PowerShell (click Start, type "cmd" or "PowerShell", press Enter)</li>
                                <li>In the Command Prompt window, type exactly: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.2</code></li>
                                <li>Press Enter</li>
                                <li>You'll see download progress. This will take several minutes depending on your internet speed</li>
                                <li>When you see "pulling complete", the model is ready!</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.2</p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Alternative: Llama 3.1 (8B parameters)</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Higher quality results, but requires more RAM (16GB+ recommended).</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~4.7GB download, ~9GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In Command Prompt, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.1:8b</code></li>
                                <li>Press Enter and wait for download to complete</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.1:8b</p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Latest: Llama 3.3</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Most recent version with latest improvements.</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~2-5GB download (varies by variant), ~4-10GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In Command Prompt, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.3</code></li>
                                <li>Press Enter and wait for download to complete</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linux Step 2 Content -->
                <div id="content2-linux" class="tab-content-2 hidden">
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">Check Available Disk Space First</h3>
                        <p class="text-gray-700 text-sm mb-2">Before downloading, make sure you have enough free disk space:</p>
                        <ol class="list-decimal list-inside space-y-1 text-gray-700 text-sm ml-4">
                            <li>Open Terminal (press <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">Ctrl+Alt+T</code>)</li>
                            <li>Type: <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">df -h</code> and press Enter</li>
                            <li>Look at the "Avail" column - you'll need at least <strong>5-10GB free</strong> depending on the model</li>
                            <li>Or use your system's disk usage tool (varies by distribution)</li>
                        </ol>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Recommended: Llama 3.2 (3B parameters)</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Best balance of quality and speed. Works well on most computers with 8GB+ RAM.</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~2GB download, ~4GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Open Terminal (press <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">Ctrl+Alt+T</code> or find it in Applications)</li>
                                <li>In the Terminal, type exactly: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.2</code></li>
                                <li>Press Enter</li>
                                <li>You'll see download progress. This will take several minutes depending on your internet speed</li>
                                <li>When you see "pulling complete", the model is ready!</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.2</p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Alternative: Llama 3.1 (8B parameters)</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Higher quality results, but requires more RAM (16GB+ recommended).</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~4.7GB download, ~9GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In Terminal, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.1:8b</code></li>
                                <li>Press Enter and wait for download to complete</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.1:8b</p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Latest: Llama 3.3</h3>
                            <div class="mb-3 space-y-2">
                                <p class="text-gray-700">Most recent version with latest improvements.</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-sm text-gray-700"><strong>Disk Space Required:</strong> ~2-5GB download (varies by variant), ~4-10GB total after installation</p>
                                </div>
                            </div>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>In Terminal, type: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama pull llama3.3</code></li>
                                <li>Press Enter and wait for download to complete</li>
                            </ol>
                            <div class="bg-gray-100 p-3 rounded mt-3">
                                <p class="text-sm font-mono text-gray-800">ollama pull llama3.3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-gray-700 mb-2">
                        <strong>Important Notes:</strong>
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-4">
                        <li>The first download will take several minutes depending on your internet speed</li>
                        <li>Model files are large (2-10GB depending on the model) - make sure you have enough disk space before starting</li>
                        <li>Subsequent uses will be instant as the model is stored locally on your computer</li>
                        <li>If you run out of space during download, you can delete the partial download and try again later</li>
                    </ul>
                </div>
            </div>

                    <!-- Step 3: Verify Your Setup is Working -->
                    <div id="step-3-verify" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                3
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Verify Your Setup is Working</h2>
                        </div>
                
                <p class="text-gray-700 mb-4">Make sure Ollama is running and your model is ready to use.</p>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">1. Make Sure Ollama is Running</h3>
                        <p class="text-gray-700 mb-3">Ollama needs to be running on your computer for the AI features to work.</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li><strong>macOS/Windows:</strong> Check if the Ollama app is open. If not, open it from your Applications folder</li>
                            <li><strong>Linux:</strong> Ollama should start automatically, but if not, run: <code class="bg-gray-100 px-1 py-0.5 rounded text-sm">ollama serve</code></li>
                        </ul>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">2. Verify Your Model is Installed</h3>
                        <p class="text-gray-700 mb-3">Check that your downloaded model is available:</p>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600"><strong>macOS/Linux:</strong> Open Terminal and run:</p>
                            <div class="bg-gray-100 p-3 rounded">
                                <p class="text-sm font-mono text-gray-800">ollama list</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-2"><strong>Windows:</strong> Open Command Prompt and run:</p>
                            <div class="bg-gray-100 p-3 rounded">
                                <p class="text-sm font-mono text-gray-800">ollama list</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">You should see your downloaded model (e.g., <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">llama3.2</code>) in the list.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">3. Test Ollama API Connection</h3>
                        <p class="text-gray-700 mb-3">Verify that Ollama's API is accessible:</p>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600"><strong>macOS/Linux:</strong> In Terminal, run:</p>
                            <div class="bg-gray-100 p-3 rounded">
                                <p class="text-sm font-mono text-gray-800">curl http://localhost:11434/api/tags</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-2"><strong>Windows:</strong> In Command Prompt, run:</p>
                            <div class="bg-gray-100 p-3 rounded">
                                <p class="text-sm font-mono text-gray-800">curl http://localhost:11434/api/tags</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">This should return a JSON list of your installed models. If you see an error, make sure Ollama is running.</p>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-400 bg-green-50 p-4 rounded">
                        <h3 class="font-semibold text-gray-900 mb-2">Configure Your Connection</h3>
                        <p class="text-sm text-gray-700 mb-3">Once Ollama is installed and your model is downloaded, you need to configure Simple CV Builder to use your local Ollama installation.</p>
                        <?php if (isLoggedIn()): ?>
                            <a href="/ai-settings.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 mt-2">
                                Go to AI Settings →
                            </a>
                        <?php else: ?>
                            <p class="text-sm text-gray-600">You'll need to be logged in to configure your AI settings. After logging in, go to the AI Settings page to enter your Ollama connection details.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

                    <!-- Step 4: Configure Connection in Simple CV Builder -->
                    <div id="step-4-configure" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                4
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Configure Connection in Simple CV Builder</h2>
                        </div>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Enter Your Ollama Settings</h3>
                        <p class="text-gray-700 mb-4">Now that Ollama is installed and your model is downloaded, configure Simple CV Builder to connect to it:</p>
                        
                        <?php if (isLoggedIn()): ?>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                                <li>Go to the <a href="/ai-settings.php" class="text-blue-600 hover:text-blue-800 underline">AI Settings page</a></li>
                                <li>Select "Local Ollama" as your AI Service</li>
                                <li>Enter your Ollama Base URL (usually <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">http://localhost:11434</code>)</li>
                                <li>Enter your model name (e.g., <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">llama3.2</code>)</li>
                                <li>Click "Test Connection" to verify it works</li>
                                <li>Click "Save Settings"</li>
                            </ol>
                            <a href="/ai-settings.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Go to AI Settings →
                            </a>
                        <?php else: ?>
                            <p class="text-gray-700 mb-4">You'll need to be logged in to configure your AI settings. After logging in:</p>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                                <li>Go to the AI Settings page</li>
                                <li>Select "Local Ollama" as your AI Service</li>
                                <li>Enter your Ollama Base URL (usually <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">http://localhost:11434</code>)</li>
                                <li>Enter your model name (e.g., <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">llama3.2</code>)</li>
                                <li>Click "Test Connection" to verify it works</li>
                                <li>Click "Save Settings"</li>
                            </ol>
                        <?php endif; ?>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Try the AI Features</h3>
                        <p class="text-gray-700 mb-4">Once configured, you can test the AI features in Simple CV Builder:</p>
                        
                        <div class="space-y-3">
                            <div class="bg-gray-50 p-3 rounded">
                                <h4 class="font-medium text-gray-900 mb-2">CV Quality Assessment</h4>
                                <p class="text-sm text-gray-700 mb-2">Get AI-powered feedback on your CV quality with scores and recommendations.</p>
                                <?php if (isLoggedIn()): ?>
                                    <a href="/cv-quality.php" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                        Test CV Assessment →
                                    </a>
                                <?php else: ?>
                                    <p class="text-xs text-gray-500">You'll need to be logged in to use this feature.</p>
                                <?php endif; ?>
                            </div>

                            <div class="bg-gray-50 p-3 rounded">
                                <h4 class="font-medium text-gray-900 mb-2">AI CV Rewriting</h4>
                                <p class="text-sm text-gray-700 mb-2">Generate job-specific CV variants automatically from job descriptions.</p>
                                <?php if (isLoggedIn()): ?>
                                    <a href="/cv-variants/rewrite.php" class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">
                                        Generate AI CV →
                                    </a>
                                <?php else: ?>
                                    <p class="text-xs text-gray-500">You'll need to be logged in to use this feature.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="border-l-4 border-green-400 bg-green-50 p-4 rounded">
                        <h3 class="font-semibold text-gray-900 mb-2">What to Expect</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 ml-4">
                            <li>The first request may take 30-60 seconds as the model loads into memory</li>
                            <li>Subsequent requests will be faster (10-30 seconds)</li>
                            <li>All processing happens on your computer - your data never leaves your machine</li>
                            <li>If you see an error, check that Ollama is running and the model is installed</li>
                        </ul>
                    </div>
                </div>
            </div>

                    <!-- Troubleshooting -->
                    <div id="troubleshooting" class="bg-white rounded-lg shadow p-6 mb-6 scroll-mt-24">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Troubleshooting</h2>
                
                <div class="space-y-4">
                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Ollama not found</h3>
                        <p class="text-gray-700 text-sm">Make sure Ollama is running. On macOS/Windows, check if the Ollama app is open. On Linux, start it with: <code class="bg-yellow-100 px-1 py-0.5 rounded text-xs">ollama serve</code></p>
                    </div>

                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Connection refused</h3>
                        <p class="text-gray-700 text-sm">Verify Ollama is running on port 11434. Check with: <code class="bg-yellow-100 px-1 py-0.5 rounded text-xs">curl http://localhost:11434/api/tags</code></p>
                    </div>

                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Model not found</h3>
                        <p class="text-gray-700 text-sm">Make sure you've downloaded the model. Run <code class="bg-yellow-100 px-1 py-0.5 rounded text-xs">ollama pull llama3.2</code> (or your chosen model).</p>
                    </div>

                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Slow performance</h3>
                        <p class="text-gray-700 text-sm">Try a smaller model like <code class="bg-yellow-100 px-1 py-0.5 rounded text-xs">llama3.2</code> instead of larger ones. Also ensure you have enough RAM (8GB+ recommended).</p>
                    </div>

                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Out of memory errors</h3>
                        <p class="text-gray-700 text-sm">Close other applications to free up RAM, or use a smaller model. Llama 3.2 (3B) requires less memory than Llama 3.1 (8B).</p>
                    </div>
                </div>
                    </div>

                    <!-- Next Steps -->
                    <div id="next-steps" class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 scroll-mt-24">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">You're All Set!</h2>
                        <p class="text-gray-700 mb-4">Now you can use AI-powered CV features completely free and privately on your own computer.</p>
                        <div class="flex flex-wrap gap-3">
                            <?php if (isLoggedIn()): ?>
                                <a href="/cv-variants/rewrite.php" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    Generate AI CV →
                                </a>
                                <a href="/cv-quality.php" class="inline-flex items-center px-4 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50">
                                    Assess CV Quality →
                                </a>
                            <?php else: ?>
                                <a href="/register.php" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    Get Started →
                                </a>
                            <?php endif; ?>
                            <a href="/content-editor.php#cv-variants" class="inline-flex items-center px-4 py-2 text-purple-600 hover:text-purple-700">
                                Learn More About AI Features →
                            </a>
                        </div>
                        <p class="mt-4 pt-4 border-t border-purple-200 text-sm text-gray-600">
                            <a href="/resources/ai/prompt-best-practices.php" class="text-purple-600 hover:text-purple-700 font-medium">CV Prompt Best Practices</a> — learn how to write effective prompts for AI CV rewriting.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>
    <?php partial('auth-modals'); ?>

    <style>
        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }
        
        /* Active sidebar link highlighting */
        nav a.active {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 500;
        }
    </style>
    <script>
        // Smooth scrolling for sidebar navigation links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('aside nav a[href^="#"]');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        // Calculate offset for header (96px = top-24)
                        const headerOffset = 96;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });

        // Highlight active sidebar link on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('[id^="video-tutorials"], [id^="what-is-local-ai"], [id^="why-use-local-ai"], [id^="step-"], [id^="model-comparison"], [id^="troubleshooting"], [id^="next-steps"]');
            const navLinks = document.querySelectorAll('aside nav a');
            
            function updateActiveLink() {
                let current = '';
                const scrollPosition = window.scrollY + 150; // Offset for header
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });
                
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active');
                    }
                });
            }
            
            window.addEventListener('scroll', updateActiveLink);
            updateActiveLink(); // Initial call
        });

        // Tab switching for Step 1
        function switchTab(os) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-600', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected content
            document.getElementById('content-' + os).classList.remove('hidden');
            
            // Activate selected tab
            const tab = document.getElementById('tab-' + os);
            tab.classList.remove('border-transparent', 'text-gray-500');
            tab.classList.add('border-blue-600', 'text-blue-600');
        }

        // Tab switching for Step 2
        function switchTab2(os) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content-2').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.tab-button-2').forEach(button => {
                button.classList.remove('border-blue-600', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected content
            document.getElementById('content2-' + os).classList.remove('hidden');
            
            // Activate selected tab
            const tab = document.getElementById('tab2-' + os);
            tab.classList.remove('border-transparent', 'text-gray-500');
            tab.classList.add('border-blue-600', 'text-blue-600');
        }
    </script>
</body>
</html>

