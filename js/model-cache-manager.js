/**
 * Model Cache Manager
 * Manages downloaded AI models in IndexedDB for browser-based AI execution
 */

const ModelCacheManager = {
    dbName: 'aiModelCache',
    dbVersion: 1,
    storeName: 'models',
    
    /**
     * Initialize IndexedDB database
     * @returns {Promise<IDBDatabase>} Database instance
     */
    async initDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => resolve(request.result);
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Create object store if it doesn't exist
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const store = db.createObjectStore(this.storeName, { keyPath: 'id' });
                    store.createIndex('modelName', 'modelName', { unique: false });
                    store.createIndex('modelType', 'modelType', { unique: false });
                    store.createIndex('version', 'version', { unique: false });
                    store.createIndex('lastUsed', 'lastUsed', { unique: false });
                }
            };
        });
    },
    
    /**
     * Get cached model
     * @param {string} modelName Model name (e.g., 'llama3.2')
     * @param {string} modelType Model type ('webllm' or 'tensorflow')
     * @returns {Promise<Object|null>} Cached model data or null if not found
     */
    async getModel(modelName, modelType = 'webllm') {
        try {
            const db = await this.initDB();
            const transaction = db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const index = store.index('modelName');
            
            return new Promise((resolve, reject) => {
                const request = index.getAll(modelName);
                
                request.onsuccess = () => {
                    const models = request.result.filter(m => m.modelType === modelType);
                    if (models.length > 0) {
                        // Update last used timestamp
                        const model = models[0];
                        model.lastUsed = Date.now();
                        this.saveModel(model).catch(console.error);
                        resolve(model);
                    } else {
                        resolve(null);
                    }
                };
                
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('Error getting cached model:', error);
            return null;
        }
    },
    
    /**
     * Save model to cache
     * @param {Object} modelData Model data to cache
     * @returns {Promise<void>}
     */
    async saveModel(modelData) {
        try {
            const db = await this.initDB();
            const transaction = db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            
            const modelRecord = {
                id: `${modelData.modelType}_${modelData.modelName}_${modelData.version || '1.0'}`,
                modelName: modelData.modelName,
                modelType: modelData.modelType,
                version: modelData.version || '1.0',
                data: modelData.data,
                size: modelData.size || 0,
                cachedAt: modelData.cachedAt || Date.now(),
                lastUsed: modelData.lastUsed || Date.now(),
            };
            
            return new Promise((resolve, reject) => {
                const request = store.put(modelRecord);
                request.onsuccess = () => resolve();
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('Error saving model to cache:', error);
            throw error;
        }
    },
    
    /**
     * Delete model from cache
     * @param {string} modelName Model name
     * @param {string} modelType Model type
     * @returns {Promise<void>}
     */
    async deleteModel(modelName, modelType = 'webllm') {
        try {
            const db = await this.initDB();
            const transaction = db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            const index = store.index('modelName');
            
            return new Promise((resolve, reject) => {
                const request = index.getAll(modelName);
                
                request.onsuccess = () => {
                    const models = request.result.filter(m => m.modelType === modelType);
                    const deletePromises = models.map(model => {
                        return new Promise((res, rej) => {
                            const deleteReq = store.delete(model.id);
                            deleteReq.onsuccess = () => res();
                            deleteReq.onerror = () => rej(deleteReq.error);
                        });
                    });
                    
                    Promise.all(deletePromises).then(() => resolve()).catch(reject);
                };
                
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('Error deleting model from cache:', error);
            throw error;
        }
    },
    
    /**
     * Get total cache size
     * @returns {Promise<number>} Total size in bytes
     */
    async getCacheSize() {
        try {
            const db = await this.initDB();
            const transaction = db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            
            return new Promise((resolve, reject) => {
                const request = store.getAll();
                
                request.onsuccess = () => {
                    const models = request.result;
                    const totalSize = models.reduce((sum, model) => sum + (model.size || 0), 0);
                    resolve(totalSize);
                };
                
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('Error getting cache size:', error);
            return 0;
        }
    },
    
    /**
     * Clear all cached models
     * @returns {Promise<void>}
     */
    async clearAll() {
        try {
            const db = await this.initDB();
            const transaction = db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            
            return new Promise((resolve, reject) => {
                const request = store.clear();
                request.onsuccess = () => resolve();
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('Error clearing cache:', error);
            throw error;
        }
    },
    
    /**
     * Get list of all cached models
     * @returns {Promise<Array>} List of cached models
     */
    async listModels() {
        try {
            const db = await this.initDB();
            const transaction = db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            
            return new Promise((resolve, reject) => {
                const request = store.getAll();
                
                request.onsuccess = () => {
                    const models = request.result.map(m => ({
                        id: m.id,
                        modelName: m.modelName,
                        modelType: m.modelType,
                        version: m.version,
                        size: m.size || 0,
                        cachedAt: m.cachedAt,
                        lastUsed: m.lastUsed,
                    }));
                    resolve(models);
                };
                
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('Error listing models:', error);
            return [];
        }
    },
    
    /**
     * Clean up old models based on last used timestamp
     * @param {number} maxAgeMs Maximum age in milliseconds (default: 30 days)
     * @returns {Promise<number>} Number of models deleted
     */
    async cleanupOldModels(maxAgeMs = 30 * 24 * 60 * 60 * 1000) {
        try {
            const models = await this.listModels();
            const now = Date.now();
            const toDelete = models.filter(m => (now - m.lastUsed) > maxAgeMs);
            
            for (const model of toDelete) {
                await this.deleteModel(model.modelName, model.modelType);
            }
            
            return toDelete.length;
        } catch (error) {
            console.error('Error cleaning up old models:', error);
            return 0;
        }
    },
    
    /**
     * Format bytes to human-readable string
     * @param {number} bytes Size in bytes
     * @returns {string} Formatted string (e.g., "2.5 GB")
     */
    formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
};

