/**
 * Browser AI Service
 * Handles AI execution in the browser using WebLLM and TensorFlow.js
 */


const BrowserAIService = {
    webllmEngine: null,
    webllmModule: null, // Store the imported WebLLM module
    tensorflowModel: null,
    currentModel: null,
    currentModelType: null,

    /**
     * Get user-friendly error message and whether to offer server AI fallback
     * @param {Error} error
     * @returns {{ message: string, offerServerFallback: boolean }}
     */
    getWebLLMErrorHelp(error) {
        const msg = (error && error.message) ? String(error.message) : '';
        const isCacheError = msg.includes('Cache.add()') || msg.includes('network error') || msg.includes('Cache') && msg.includes('network');
        const isWebLLMInit = msg.includes('WebLLM') || msg.includes('Failed to load') || msg.includes('CreateMLCEngine');
        if (isCacheError || isWebLLMInit) {
            const tips = isCacheError
                ? 'This often happens in production when:\n• Browser storage is restricted ("Clear cookies when you close" reduces quota)\n• A firewall or ad-blocker blocks model downloads (~2GB)\n• Network/CORS limits on your host\n\nTry: different browser, disable that setting, use incognito (with setting off), or use Ollama/server AI instead.'
                : 'Browser AI could not load. Try a different browser (Chrome/Edge recommended) or use Ollama/server-side AI.';
            return {
                message: 'Browser AI failed: ' + (isCacheError ? 'Cache storage error while downloading the model. ' : '') + tips,
                offerServerFallback: true
            };
        }
        return { message: msg || 'Unknown error', offerServerFallback: false };
    },
    
    /**
     * Initialize browser AI with selected model
     * @param {string} modelType 'webllm' or 'tensorflow'
     * @param {string} modelName Model name (e.g., 'llama3.2', 'mistral')
     * @param {Function} progressCallback Callback for progress updates
     * @returns {Promise<void>}
     */
    async initBrowserAI(modelType, modelName, progressCallback = null) {
        try {
            this.currentModelType = modelType;
            this.currentModel = modelName;
            
            if (modelType === 'webllm') {
                return await this.initWebLLM(modelName, progressCallback);
            } else if (modelType === 'tensorflow') {
                return await this.initTensorFlow(modelName, progressCallback);
            } else {
                throw new Error(`Unknown model type: ${modelType}`);
            }
        } catch (error) {
            console.error('Error initializing browser AI:', error);
            throw error;
        }
    },
    
    /**
     * Map user-friendly model names to WebLLM model IDs
     * @param {string} modelName User-friendly model name (e.g., 'llama3.2', 'llama3')
     * @returns {string} WebLLM model ID
     */
    mapModelNameToWebLLMID(modelName) {
        const modelMap = {
            'llama3.2': 'Llama-3.1-8B-Instruct-q4f32_1-MLC',
            'llama3': 'Llama-3.1-8B-Instruct-q4f32_1-MLC',
            'llama3.1': 'Llama-3.1-8B-Instruct-q4f32_1-MLC',
            'llama-3.2': 'Llama-3.1-8B-Instruct-q4f32_1-MLC',
            'llama-3.1': 'Llama-3.1-8B-Instruct-q4f32_1-MLC',
            'llama-3': 'Llama-3.1-8B-Instruct-q4f32_1-MLC',
            'mistral': 'Mistral-7B-Instruct-v0.3-q4f32_1-MLC',
            'phi3': 'Phi-3-mini-4k-instruct-q4f32_1-MLC',
            'gemma': 'Gemma-2-2b-it-q4f32_1-MLC',
        };
        
        // If it's already a WebLLM model ID (contains dashes and MLC), use as-is
        if (modelName.includes('-') && modelName.includes('MLC')) {
            return modelName;
        }
        
        // Map to WebLLM model ID, default to Llama 3.1 if not found
        return modelMap[modelName.toLowerCase()] || 'Llama-3.1-8B-Instruct-q4f32_1-MLC';
    },
    
    /**
     * Initialize WebLLM model
     * @param {string} modelName Model name (user-friendly or WebLLM ID)
     * @param {Function} progressCallback Progress callback
     * @returns {Promise<void>}
     */
    async initWebLLM(modelName, progressCallback = null) {
        try {
            // Check if WebLLM module is loaded
            if (!this.webllmModule) {
                // Load WebLLM dynamically using import()
                await this.loadWebLLMLibrary();
            }
            
            // Get webllm from the imported module
            let webllm = this.webllmModule;
            if (!webllm) {
                throw new Error('WebLLM module loaded but exports not available');
            }
            
            // Handle default export if present
            if (webllm.default && !webllm.CreateMLCEngine) {
                webllm = webllm.default;
                this.webllmModule = webllm;
            }
            
            // Check if the module has the expected exports
            if (!webllm.CreateMLCEngine) {
                console.warn('WebLLM module structure:', Object.keys(webllm));
                throw new Error('WebLLM module loaded but CreateMLCEngine not found. Available exports: ' + Object.keys(webllm).join(', '));
            }
            
            // Map user-friendly model name to WebLLM model ID
            const webllmModelId = this.mapModelNameToWebLLMID(modelName);
            
            // Check for cached model (use original name for cache lookup)
            const cachedModel = await ModelCacheManager.getModel(modelName, 'webllm');
            
            if (progressCallback) {
                progressCallback({ stage: 'checking_cache', message: 'Checking for cached model...' });
            }
            
            // WebLLM uses CreateMLCEngine with model name and config
            const engineConfig = {
                initProgressCallback: (progress) => {
                    if (progressCallback) {
                        progressCallback({
                            stage: 'downloading',
                            progress: progress.progress || 0,
                            message: progress.text || `Downloading model: ${Math.round((progress.progress || 0) * 100)}%`
                        });
                    }
                }
            };
            
            // Use CreateMLCEngine (CreateWebWorkerMLCEngine has different API signature)
            const CreateMLCEngine = webllm.CreateMLCEngine || webllm.default?.CreateMLCEngine || (webllm.default && webllm.default.CreateMLCEngine) || webllm.CreateWebLLMEngine;
            
            if (!CreateMLCEngine) {
                throw new Error('CreateMLCEngine not found in WebLLM module. Available exports: ' + Object.keys(webllm).join(', '));
            }
            
            // Create engine with WebLLM model ID and config
            this.webllmEngine = await CreateMLCEngine(webllmModelId, engineConfig);
            
            // Cache the model for future use (if getModelCache method exists)
            if (!cachedModel && this.webllmEngine && typeof this.webllmEngine.getModelCache === 'function') {
                if (progressCallback) {
                    progressCallback({ stage: 'caching', message: 'Caching model for future use...' });
                }
                try {
                    await ModelCacheManager.saveModel({
                        modelName: modelName,
                        modelType: 'webllm',
                        data: this.webllmEngine.getModelCache(),
                        size: this.estimateModelSize(modelName),
                    });
                } catch (cacheError) {
                    // Cache saving is optional, log but don't fail
                    console.warn('Failed to cache model:', cacheError);
                }
            }
            
            return Promise.resolve();
        } catch (error) {
            console.error('Error initializing WebLLM:', error);
            throw new Error(`Failed to initialize WebLLM model ${modelName}: ${error.message}`);
        }
    },
    
    /**
     * Initialize TensorFlow.js model
     * @param {string} modelName Model name
     * @param {Function} progressCallback Progress callback
     * @returns {Promise<void>}
     */
    async initTensorFlow(modelName, progressCallback = null) {
        try {
            // Load TensorFlow.js if not already loaded
            if (typeof tf === 'undefined') {
                await this.loadTensorFlowLibrary();
            }
            
            // Check for cached model
            const cachedModel = await ModelCacheManager.getModel(modelName, 'tensorflow');
            
            if (progressCallback) {
                progressCallback({ stage: 'checking_cache', message: 'Checking for cached model...' });
            }
            
            // Load model (simplified - would need actual model URL)
            // This is a placeholder - actual implementation would load from URL or cache
            if (cachedModel && cachedModel.data) {
                this.tensorflowModel = await tf.loadLayersModel(cachedModel.data);
            } else {
                // Load from URL (would need actual model URL)
                const modelUrl = this.getTensorFlowModelUrl(modelName);
                if (progressCallback) {
                    progressCallback({ stage: 'downloading', message: 'Downloading TensorFlow.js model...' });
                }
                this.tensorflowModel = await tf.loadLayersModel(modelUrl);
                
                // Cache the model
                await ModelCacheManager.saveModel({
                    modelName: modelName,
                    modelType: 'tensorflow',
                    data: modelUrl, // Store URL or model data
                    size: this.estimateModelSize(modelName, 'tensorflow'),
                });
            }
            
            return Promise.resolve();
        } catch (error) {
            console.error('Error initializing TensorFlow.js:', error);
            throw new Error(`Failed to initialize TensorFlow.js model ${modelName}: ${error.message}`);
        }
    },
    
    /**
     * Load WebLLM library dynamically
     * @returns {Promise<void>}
     */
    async loadWebLLMLibrary() {
        // WebLLM uses ES modules, so we need to use dynamic import() instead of script tag
        // Try different import paths as WebLLM package structure may vary
        const importPaths = [
            'https://unpkg.com/@mlc-ai/web-llm@0.2.80/lib/index.js',
            'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm@0.2.80/lib/index.js',
            'https://unpkg.com/@mlc-ai/web-llm@latest/lib/index.js',
            'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm@latest/lib/index.js'
        ];
        
        let lastError = null;
        for (const importPath of importPaths) {
            try {
                console.log(`Attempting to load WebLLM from ${importPath}...`);
                const webllmModule = await import(importPath);
                // Store the module for later use - handle both default and named exports
                // WebLLM exports CreateMLCEngine directly, not as default
                if (webllmModule.CreateMLCEngine) {
                    this.webllmModule = webllmModule;
                } else if (webllmModule.default?.CreateMLCEngine) {
                    this.webllmModule = webllmModule.default;
                } else {
                    this.webllmModule = webllmModule.default || webllmModule;
                }
                console.log('WebLLM loaded successfully from', importPath);
                console.log('WebLLM module keys:', Object.keys(this.webllmModule));
                if (this.webllmModule.CreateMLCEngine) {
                    console.log('CreateMLCEngine found in module');
                } else {
                    console.warn('CreateMLCEngine not found in module');
                }
                return;
            } catch (error) {
                console.error(`Failed to load WebLLM from ${importPath}:`, error);
                lastError = error;
                continue;
            }
        }
        
        // If all paths failed, throw error with details
        throw new Error(`Failed to load WebLLM library from all CDN sources. Last error: ${lastError?.message || 'Unknown error'}. Browser AI requires an internet connection. Please check your network connection and try again.`);
    },
    
    /**
     * Try loading WebLLM from jsdelivr CDN as fallback
     * @param {Function} resolve
     * @param {Function} reject
     */
    tryJsdelivrCDN(resolve, reject) {
        const altScript = document.createElement('script');
        altScript.type = 'module'; // WebLLM uses ES modules
        // Try jsdelivr with correct path
        altScript.src = 'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm@0.2.80/lib/index.js';
        altScript.onload = () => {
            // Check for both webllm and WebLLM (different naming conventions)
            if (typeof webllm !== 'undefined' || typeof WebLLM !== 'undefined' || window.webllm || window.WebLLM) {
                resolve();
            } else {
                reject(new Error('WebLLM library loaded but webllm object not available. Browser AI is not supported in this environment. Please check browser console for details.'));
            }
        };
        altScript.onerror = () => {
            reject(new Error('Failed to load WebLLM library from both CDN sources. Browser AI requires an internet connection. Please check your network connection and try again.'));
        };
        document.head.appendChild(altScript);
    },
    
    /**
     * Load TensorFlow.js library dynamically
     * @returns {Promise<void>}
     */
    async loadTensorFlowLibrary() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load TensorFlow.js library'));
            document.head.appendChild(script);
        });
    },
    
    /**
     * Generate text using browser AI
     * @param {string} prompt Input prompt
     * @param {Object} options Generation options
     * @returns {Promise<string>} Generated text
     */
    async generateText(prompt, options = {}) {
        try {
            if (!this.currentModel) {
                throw new Error('Browser AI not initialized. Call initBrowserAI() first.');
            }
            
            if (this.currentModelType === 'webllm') {
                return await this.generateWithWebLLM(prompt, options);
            } else if (this.currentModelType === 'tensorflow') {
                return await this.generateWithTensorFlow(prompt, options);
            } else {
                throw new Error(`Unknown model type: ${this.currentModelType}`);
            }
        } catch (error) {
            console.error('Error generating text:', error);
            throw error;
        }
    },

    /**
     * Build prompt for paraphrasing cover letter to reduce AI detection.
     * @param {string} text Cover letter text to paraphrase
     * @returns {string} Prompt for AI
     */
    getParaphrasePrompt(text) {
        return "Paraphrase the following cover letter so it sounds more natural and human-written.\n\n" +
            "Requirements: Vary sentence length (mix short and long). Use occasional contractions (I'm, I've, that's). " +
            "Keep the meaning, tone, structure, and all key information exactly the same. Use British English.\n\n" +
            "Output ONLY the paraphrased cover letter as plain text. No JSON, no markdown, no explanation before or after.\n\n" +
            "---\n\n" + (text || '');
    },

    /**
     * Humanize AI-generated text by removing common artifacts and hype.
     * This is intentionally conservative to avoid changing meaning.
     * @param {string} text
     * @returns {string}
     */
    humanizeText(text) {
        if (typeof text !== 'string' || text.trim() === '') {
            return text;
        }

        let output = text;
        output = output.replace(/\[control_\d+\]/gi, '');
        var originalLength = output.length;
        var replaceHitCount = 0;
        var emojiRemoved = 0;
        var quoteNormalized = 0;

        // Remove common AI preambles and closers
        output = output.replace(/^\s*(Sure|Of course|Certainly|Great question)[^.\n]*[.\n]+/i, '');
        output = output.replace(/\bI hope this helps\.?\b/gi, '');
        output = output.replace(/\bLet me know if you have any questions\.?\b/gi, '');
        output = output.replace(/\bAs an AI[^.\n]*[.\n]*/gi, '');

        // Replace common AI-sounding words (aggressive humaniser for cover letters)
        const replacements = [
            [/\butili[sz]e\b/gi, 'use'], [/\butili[sz]es\b/gi, 'uses'], [/\butili[sz]ed\b/gi, 'used'],
            [/\bleverage\b/gi, 'use'], [/\bleveraged\b/gi, 'used'], [/\bleverages\b/gi, 'uses'],
            [/\badditionally\b/gi, 'also'],
            [/\bfurthermore\b/gi, 'also'],
            [/\bmoreover\b/gi, 'also'],
            [/\bimpactful\b/gi, 'useful'],
            [/\brobust\b/gi, 'solid'],
            [/\bcomprehensive\b/gi, 'clear'],
            [/\bdelve into\b/gi, 'look into'],
            [/\bunderscore\b/gi, 'show'], [/\bunderscores\b/gi, 'shows'], [/\bunderscored\b/gi, 'showed'],
            [/\bseamless\b/gi, 'smooth'], [/\bseamlessly\b/gi, 'smoothly'],
            [/\bcutting[- ]edge\b/gi, 'modern'],
            [/\binnovative\b/gi, 'new'],
            [/\bstrategic\b/gi, 'planned'],
            [/\bresults[- ]driven\b/gi, 'results focused'],
            [/\bdetail[- ]oriented\b/gi, 'detail focused'],
            [/\bfast[- ]paced\b/gi, 'busy'],
            [/\bpassionate\b/gi, 'keen'], [/\bpassionately\b/gi, 'keenly'],
            [/\bexcited to\b/gi, 'keen to'],
            [/\bI am excited\b/gi, 'I am keen'],
            [/\beager\b/gi, 'keen'], [/\beagerly\b/gi, 'keenly'],
            [/\bthrilled\b/gi, 'pleased'],
            [/\bdelighted\b/gi, 'pleased'],
            [/\bproactive\b/gi, 'active'], [/\bproactively\b/gi, 'actively'],
            [/\bsynerg(y|ies)\b/gi, 'fit'],
            [/\bhighly\b/gi, 'very'],
            [/\bexceptional\b/gi, 'strong'], [/\bexceptionally\b/gi, 'very'],
            [/\boutstanding\b/gi, 'strong'], [/\boutstandingly\b/gi, 'very'],
            [/\bdynamic\b/gi, 'focused'],
            [/\bresults[- ]oriented\b/gi, 'results focused'],
            [/\bproven track record\b/gi, 'track record'],
            [/\bstrong background\b/gi, 'background'],
            [/\binnovative solutions\b/gi, 'solutions'],
            [/\bkey stakeholders\b/gi, 'stakeholders'],
            [/\bstrategic initiatives\b/gi, 'initiatives'],
            [/\bdrive(ing)? impact\b/gi, 'improve'],
            [/\bmission[- ]driven\b/gi, 'purpose led'],
            [/\bend[- ]to[- ]end\b/gi, 'full'],
            [/\bworld[- ]class\b/gi, 'strong'],
            [/\bresults[- ]based\b/gi, 'results focused'],
            [/\bthought leadership\b/gi, 'leadership'],
            [/\bvalue[- ]add(ed)?\b/gi, 'value'],
            [/\bdemonstrate\b/gi, 'show'], [/\bdemonstrates\b/gi, 'shows'], [/\bdemonstrated\b/gi, 'showed'],
            [/\bfacilitate\b/gi, 'help'], [/\bfacilitates\b/gi, 'helps'], [/\bfacilitated\b/gi, 'helped'],
            [/\benable\b/gi, 'help'], [/\benables\b/gi, 'helps'], [/\benabled\b/gi, 'helped'],
            [/\bshowcase\b/gi, 'show'], [/\bshowcases\b/gi, 'shows'], [/\bshowcased\b/gi, 'showed'],
            [/\bhighlight\b/gi, 'show'], [/\bhighlights\b/gi, 'shows'], [/\bhighlighted\b/gi, 'showed'], [/\bhighlighting\b/gi, 'showing'],
            [/\binstrumental\b/gi, 'important'],
            [/\bintegral\b/gi, 'key'],
            [/\bmyriad\b/gi, 'many'],
            [/\bplethora\b/gi, 'many'],
            [/\bstreamline\b/gi, 'simplify'], [/\bstreamlines\b/gi, 'simplifies'], [/\bstreamlined\b/gi, 'simplified'],
            [/\benhance\b/gi, 'improve'], [/\benhances\b/gi, 'improves'], [/\benhanced\b/gi, 'improved'],
            [/\bimplement\b/gi, 'put in place'], [/\bimplemented\b/gi, 'put in place'], [/\bimplements\b/gi, 'puts in place'],
            [/\btransform\b/gi, 'improve'], [/\btransformed\b/gi, 'improved'], [/\btransforms\b/gi, 'improves'], [/\btransforming\b/gi, 'improving'],
            [/\bempower\b/gi, 'help'], [/\bempowers\b/gi, 'helps'], [/\bempowered\b/gi, 'helped'],
            [/\bfoster\b/gi, 'support'], [/\bfosters\b/gi, 'supports'], [/\bfostered\b/gi, 'supported'],
            [/\bnuance\b/gi, 'detail'], [/\bnuanced\b/gi, 'detailed'],
            [/\boptimal\b/gi, 'best'],
            [/\boptimize\b/gi, 'improve'], [/\boptimizes\b/gi, 'improves'], [/\boptimized\b/gi, 'improved'],
            [/\bproficient\b/gi, 'skilled'],
            [/\bexemplify\b/gi, 'show'], [/\bexemplifies\b/gi, 'shows'], [/\bexemplified\b/gi, 'showed'],
            [/\barticulate\b/gi, 'explain'], [/\barticulates\b/gi, 'explains'], [/\barticulated\b/gi, 'explained'],
        ];
        replacements.forEach(([pattern, replacement]) => {
            var before = output;
            output = output.replace(pattern, replacement);
            if (before !== output) replaceHitCount += 1;
        });

        // Remove generic filler phrases
        output = output.replace(/\bIn order to\b/gi, 'To');
        output = output.replace(/\bDue to the fact that\b/gi, 'Because');
        output = output.replace(/\bAt the end of the day\b/gi, '');
        output = output.replace(/\bIn conclusion\b/gi, '');
        output = output.replace(/\bOverall,\b/gi, '');
        output = output.replace(/\bIt is important to note that\b/gi, '');
        output = output.replace(/\bIt is worth noting that\b/gi, '');
        output = output.replace(/\bIn summary\b/gi, '');
        output = output.replace(/\bTo summarize\b/gi, '');
        output = output.replace(/\bIn this regard\b/gi, '');
        output = output.replace(/\bIn today['’]s (?:fast[- ]paced|dynamic) environment\b/gi, 'In this role');
        output = output.replace(/\bI am confident that I\b/gi, 'I');
        output = output.replace(/\bI am confident\b/gi, 'I am sure');
        output = output.replace(/\bI believe\b/gi, 'I think');
        output = output.replace(/\bI would be thrilled\b/gi, 'I would be pleased');

        // Remove emojis
        var beforeEmoji = output;
        output = output.replace(/[\u{1F300}-\u{1FAFF}]/gu, '');
        if (beforeEmoji !== output) emojiRemoved = 1;

        // Normalize quotes to ASCII
        var beforeQuotes = output;
        output = output.replace(/[“”]/g, '"').replace(/[‘’]/g, "'");
        if (beforeQuotes !== output) quoteNormalized = 1;

        // Normalize whitespace
        output = output.replace(/[ \t]{2,}/g, ' ');
        output = output.replace(/\n{3,}/g, '\n\n');

        // Convert American to British spelling (UK documents)
        output = this.convertToBritishSpelling(output);

        var finalOutput = output.trim();
        return finalOutput;
    },

    /**
     * Convert American spelling to British (UK English).
     * @param {string} text
     * @returns {string}
     */
    convertToBritishSpelling(text) {
        if (typeof text !== 'string' || text.trim() === '') return text;
        var pairs = [
            [/\borganization\b/gi, 'organisation'], [/\borganizations\b/gi, 'organisations'], [/\borganized\b/gi, 'organised'], [/\borganizing\b/gi, 'organising'], [/\borganize\b/gi, 'organise'],
            [/\bemphasize\b/gi, 'emphasise'], [/\bemphasized\b/gi, 'emphasised'], [/\bemphasizing\b/gi, 'emphasising'],
            [/\bcolor\b/gi, 'colour'], [/\bcolors\b/gi, 'colours'], [/\bcenter\b/gi, 'centre'], [/\bcenters\b/gi, 'centres'],
            [/\brealize\b/gi, 'realise'], [/\brealized\b/gi, 'realised'], [/\brealizes\b/gi, 'realises'],
            [/\brecognize\b/gi, 'recognise'], [/\brecognized\b/gi, 'recognised'], [/\brecognizes\b/gi, 'recognises'],
            [/\banalyze\b/gi, 'analyse'], [/\banalyzed\b/gi, 'analysed'], [/\banalyzes\b/gi, 'analyses'],
            [/\bfavor\b/gi, 'favour'], [/\bfavors\b/gi, 'favours'], [/\bfavored\b/gi, 'favoured'],
            [/\bhonor\b/gi, 'honour'], [/\bhonors\b/gi, 'honours'], [/\bhonored\b/gi, 'honoured'],
            [/\blabor\b/gi, 'labour'], [/\blabors\b/gi, 'labours'], [/\bneighbor\b/gi, 'neighbour'], [/\bneighbors\b/gi, 'neighbours'],
            [/\bbehavior\b/gi, 'behaviour'], [/\bbehaviors\b/gi, 'behaviours'], [/\bbehavioral\b/gi, 'behavioural'],
            [/\bprioritize\b/gi, 'prioritise'], [/\bprioritized\b/gi, 'prioritised'], [/\bprioritizing\b/gi, 'prioritising'],
            [/\boptimize\b/gi, 'optimise'], [/\boptimized\b/gi, 'optimised'], [/\boptimizing\b/gi, 'optimising'],
            [/\bspecialize\b/gi, 'specialise'], [/\bspecialized\b/gi, 'specialised'], [/\bspecializing\b/gi, 'specialising'],
            [/\bcustomize\b/gi, 'customise'], [/\bcustomized\b/gi, 'customised'], [/\bcustomizing\b/gi, 'customising'],
            [/\bmaximize\b/gi, 'maximise'], [/\bmaximized\b/gi, 'maximised'], [/\bminimize\b/gi, 'minimise'], [/\bminimized\b/gi, 'minimised'],
            [/\bfinalize\b/gi, 'finalise'], [/\bfinalized\b/gi, 'finalised'], [/\bsummarize\b/gi, 'summarise'], [/\bsummarized\b/gi, 'summarised'],
            [/\btraveled\b/gi, 'travelled'], [/\btraveling\b/gi, 'travelling'], [/\blabeled\b/gi, 'labelled'], [/\blabeling\b/gi, 'labelling'],
            [/\bcanceled\b/gi, 'cancelled'], [/\bcanceling\b/gi, 'cancelling'], [/\bmodeled\b/gi, 'modelled'], [/\bmodeling\b/gi, 'modelling'],
            [/\bfulfill\b/gi, 'fulfil'], [/\bfulfillment\b/gi, 'fulfilment'], [/\bdefense\b/gi, 'defence'],
            [/\bdialog\b/gi, 'dialogue'], [/\bdialogs\b/gi, 'dialogues'],
            [/\bfervor\b/gi, 'fervour'], [/\bfervors\b/gi, 'fervours'],
            [/\bvigor\b/gi, 'vigour'], [/\bvigors\b/gi, 'vigours'],
            [/\bharbor\b/gi, 'harbour'], [/\bharbors\b/gi, 'harbours'], [/\bharbored\b/gi, 'harboured'],
            [/\brumor\b/gi, 'rumour'], [/\brumors\b/gi, 'rumours'],
            [/\bflavor\b/gi, 'flavour'], [/\bflavors\b/gi, 'flavours'], [/\bflavored\b/gi, 'flavoured'],
            [/\bendeavor\b/gi, 'endeavour'], [/\bendeavors\b/gi, 'endeavours'], [/\bendeavored\b/gi, 'endeavoured'],
            [/\bfavorable\b/gi, 'favourable'], [/\bunfavorable\b/gi, 'unfavourable'],
            [/\bhonorable\b/gi, 'honourable'], [/\barmor\b/gi, 'armour'], [/\barmors\b/gi, 'armours'],
            [/\bhumor\b/gi, 'humour'], [/\bhumors\b/gi, 'humours'], [/\bhumored\b/gi, 'humoured'],
            [/\brevolutionizing\b/gi, 'revolutionising'], [/\brevolutionized\b/gi, 'revolutionised'],
            [/\brevolutionize\b/gi, 'revolutionise'], [/\brevolutionizes\b/gi, 'revolutionises'],
            [/\bmodernizing\b/gi, 'modernising'], [/\bmodernized\b/gi, 'modernised'],
            [/\bmodernize\b/gi, 'modernise'], [/\bmodernizes\b/gi, 'modernises'],
            [/\bfamiliarizing\b/gi, 'familiarising'], [/\bfamiliarized\b/gi, 'familiarised'],
            [/\bfamiliarize\b/gi, 'familiarise'], [/\bfamiliarizes\b/gi, 'familiarises'],
            [/\bparalyzing\b/gi, 'paralysing'], [/\bparalyzed\b/gi, 'paralysed'],
            [/\bparalyze\b/gi, 'paralyse'], [/\bparalyzes\b/gi, 'paralyses'],
        ];
        pairs.forEach(function(p) { text = text.replace(p[0], p[1]); });
        return text;
    },

    /**
     * Recursively humanize all string values in an object/array.
     * @param {*} data
     * @returns {*}
     */
    shouldHumanizeKey(key) {
        const allowed = [
            'description',
            'content',
            'summary',
            'professional_summary',
            'recommendations',
            'strengths',
            'weaknesses',
            'enhanced_recommendations',
            'answer_text',
            'cover_letter_text',
            'responsibilities',
            'responsibility_categories',
            'items',
            'notes',
            'details'
        ];
        return allowed.includes(String(key || '').toLowerCase());
    },

    humanizeObjectStrings(data, keyPath = []) {
        if (typeof data === 'string') {
            const key = keyPath.length ? keyPath[keyPath.length - 1] : '';
            if (!key || this.shouldHumanizeKey(key)) {
                return this.humanizeText(data);
            }
            return data;
        }
        if (Array.isArray(data)) {
            return data.map((item) => this.humanizeObjectStrings(item, keyPath));
        }
        if (data && typeof data === 'object') {
            const result = {};
            Object.keys(data).forEach((key) => {
                result[key] = this.humanizeObjectStrings(data[key], keyPath.concat([key]));
            });
            return result;
        }
        return data;
    },
    
    /**
     * Generate text with WebLLM
     * @param {string} prompt Input prompt
     * @param {Object} options Generation options
     * @returns {Promise<string>} Generated text
     */
    async generateWithWebLLM(prompt, options = {}) {
        if (!this.webllmEngine) {
            throw new Error('WebLLM engine not initialized');
        }
        
        // WebLLM uses OpenAI-compatible chat.completions API
        // Convert prompt to messages format
        const messages = [
            { role: 'user', content: prompt }
        ];
        
        const generationConfig = {
            messages: messages,
            temperature: options.temperature || 0.7,
            max_tokens: options.maxTokens || 2000,
            stop: options.stop || [],
        };
        
        // Try OpenAI-compatible API first, fallback to complete() if needed
        let response;
        if (this.webllmEngine.chat && this.webllmEngine.chat.completions && this.webllmEngine.chat.completions.create) {
            response = await this.webllmEngine.chat.completions.create(generationConfig);
            // Extract text from OpenAI-compatible response
            return response.choices[0]?.message?.content || response.choices[0]?.text || '';
        } else if (this.webllmEngine.complete) {
            // Fallback to older API
            response = await this.webllmEngine.complete(prompt, {
                temperature: generationConfig.temperature,
                maxTokens: generationConfig.max_tokens,
                stop: generationConfig.stop
            });
            return typeof response === 'string' ? response : (response.text || response.content || '');
        } else {
            throw new Error('WebLLM engine does not support chat.completions or complete methods');
        }
    },
    
    /**
     * Generate text with TensorFlow.js (simplified - would need actual model)
     * @param {string} prompt Input prompt
     * @param {Object} options Generation options
     * @returns {Promise<string>} Generated text
     */
    async generateWithTensorFlow(prompt, options = {}) {
        // Placeholder - actual implementation would use TensorFlow.js model
        // For now, return a simple response
        throw new Error('TensorFlow.js generation not yet implemented. Please use WebLLM instead.');
    },
    
    /**
     * Check if browser supports WebGPU/WebGL for AI execution
     * @returns {Object} Support information
     */
    checkBrowserSupport() {
        const support = {
            webgpu: false,
            webgl: false,
            indexeddb: false,
            required: false,
        };
        
        // Check WebGPU
        if (navigator.gpu) {
            support.webgpu = true;
            support.required = true;
        }
        
        // Check WebGL as fallback
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
        if (gl) {
            support.webgl = true;
            if (!support.webgpu) {
                support.required = true;
            }
        }
        
        // Check IndexedDB
        if (window.indexedDB) {
            support.indexeddb = true;
        }
        
        return support;
    },
    
    /**
     * Get TensorFlow.js model URL (placeholder)
     * @param {string} modelName Model name
     * @returns {string} Model URL
     */
    getTensorFlowModelUrl(modelName) {
        // Placeholder - would need actual model URLs
        return `https://example.com/models/${modelName}/model.json`;
    },
    
    /**
     * Estimate model size in bytes
     * @param {string} modelName Model name
     * @param {string} modelType Model type
     * @returns {number} Estimated size in bytes
     */
    estimateModelSize(modelName, modelType = 'webllm') {
        // Rough estimates in bytes
        const sizeMap = {
            webllm: {
                'llama3.2': 2 * 1024 * 1024 * 1024, // ~2GB
                'mistral': 4 * 1024 * 1024 * 1024, // ~4GB
                'phi3': 2 * 1024 * 1024 * 1024, // ~2GB
            },
            tensorflow: {
                'universal-sentence-encoder': 100 * 1024 * 1024, // ~100MB
            }
        };
        
        return sizeMap[modelType]?.[modelName] || 500 * 1024 * 1024; // Default 500MB
    },
    
    /**
     * Cleanup and release resources
     */
    async cleanup() {
        if (this.webllmEngine) {
            // Release WebLLM resources
            this.webllmEngine = null;
        }
        
        if (this.tensorflowModel) {
            // Dispose TensorFlow.js model
            this.tensorflowModel.dispose();
            this.tensorflowModel = null;
        }
        
        this.currentModel = null;
        this.currentModelType = null;
    }
};

