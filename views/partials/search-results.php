<?php
/**
 * Site search results (blog + help content).
 * Expects: $query (string), $results (array of ['title','description','url','category'])
 */
$query = $query ?? '';
$results = $results ?? [];
?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Search results</h1>

    <form action="/" method="get" class="mb-10">
        <label for="search-input" class="sr-only">Search Simple CV Builder</label>
        <div class="flex items-stretch rounded-md border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
            <input
                type="search"
                id="search-input"
                name="s"
                value="<?php echo e($query); ?>"
                placeholder="Search CV tips, job search advice, help guides..."
                class="flex-1 px-4 py-3 text-sm text-gray-900 focus:outline-none"
            >
            <button type="submit" class="px-5 py-3 bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                Search
            </button>
        </div>
    </form>

    <?php if ($query === ''): ?>
        <p class="text-gray-600">Enter a search term above to find CV tips, job search advice, and help guides.</p>
    <?php elseif (empty($results)): ?>
        <p class="text-gray-600">
            No results for &ldquo;<?php echo e($query); ?>&rdquo;. Try a different search, or browse our
            <a href="/blog/" class="text-blue-600 hover:text-blue-700 underline">CV advice articles</a>
            or <a href="/help/faq.php" class="text-blue-600 hover:text-blue-700 underline">FAQ</a>.
        </p>
    <?php else: ?>
        <p class="text-sm text-gray-500 mb-6"><?php echo count($results); ?> result<?php echo count($results) === 1 ? '' : 's'; ?> for &ldquo;<?php echo e($query); ?>&rdquo;</p>
        <div class="space-y-4">
            <?php foreach ($results as $result): ?>
                <a href="<?php echo e($result['url']); ?>" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:border-blue-300 transition-colors">
                    <span class="inline-block text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1"><?php echo e($result['category']); ?></span>
                    <h2 class="text-lg font-semibold text-gray-900 mb-1"><?php echo e($result['title']); ?></h2>
                    <p class="text-sm text-gray-600"><?php echo e($result['description']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
