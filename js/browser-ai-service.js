/**
 * Browser AI Service
 * Handles AI execution in the browser using WebLLM and TensorFlow.js
 */

const BrowserAIService = {
    webllmEngine: null,
    tensorflowModel: null,
    currentModel: null,
    currentModelType: null,
    
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
     * Initialize WebLLM model
     * @param {string} modelName Model name
     * @param {Function} progressCallback Progress callback
     * @returns {Promise<void>}
     */
    async initWebLLM(modelName, progressCallback = null) {
        try {
            // Check if WebLLM is available
            if (typeof webllm === 'undefined') {
                // Load WebLLM dynamically
                await this.loadWebLLMLibrary();
            }
            
            // Check for cached model
            const cachedModel = await ModelCacheManager.getModel(modelName, 'webllm');
            
            if (progressCallback) {
                progressCallback({ stage: 'checking_cache', message: 'Checking for cached model...' });
            }
            
            const engineConfig = {
                modelName: modelName,
                gpuDeviceId: 0, // Use first GPU
                onProgress: (progress) => {
                    if (progressCallback) {
                        progressCallback({
                            stage: 'downloading',
                            progress: progress.progress,
                            message: `Downloading model: ${Math.round(progress.progress * 100)}%`
                        });
                    }
                }
            };
            
            if (cachedModel && cachedModel.data) {
                engineConfig.modelCache = cachedModel.data;
            }
            
            this.webllmEngine = await webllm.CreateWebLLMEngine(engineConfig);
            
            // Cache the model for future use
            if (!cachedModel) {
                if (progressCallback) {
                    progressCallback({ stage: 'caching', message: 'Caching model for future use...' });
                }
                await ModelCacheManager.saveModel({
                    modelName: modelName,
                    modelType: 'webllm',
                    data: this.webllmEngine.getModelCache(),
                    size: this.estimateModelSize(modelName),
                });
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
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm/dist/index.js';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load WebLLM library'));
            document.head.appendChild(script);
        });
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
     * Generate text with WebLLM
     * @param {string} prompt Input prompt
     * @param {Object} options Generation options
     * @returns {Promise<string>} Generated text
     */
    async generateWithWebLLM(prompt, options = {}) {
        if (!this.webllmEngine) {
            throw new Error('WebLLM engine not initialized');
        }
        
        const generationConfig = {
            temperature: options.temperature || 0.7,
            maxTokens: options.maxTokens || 2000,
            stop: options.stop || [],
        };
        
        const response = await this.webllmEngine.complete(prompt, generationConfig);
        return response;
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

