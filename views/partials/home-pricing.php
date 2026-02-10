<?php
// Shared pricing section for homepage and /pricing. Expects $pricingLaunchOffer (or uses default).
if (!isset($pricingLaunchOffer)) {
    $pricingLaunchOffer = [
        'active' => true,
        'heading' => 'Try Pro free for 1 month',
        'subtext' => 'No charge today. Cancel anytime from your billing portal.',
    ];
}
$pricingUseRegisterModal = $pricingUseRegisterModal ?? false;
$trialActive = !empty($pricingLaunchOffer['active']);
$pricingCards = [
    [
        'label' => 'Free',
        'price' => '£0',
        'detail' => 'Forever free',
        'highlight' => false,
        'badge' => null,
        'features' => [
            '1 work experience entry',
            '1 project showcase',
            '3 highlighted skills',
            'Minimal template',
        ],
        'button' => ['text' => 'Start for free', 'href' => '/#auth-section', 'dataOpenRegister' => true],
    ],
    [
        'label' => 'Pro Monthly',
        'price' => '£4.99',
        'detail' => 'per month',
        'highlight' => true,
        'badge' => $trialActive ? '1 month free' : null,
        'features' => [
            'Unlimited sections & entries',
            'Professional template with colours',
            'Download print-ready PDFs',
            'Priority email support',
            $trialActive ? 'First month free, then £4.99/month' : null,
        ],
        'button' => ['text' => 'Start free trial', 'href' => '/#auth-section', 'dataOpenRegister' => true, 'requiresAccount' => true],
    ],
    [
        'label' => 'Pro Annual',
        'price' => '£29.99',
        'detail' => 'per year',
        'highlight' => false,
        'badge' => $trialActive ? '1 month free' : 'Best value',
        'features' => [
            'Everything in Pro Monthly',
            'Best value for serious job seekers',
            'Annual billing with Stripe',
            'Priority email support',
            $trialActive ? 'First month free, then £29.99/year' : null,
        ],
        'button' => ['text' => 'Start free trial', 'href' => '/#auth-section', 'dataOpenRegister' => true, 'requiresAccount' => true],
    ],
    [
        'label' => 'Lifetime',
        'price' => '£39.99',
        'detail' => 'one-time payment',
        'highlight' => false,
        'badge' => null,
        'features' => [
            'Unlimited sections & entries',
            'Professional template with colours',
            'Download print-ready PDFs',
            'Priority email support',
            'Lifetime access — no recurring fees',
        ],
        'button' => ['text' => 'Create account to purchase', 'href' => '/#auth-section', 'dataOpenRegister' => true, 'requiresAccount' => true],
    ],
];
// Remove null feature lines (e.g. when trial inactive)
foreach ($pricingCards as &$card) {
    $card['features'] = array_values(array_filter($card['features']));
}
unset($card);
?>
<div class="bg-gray-900 py-16 sm:py-24" id="pricing">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <?php if (!empty($pricingLaunchOffer['active'])): ?>
        <div class="mb-10 rounded-xl border border-amber-400/60 bg-amber-500/10 px-6 py-4 text-center">
            <p class="text-lg font-semibold text-amber-200"><?php echo e($pricingLaunchOffer['heading']); ?></p>
            <p class="mt-1 text-sm text-amber-200/90"><?php echo e($pricingLaunchOffer['subtext']); ?></p>
        </div>
        <?php endif; ?>
        <div class="max-w-2xl text-center mx-auto">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Individual plans</h2>
            <p class="mt-4 text-lg text-gray-300">
                Plans for job seekers and personal CV use. Free tier includes unlimited job tracking and AI-assisted CV and cover letters; the free plan has limited CV sections and no PDF export. Upgrade to Pro for unlimited sections and print-ready PDFs.
            </p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($pricingCards as $card):
                $classes = $card['highlight']
                    ? 'border-blue-500 ring-1 ring-blue-200 bg-white text-gray-900'
                    : 'border-gray-700 bg-gray-800 text-gray-100';
            ?>
                <div class="flex flex-col rounded-2xl border <?php echo $classes; ?> p-8 shadow-xl relative">
                    <?php if (!empty($card['badge'])): ?>
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-lg">
                                <?php echo e($card['badge']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="text-xl font-semibold"><?php echo e($card['label']); ?></h3>
                        <div class="mt-6 flex flex-wrap items-baseline gap-2">
                            <?php if (!empty($card['was_price'])): ?>
                                <span class="text-lg text-gray-400 line-through"><?php echo e($card['was_price']); ?></span>
                            <?php endif; ?>
                            <span class="text-3xl font-bold"><?php echo e($card['price']); ?></span>
                            <span class="text-sm text-gray-400"><?php echo e($card['detail']); ?></span>
                        </div>
                    </div>
                    <ul class="mt-8 space-y-3 text-sm flex-1">
                        <?php foreach ($card['features'] as $feature): ?>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><?php echo e($feature); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="mt-8">
                        <?php
                        $buttonHref = $card['button']['href'] ?? '/#auth-section';
                        $requiresAccount = $card['button']['requiresAccount'] ?? false;
                        $useModal = $pricingUseRegisterModal && !empty($card['button']['dataOpenRegister']);
                        ?>
                        <?php if ($useModal): ?>
                        <button type="button" data-open-register
                           class="inline-flex w-full items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition
                           <?php echo $card['highlight']
                               ? 'bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white'
                               : 'bg-white text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800'; ?>">
                            <?php echo e($card['button']['text']); ?>
                        </button>
                        <?php else: ?>
                        <a href="<?php echo e($buttonHref); ?>"
                           class="inline-flex w-full items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition
                           <?php echo $card['highlight']
                               ? 'bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white'
                               : 'bg-white text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800'; ?>">
                            <?php echo e($card['button']['text']); ?>
                        </a>
                        <?php endif; ?>
                        <?php if ($requiresAccount): ?>
                            <p class="mt-2 text-xs text-center <?php echo $card['highlight'] ? 'text-gray-600' : 'text-gray-400'; ?>">
                                Create a free account first, then upgrade from your dashboard
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-12 rounded-xl border border-gray-700 bg-gray-800 p-6 sm:p-8 text-center">
            <h3 class="text-lg font-semibold text-white">For organisations &amp; agencies</h3>
            <p class="mt-2 text-gray-300">
                Recruitment agencies: manage candidates, team collaboration, and branding in one platform.
            </p>
            <ul class="mt-4 flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm text-gray-400">
                <li>Custom candidate and team limits</li>
                <li>White-label branding and support</li>
            </ul>
            <a href="/organisations.php" class="mt-6 inline-flex items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                Contact us to set up your organisation
            </a>
        </div>
        <p class="mt-8 text-center text-sm text-gray-400">
            Secure payments powered by Stripe. Your card is safe; cancel anytime from your billing portal.
        </p>
    </div>
</div>
