<?php
require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'Entry-Level Healthcare Careers (UK)';
$metaDescription = 'Healthcare careers you can start without a degree. Entry-level roles, training, salaries. UK guide for healthcare support jobs.';
$canonicalUrl = APP_URL . '/blog/job-search/entry-level-healthcare-careers.php';
$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL . '/'],
    ['name' => 'Blog', 'url' => APP_URL . '/blog/'],
    ['name' => 'Job Search', 'url' => APP_URL . '/blog/job-search/'],
    ['name' => $pageTitle, 'url' => $canonicalUrl],
];

$jobs = [
    [
        'id' => 'mental-health-support-assistant',
        'title' => 'Mental Health Support Worker',
        'number' => 1,
        'overview' => 'Mental Health Support Workers provide emotional and practical support to individuals facing mental health challenges, helping develop coping strategies and maintain daily routines.',
        'requirements' => 'Compassion, communication skills, and relevant training. Mental Health or Counselling Support courses boost employability.',
        'salary' => [
            'Average: £22,000 - £32,000 annually',
            'Senior/Specialised: £35,000+',
        ],
        'training' => 'Study mental health conditions, therapeutic communication, and crisis management through Counselling Skills training.',
    ],
    [
        'id' => 'child-mental-health-support',
        'title' => 'Child Mental Health Support Worker',
        'number' => 2,
        'overview' => 'These professionals provide emotional and behavioural support to children experiencing mental health challenges, working in schools, care homes, or community settings to identify early signs of anxiety, depression, or trauma.',
        'requirements' => 'Training in child psychology, safeguarding, or counselling. Practical knowledge of supporting children with mental health issues is often valued over formal qualifications.',
        'salary' => [
            'Entry Level: £20,000 - £26,000 annually',
            'Experienced: Up to £32,000+ in educational settings',
        ],
        'training' => 'Complete Child Mental Health Support Worker training covering emotional development, safeguarding, and child psychology fundamentals.',
    ],
    [
        'id' => 'emergency-care-assistant',
        'title' => 'Emergency Care Assistant (ECA)',
        'number' => 3,
        'overview' => 'ECAs work alongside paramedics providing life-saving support in urgent situations, responding to emergency calls, transporting patients, and delivering vital first aid or CPR.',
        'requirements' => 'First aid, patient care, and emergency response training. Many enter after completing healthcare support or ambulance care programmes.',
        'salary' => [
            'Entry Level: £22,000 - £28,000 annually',
            'Experienced: £35,000+ as Paramedic or Technician',
        ],
        'training' => 'Learn patient assessment, trauma response, and essential life support through Emergency Care Assistant training.',
    ],
    [
        'id' => 'first-aid-responder',
        'title' => 'First Aider ',
        'number' => 4,
        'overview' => 'First Aid Responders handle medical emergencies before professional help arrives, providing immediate care during accidents, injuries, or sudden illnesses. They work across offices, schools, factories, and public facilities.',
        'requirements' => 'First Aid at Work certificate or equivalent training. Many organisations cover training costs as part of health and safety compliance.',
        'salary' => [
            'Average: £20,000 - £30,000 annually',
            'Key Skills: CPR, emergency response, injury management',
        ],
        'training' => 'Obtain First Aid at Work certification covering life-saving skills, risk assessment, and UK workplace health and safety protocols.',
    ],
    [
        'id' => 'laboratory-assistant',
        'title' => 'Laboratory Assistant',
        'number' => 5,
        'overview' => 'Laboratory Assistants collect and process biological samples, prepare equipment, record data, and assist scientists in testing and research critical for diagnosing and preventing disease.',
        'requirements' => 'GCSEs (particularly science subjects) and practical lab skills. Medical Laboratory Assistant training provides technical grounding.',
        'salary' => [
            'Starting: £20,000 annually',
            'Experienced: £30,000+ with specialisation',
        ],
        'training' => 'Learn laboratory safety, sample handling, and data recording through Laboratory Assistant courses.',
    ],
    [
        'id' => 'pharmacy-assistant',
        'title' => 'Pharmacy Assistant',
        'number' => 6,
        'overview' => 'Pharmacy Assistants support pharmacists in preparing prescriptions, managing stock, advising customers, and ensuring medications are dispensed accurately and safely.',
        'requirements' => 'Basic GCSEs and on-the-job training. Pharmacy Assistant training significantly improves employment prospects.',
        'salary' => [
            'Average: £20,000 - £27,000 annually',
            'Experienced: £30,000+ as Pharmacy Technician',
        ],
        'training' => 'Complete Pharmacy Assistant training covering pharmaceutical practice, stock control, and customer care.',
    ],
    [
        'id' => 'physiotherapy-assistant',
        'title' => 'Physiotherapy Assistant',
        'number' => 7,
        'overview' => 'Physiotherapy Assistants help patients recover from injuries, illnesses, or surgeries by setting up treatment areas, guiding exercises, monitoring progress, and providing motivational support.',
        'requirements' => 'GCSEs and caring attitude. Physiotherapy assistant training or healthcare experience enhances employability.',
        'salary' => [
            'Average: £21,000 - £29,000 annually',
            'Private Clinics: Up to £35,000+',
        ],
        'training' => 'Study anatomy, rehabilitation techniques, and patient communication through physiotherapy courses.',
    ],
    [
        'id' => 'hospital-porter',
        'title' => 'Hospital Porter',
        'number' => 8,
        'overview' => 'Hospital Porters keep facilities running smoothly by transporting patients between departments, delivering equipment, assisting with meals, and maintaining cleanliness and safety standards.',
        'requirements' => 'Most employers provide on-the-job training. Healthcare support training or background in health and safety beneficial.',
        'salary' => [
            'Entry Level: £20,000 - £25,000 annually',
            'Experienced: £30,000+ in specialist wards',
        ],
        'training' => 'Gain foundation skills through Healthcare Assistant courses covering patient care, hygiene, communication, and workplace safety.',
    ],
    [
        'id' => 'health-social-care-assistant',
        'title' => 'Support Worker',
        'number' => 9,
        'overview' => 'Support Workers help individuals live safely and comfortably through assistance with personal care, meal preparation, medication administration, and emotional support. They work across hospitals, residential homes, and community settings.',
        'requirements' => 'Level 2 or Level 3 Health and Social Care certificate or equivalent training. Many employers provide on-the-job training for the right candidates.',
        'salary' => [
            'Entry Level: £19,000 - £23,000 annually',
            'Experienced: Up to £28,000+ with night shifts or senior roles',
        ],
        'training' => 'Pursue a Level 3 Diploma in Health and Social Care. Focus on learning safeguarding, person-centred care, and professional communication skills.',
    ],
    [
        'id' => 'nursing-assistant',
        'title' => 'Nursing Assistant',
        'number' => 10,
        'overview' => 'Nursing Assistants support registered nurses in delivering essential patient care across various healthcare settings. Daily responsibilities include assisting with personal hygiene, recording vital signs, preparing medical equipment, and supporting patient mobility and rehabilitation.',
        'requirements' => 'No university degree required. Most positions accept GCSE-level education plus relevant healthcare training such as care certificates or introductory healthcare courses covering patient support, safeguarding, and infection control.',
        'salary' => [
            'Entry Level: £20,000 - £26,000 annually',
            'Work Settings: NHS hospitals, care homes, mental health facilities, community services',
        ],
        'training' => 'Begin with Level 2 or 3 Health & Social Care qualifications. Look for online courses that cover patient care fundamentals, communication techniques, and clinical support procedures.',
    ],
    [
        'id' => 'dental-assistant',
        'title' => 'Dental Assistant',
        'number' => 11,
        'overview' => 'Dental Assistants provide clinical and administrative support, preparing treatment rooms, sterilising instruments, assisting during procedures, and managing patient records and appointments.',
        'requirements' => 'Dental support or medical administration training covering oral hygiene, infection control, and dental terminology.',
        'salary' => [
            'Entry Level: £21,000 - £26,000 annually',
            'Experienced: £30,000+ in private practice',
        ],
        'training' => 'Complete dental assistant courses covering procedures, communication, record management, and infection prevention.',
    ],
    [
        'id' => 'sterile-services-technician',
        'title' => 'Cleaning Services',
        'number' => 12,
        'overview' => 'These professionals maintain hygiene and safety by disinfecting patient areas, sterilising surgical instruments, managing infection control protocols, and ensuring equipment meets hospital standards.',
        'requirements' => 'Entry-level positions with on-the-job training. Clinical cleaning or infection control training provides competitive advantage.',
        'salary' => [
            'Average: £20,000 - £28,000 annually',
            'Senior Roles: £30,000+ in management',
            'Demand: Strong need for trained infection prevention professionals',
        ],
        'training' => 'Pursue clinical cleaning and infection control training covering sterilisation procedures and safety protocols.',
    ],
    [
        'id' => 'massage-therapist',
        'title' => 'Massage Therapist',
        'number' => 13,
        'overview' => 'Massage Therapists help clients relieve pain, reduce stress, and improve wellbeing through therapeutic techniques including sports massage, aromatherapy, and reflexology.',
        'requirements' => 'Practical training and certification from massage therapy courses. University degree not required.',
        'salary' => [
            'Average: £22,000 - £35,000 annually',
            'Self-Employed: £50,000+ with established clientele',
            'Specialisations: Sports rehabilitation, reflexology, holistic wellness',
        ],
        'training' => 'Complete massage therapy training covering anatomy, physiology, and professional massage techniques.',
    ],
    [
        'id' => 'nutrition-advisor',
        'title' => 'Nutrition Coach',
        'number' => 14,
        'overview' => 'Nutrition Advisors help clients improve wellbeing through balanced diet plans, lifestyle guidance, and motivational support, educating on healthy eating habits and disease prevention.',
        'requirements' => 'Nutrition training or diploma providing foundational knowledge of dietary science, metabolism, and health coaching techniques.',
        'salary' => [
            'Average: £22,000 - £35,000 annually',
            'Freelance/Corporate: £40,000+ for consultants',
            'Specialisations: Sports nutrition, weight management, clinical health coaching',
        ],
        'training' => 'Complete nutrition advisor courses covering evidence-based nutrition, dietary planning, and lifestyle support techniques.',
    ],
    [
        'id' => 'health-safety-officer',
        'title' => 'Health and Safety Officer',
        'number' => 15,
        'overview' => 'Health and Safety Officers ensure workplaces comply with safety laws, preventing accidents and injuries through risk assessments, policy implementation, employee training, and incident investigation.',
        'requirements' => 'Level 3 Certificate in Health and Safety or equivalent accredited training. Many professionals begin as First Aid Responders before specialising.',
        'salary' => [
            'Average: £28,000 - £45,000 annually',
            'Senior: £50,000+ for consultants and managers',
        ],
        'training' => 'Pursue Health and Safety training covering hazard identification, risk assessment, and safety culture promotion in workplace environments.',
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => $metaDescription,
        'canonicalUrl' => $canonicalUrl,
        'metaImage' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1200&h=630&fit=crop',
        'metaKeywords' => 'healthcare jobs UK, healthcare careers without degree, entry-level healthcare, NHS jobs, healthcare assistant, healthcare training UK, healthcare support worker, UK healthcare careers',
        'breadcrumbs' => $breadcrumbs,
        'structuredDataType' => 'article',
        'structuredData' => [
            'title' => $pageTitle,
            'description' => $metaDescription,
            'url' => $canonicalUrl,
            'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1200&h=630&fit=crop',
            'datePublished' => '2025-01-01',
            'dateModified' => date('Y-m-d'),
            'articleSection' => 'Job Market Insights',
        ],
    ]); ?>
    <style>
        /* Prevent image overlap with info boxes */
        .job-content-wrapper {
            overflow: hidden;
        }
        .job-content-wrapper .float-right {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        /* Ensure info boxes clear the float and go full width */
        .job-content-wrapper .bg-blue-50 {
            clear: both;
            margin-top: 1.5rem;
            width: 100%;
        }
        @media (max-width: 1023px) {
            .job-content-wrapper .float-right {
                float: none;
                margin: 0 auto 1.5rem;
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
<?php partial('header'); ?>

<main id="main-content" role="main">
    <article class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[80%] rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="absolute -bottom-32 right-0 h-64 w-64 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <header class="space-y-8">
                <div class="inline-flex items-center rounded-full border border-white/20 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white/80">
                    Job Market Insights
                </div>
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl"><?php echo e($pageTitle); ?></h1>
                <p class="text-lg text-slate-200 max-w-3xl leading-relaxed">
                    Discover rewarding career opportunities in healthcare that require training, not a traditional university qualification.
                </p>
                <nav aria-label="Page sections" class="rounded-xl border border-white/20 bg-white/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-white/70 mb-3">Jump to a section</p>
                    <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                        <li><a href="#introduction" class="text-white/90 hover:text-white underline underline-offset-2">Introduction</a></li>
                        <li><a href="#mental-health-support-assistant" class="text-white/90 hover:text-white underline underline-offset-2">Job listings</a></li>
                        <li><a href="#finding-your-fit" class="text-white/90 hover:text-white underline underline-offset-2">Finding your fit</a></li>
                        <li><a href="#getting-started" class="text-white/90 hover:text-white underline underline-offset-2">Getting started</a></li>
                        <li><a href="#conclusion" class="text-white/90 hover:text-white underline underline-offset-2">Conclusion</a></li>
                    </ul>
                </nav>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/blog/job-search/" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow hover:bg-slate-100">
                        Back to job search guides
                    </a>
                    <a href="#introduction" class="inline-flex items-center justify-center rounded-lg border border-white/40 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10">
                        Start reading
                    </a>
                </div>
            </header>
        </div>
    </article>

    <!-- Introduction Section -->
    <section id="introduction" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-6">Your Path to a Healthcare Career Starts Here</h2>

            <!-- Full-width image -->
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 h-64">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80" alt="Healthcare professional in a clinical setting" class="w-full h-full object-cover" width="800" height="320">
            </div>

            <div class="space-y-4 text-base leading-relaxed text-slate-700">
                <p>The healthcare sector is experiencing unprecedented growth. With an ageing population and persistent staffing shortages, opportunities have never been greater for those seeking meaningful work. The best part? Many healthcare careers are accessible through short training programmes rather than expensive university degrees.</p>
                <p>From patient care and emergency support to laboratory work and mental health assistance, entry-level roles form the foundation of the healthcare system. These positions help hospitals, clinics, and care facilities deliver safe, effective care every day.</p>
                <p>In this comprehensive guide, we'll explore healthcare jobs you can pursue with accredited training courses, vocational qualifications, or on-the-job learning. Whether you're a career changer, school leaver, or seeking more fulfilling work, there's a healthcare path waiting for you.</p>
            </div>
        </div>
    </section>

    <!-- Job Listings -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        <?php
        $gradientColors = [
            'from-blue-100 via-indigo-100 to-purple-100',
            'from-emerald-100 via-teal-100 to-cyan-100',
            'from-rose-100 via-pink-100 to-fuchsia-100',
            'from-amber-100 via-orange-100 to-red-100',
            'from-violet-100 via-purple-100 to-indigo-100',
        ];
        $iconColors = ['blue-600', 'emerald-600', 'rose-600', 'amber-600', 'violet-600'];

        // Icon paths for different healthcare roles
        $healthcareIcons = [
            'nursing-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
            'health-social-care-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'first-aid-responder' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
            'health-safety-officer' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
            'child-mental-health-support' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'nutrition-advisor' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
            'dental-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'emergency-care-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
            'hospital-porter' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
            'pharmacy-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
            'physiotherapy-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'laboratory-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
            'massage-therapist' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>',
            'sterile-services-technician' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'mental-health-support-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        ];

        $iconIndex = 0;
        foreach ($jobs as $jobIndex => $job):
            $gradient = $gradientColors[$iconIndex % count($gradientColors)];
            $iconColor = $iconColors[$iconIndex % count($iconColors)];
            $iconPath = $healthcareIcons[$job['id']] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>';
            $iconIndex++;
        ?>
            <article id="<?php echo e($job['id']); ?>" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
                <div class="flex items-center gap-3 mb-6">
                    <?php if (!empty($job['bonus'])): ?>
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Bonus</span>
                    <?php else: ?>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold"><?php echo $job['number']; ?></span>
                    <?php endif; ?>
                    <h2 class="text-2xl font-semibold text-slate-900"><?php echo e($job['title']); ?></h2>
                </div>

                <div class="job-content-wrapper">
                    <!-- Floating image on the right -->
                    <div class="float-right ml-6 mb-4 w-48 lg:w-64 flex-shrink-0">
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br <?php echo $gradient; ?> h-48 lg:h-64 flex items-center justify-center">
                            <svg class="w-16 h-16 text-<?php echo $iconColor; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <?php echo $iconPath; ?>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-6 text-base leading-relaxed text-slate-700">
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-2">Overview</h3>
                            <p><?php echo e($job['overview']); ?></p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-900 mb-2">Education Requirements</h3>
                            <p><?php echo e($job['requirements']); ?></p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-900 mb-2">How to Get Started</h3>
                            <p><?php echo e($job['training']); ?></p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 mb-2">Salary & Career Path</h3>
                            <ul class="space-y-1 text-blue-800">
                                <?php foreach ($job['salary'] as $item): ?>
                                    <li><?php echo e($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="clear-both"></div>
                </div>
            </article>
            <?php if ($jobIndex === 7): ?>
            <?php partial('blog-cta-inline', ['heading' => 'Found a healthcare role that interests you?', 'subtext' => 'Create a professional CV to apply—free to get started.']); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </section>

    <!-- Finding Your Fit Section -->
    <section id="finding-your-fit" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-6">Finding Your Perfect Healthcare Role</h2>

            <!-- Full-width image -->
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-violet-100 via-purple-100 to-fuchsia-100 h-64 flex items-center justify-center">
                <svg class="w-24 h-24 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>

            <div class="space-y-6 text-base leading-relaxed text-slate-700">
                <p>Every healthcare role offers something unique. The key is matching your natural strengths and preferences to the right opportunity. Here's what to consider:</p>

                <div class="relative">
                    <!-- Floating image on the right -->
                    <div class="float-right ml-6 mb-4 w-48 flex-shrink-0">
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 h-48 flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Consider Your Natural Strengths</h3>
                        <p class="mb-2">Are you someone who thrives on helping others directly, or do you prefer supporting healthcare operations behind the scenes? Mental health support and care work involve regular interaction, while roles like medical transcription or laboratory work offer more independent, focused environments.</p>
                        <p class="mb-2">Think about your energy levels too. Emergency care and hospital portering are active roles, whereas administrative positions offer more desk-based work. Both are equally valuable in healthcare.</p>
                        <p>Location matters as well. Some roles take you into hospitals and clinics daily, while others allow remote work or community-based settings. Consider what environment helps you perform at your best.</p>
                    </div>
                    <div class="clear-both"></div>
                </div>

                <div class="relative">
                    <!-- Floating image on the left -->
                    <div class="float-left mr-6 mb-4 w-48 flex-shrink-0">
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-emerald-100 via-teal-100 to-cyan-100 h-48 flex items-center justify-center">
                            <svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Flexibility and Growth Opportunities</h3>
                        <p class="mb-2">Healthcare offers remarkable flexibility. Many positions accommodate part-time schedules, night shifts, or weekend work—perfect if you're balancing other commitments. Roles like nutrition coaching and massage therapy often allow self-employment, giving you control over your schedule.</p>
                        <p class="mb-2">Career development paths vary by role. Some positions offer clear progression routes (like moving from healthcare assistant to nursing associate), while others encourage specialisation in areas like sports rehabilitation or holistic wellness.</p>
                        <p>Remember, you're not locked into one path. Many healthcare professionals start in one area and transition to another as their interests evolve. The skills you gain are often transferable across different healthcare settings.</p>
                    </div>
                    <div class="clear-both"></div>
                </div>

                <div>
                    <h3 class="font-semibold text-slate-900 mb-3">Making Your Decision</h3>
                    <p>There's no single "right" healthcare career—only the one that fits you best. Start by identifying roles that align with your interests, then explore training options. Many entry-level positions provide on-the-job learning, while others benefit from completing accredited courses first.</p>
                </div>

                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-base text-emerald-800">
                    <strong>Pro Tip:</strong> Don't feel pressured to choose immediately. Many people start with a general healthcare course like Health and Social Care or Mental Health Support, then specialise once they've gained experience and discovered what they enjoy most.
                </div>
            </div>
        </div>
    </section>

    <!-- Getting Started Section -->
    <section id="getting-started" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-6">Getting Started in Healthcare</h2>

            <!-- Full-width image -->
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-amber-100 via-orange-100 to-red-100 h-64 flex items-center justify-center">
                <svg class="w-24 h-24 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <div class="space-y-6 text-base leading-relaxed text-slate-700">
                <p>Starting a healthcare career is more accessible than many people realise. With the right training and determination, you can enter this rewarding field regardless of your educational background.</p>

                <div class="relative">
                    <!-- Floating image on the right -->
                    <div class="float-right ml-6 mb-4 w-48 flex-shrink-0">
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-rose-100 via-pink-100 to-fuchsia-100 h-48 flex items-center justify-center">
                            <svg class="w-16 h-16 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">The Benefits of a Healthcare Career</h3>
                        <p class="mb-2">Healthcare roles provide exceptional job security. With an ageing population and ongoing staffing needs, opportunities continue to grow across the UK. Whether you're interested in patient care, clinical support, or wellness services, there's consistent demand for skilled professionals.</p>
                        <p class="mb-2">Beyond stability, healthcare careers offer genuine purpose. Every day, you'll contribute to improving people's wellbeing and quality of life. This sense of meaning often leads to high job satisfaction.</p>
                        <p class="mb-2">The sector also provides clear advancement opportunities. Many professionals begin in entry-level positions and progress to senior roles through experience and additional training. Starting salaries are competitive, with many roles offering £20,000-£30,000+ and potential to reach £40,000-£50,000+ with specialisation.</p>
                        <p>Training is flexible too. Accredited online courses allow you to learn while maintaining your current job or other commitments, making career transitions smoother.</p>
                    </div>
                    <div class="clear-both"></div>
                </div>

                <div class="relative">
                    <!-- Floating image on the left -->
                    <div class="float-left mr-6 mb-4 w-48 flex-shrink-0">
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 h-48 flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900 mb-3">Your Action Plan</h3>
                        <p class="mb-3">Ready to begin? Follow these steps to launch your healthcare career:</p>
                        <ol class="list-decimal list-inside space-y-3 ml-2">
                            <li><strong>Explore Your Options:</strong> Review the roles listed above and note which ones resonate with your interests, skills, and lifestyle preferences.</li>
                            <li><strong>Find the Right Training:</strong> Research accredited courses that match your chosen path. Look for programmes that offer practical skills and recognised certifications.</li>
                            <li><strong>Build Experience:</strong> Consider volunteering, shadowing opportunities, or entry-level positions to gain hands-on experience and confirm your interest.</li>
                            <li><strong>Create a Strong Application:</strong> Develop a CV that highlights your training, any relevant experience, and transferable skills from previous roles or life experience.</li>
                            <li><strong>Apply with Confidence:</strong> Target employers that match your interests—whether that's the NHS, private healthcare providers, care homes, or wellness centres.</li>
                        </ol>
                    </div>
                    <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Conclusion Section -->
    <section id="conclusion" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-6">Your Healthcare Career Starts Today</h2>

            <!-- Full-width image -->
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-green-100 via-emerald-100 to-teal-100 h-64 flex items-center justify-center">
                <svg class="w-24 h-24 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <div class="relative">
                <!-- Floating image on the right -->
                <div class="float-right ml-6 mb-4 w-48 flex-shrink-0">
                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 h-48 flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                </div>

                <div class="space-y-4 text-base leading-relaxed text-slate-700 mb-8">
                    <p>Whether you're drawn to direct patient care, clinical support, wellness services, or administrative roles, there's a healthcare career path waiting for you—no university degree required.</p>
                    <p>The opportunities are vast, the demand is real, and the impact you can make is immeasurable. With accredited training, dedication, and the right support tools like a professionally crafted CV, you're ready to take the first step toward a rewarding healthcare career.</p>
                </div>
                <div class="clear-both"></div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-slate-900 mb-3">Build Your Healthcare Career Today</h3>
                <p class="text-slate-700 mb-4">Use our CV builder to create a standout application that showcases your training and passion for healthcare.</p>
                <?php if (isLoggedIn()): ?>
                    <a href="/dashboard.php" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-md hover:bg-blue-700 transition-colors">
                        Build Your CV
                    </a>
                <?php else: ?>
                    <a href="#" data-open-register class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-md hover:bg-blue-700 transition-colors">
                        Create Free Account
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    $relatedArticles = [
        ['title' => 'Using AI in Your Job Applications', 'excerpt' => 'Harness tools like ChatGPT responsibly for CVs, cover letters, and interviews.', 'url' => '/blog/job-search/using-ai-in-job-applications.php', 'section' => 'Job Search'],
        ['title' => 'How to Update Your CV: A Complete Guide', 'excerpt' => 'Step-by-step advice for refreshing every section of your CV whenever opportunity knocks.', 'url' => '/blog/cv-tips/how-to-update-your-cv.php', 'section' => 'CV Tips'],
        ['title' => 'Six Steps to Refreshing Your CV in 30 Minutes', 'excerpt' => 'Quick wins to modernise your CV layout, keywords, and story.', 'url' => '/blog/job-search/how-to-refresh-your-cv-in-30-minutes.php', 'section' => 'Job Search'],
    ];
    ?>
    <section class="bg-white border-y border-slate-200 py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-slate-900 mb-8">Other Articles You Might Be Interested In</h2>
            <div class="grid gap-6 md:grid-cols-3">
                <?php foreach ($relatedArticles as $article): ?>
                    <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></div>
                        <div class="relative">
                            <div class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                <?php echo e($article['section']); ?>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-slate-900">
                                <?php echo e($article['title']); ?>
                            </h3>
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                                <?php echo e($article['excerpt']); ?>
                            </p>
                            <a href="<?php echo e($article['url']); ?>" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                                Read full guide
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="mt-8 text-center">
                <a href="/blog/" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    View all blog articles
                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
            <p class="text-xs text-slate-500">Last updated: <?php echo date('j F Y'); ?>.</p>
        </div>
    </section>

    <?php partial('resources/article-cta'); ?>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
