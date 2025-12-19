<?php
require_once __DIR__ . '/../../php/helpers.php';

$pageTitle = 'Top Healthcare Jobs You Can Start Without a Degree';
$metaDescription = 'Explore rewarding healthcare careers that don\'t require a university degree. Learn about salaries, training paths, and how to start your healthcare career today.';

$jobs = [
    [
        'id' => 'nursing-assistant',
        'title' => 'Nursing Assistant',
        'number' => 1,
        'overview' => 'Nursing Assistants support registered nurses in delivering essential patient care across various healthcare settings. Daily responsibilities include assisting with personal hygiene, recording vital signs, preparing medical equipment, and supporting patient mobility and rehabilitation.',
        'requirements' => 'No university degree required. Most positions accept GCSE-level education plus relevant healthcare training such as care certificates or introductory healthcare courses covering patient support, safeguarding, and infection control.',
        'salary' => [
            'Entry Level: £20,000 - £26,000 annually',
            'Progression: Senior Healthcare Assistant → Nursing Associate → Registered Nurse',
            'Work Settings: NHS hospitals, care homes, mental health facilities, community services',
        ],
        'training' => 'Begin with Level 2 or 3 Health & Social Care qualifications. Look for online courses that cover patient care fundamentals, communication techniques, and clinical support procedures.',
    ],
    [
        'id' => 'health-social-care-assistant',
        'title' => 'Health and Social Care Assistant / Support Worker',
        'number' => 2,
        'overview' => 'Support Workers help individuals live safely and comfortably through assistance with personal care, meal preparation, medication administration, and emotional support. They work across hospitals, residential homes, and community settings.',
        'requirements' => 'Level 2 or Level 3 Health and Social Care certificate or equivalent training. Many employers provide on-the-job training for the right candidates.',
        'salary' => [
            'Entry Level: £19,000 - £23,000 annually',
            'Experienced: Up to £28,000+ with night shifts or senior roles',
            'Progression: Senior Care → Team Leader → Nursing Assistant',
        ],
        'training' => 'Pursue a Level 3 Diploma in Health and Social Care. Focus on learning safeguarding, person-centred care, and professional communication skills.',
    ],
    [
        'id' => 'first-aid-responder',
        'title' => 'First Aider / Workplace First Aid Responder',
        'number' => 3,
        'overview' => 'First Aid Responders handle medical emergencies before professional help arrives, providing immediate care during accidents, injuries, or sudden illnesses. They work across offices, schools, factories, and public facilities.',
        'requirements' => 'First Aid at Work certificate or equivalent training. Many organisations cover training costs as part of health and safety compliance.',
        'salary' => [
            'Average: £20,000 - £30,000 annually',
            'Progression: Health & Safety Officer → Emergency Care Assistant → Paramedic',
            'Key Skills: CPR, emergency response, injury management',
        ],
        'training' => 'Obtain First Aid at Work certification covering life-saving skills, risk assessment, and UK workplace health and safety protocols.',
    ],
    [
        'id' => 'health-safety-officer',
        'title' => 'Health and Safety Officer',
        'number' => 4,
        'overview' => 'Health and Safety Officers ensure workplaces comply with safety laws, preventing accidents and injuries through risk assessments, policy implementation, employee training, and incident investigation.',
        'requirements' => 'Level 3 Certificate in Health and Safety or equivalent accredited training. Many professionals begin as First Aid Responders before specialising.',
        'salary' => [
            'Average: £28,000 - £45,000 annually',
            'Senior: £50,000+ for consultants and managers',
            'Progression: Safety Advisor → HSE Manager → Compliance Consultant',
        ],
        'training' => 'Pursue Health and Safety training covering hazard identification, risk assessment, and safety culture promotion in workplace environments.',
    ],
    [
        'id' => 'child-mental-health-support',
        'title' => 'Child Mental Health Support Worker',
        'number' => 5,
        'overview' => 'These professionals provide emotional and behavioural support to children experiencing mental health challenges, working in schools, care homes, or community settings to identify early signs of anxiety, depression, or trauma.',
        'requirements' => 'Training in child psychology, safeguarding, or counselling. Practical knowledge of supporting children with mental health issues is often valued over formal qualifications.',
        'salary' => [
            'Entry Level: £20,000 - £26,000 annually',
            'Experienced: Up to £32,000+ in educational settings',
            'Progression: Child Wellbeing Practitioner → Mental Health Coordinator',
        ],
        'training' => 'Complete Child Mental Health Support Worker training covering emotional development, safeguarding, and child psychology fundamentals.',
    ],
    [
        'id' => 'reflexology-practitioner',
        'title' => 'Reflexology / Acupressure Practitioner',
        'number' => 6,
        'overview' => 'Practitioners help clients manage pain, anxiety, and stress through natural techniques stimulating the body\'s healing response. They apply pressure to specific points to promote relaxation and balance energy flow.',
        'requirements' => 'Accredited reflexology or acupressure training. Most practitioners complete online qualifications before gaining wellness setting experience.',
        'salary' => [
            'Average: £25,000 - £40,000 annually',
            'Self-Employed: High earning potential with established client base',
            'Progression: Complementary Therapist → Holistic Health Specialist → Wellness Consultant',
        ],
        'training' => 'Complete reflexology and acupressure courses covering reflex zones, pressure point techniques, and client consultation methods.',
    ],
    [
        'id' => 'medical-transcriptionist',
        'title' => 'Medical Transcriptionist',
        'number' => 7,
        'overview' => 'Medical Transcriptionists convert recorded medical reports into written documents such as patient histories, referral letters, and discharge summaries, maintaining accuracy and confidentiality.',
        'requirements' => 'Strong English, grammar, and computer skills plus medical terminology knowledge. All can be learned through accredited training courses.',
        'salary' => [
            'Entry Level: £20,000 - £28,000 annually',
            'Experienced: Up to £35,000+ in specialised fields',
            'Benefits: Remote work options and flexible schedules',
            'Progression: Transcriptionist → Transcription Editor → Medical Secretary',
        ],
        'training' => 'Learn medical terminology, formatting standards, and transcription techniques through medical transcription training courses.',
    ],
    [
        'id' => 'nutrition-advisor',
        'title' => 'Nutrition Advisor / Health Coach',
        'number' => 8,
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
        'id' => 'care-home-support',
        'title' => 'Care Home Support Worker / Residential Care Assistant',
        'number' => 9,
        'overview' => 'Care Home Support Workers provide daily assistance, companionship, and emotional support to residents, helping with meals, hygiene, mobility, and medication while ensuring dignity and comfort.',
        'requirements' => 'Training in health and social care, safeguarding, or dementia awareness. Practical knowledge and caring attitude highly valued.',
        'salary' => [
            'Entry Level: £20,000 - £26,000 annually',
            'Experienced: Up to £32,000+ for team leaders',
            'Progression: Support Worker → Senior Care Worker → Care Home Manager',
        ],
        'training' => 'Pursue Health and Social Care training covering person-centred care, safeguarding, communication, and health and safety.',
    ],
    [
        'id' => 'dental-assistant',
        'title' => 'Dental Assistant / Dental Receptionist',
        'number' => 10,
        'overview' => 'Dental Assistants provide clinical and administrative support, preparing treatment rooms, sterilising instruments, assisting during procedures, and managing patient records and appointments.',
        'requirements' => 'Dental support or medical administration training covering oral hygiene, infection control, and dental terminology.',
        'salary' => [
            'Entry Level: £21,000 - £26,000 annually',
            'Experienced: £30,000+ in private practice',
            'Progression: Dental Assistant → Dental Nurse → Practice Manager',
        ],
        'training' => 'Complete dental assistant courses covering procedures, communication, record management, and infection prevention.',
    ],
    [
        'id' => 'emergency-care-assistant',
        'title' => 'Emergency Care Assistant (ECA)',
        'number' => 11,
        'overview' => 'ECAs work alongside paramedics providing life-saving support in urgent situations, responding to emergency calls, transporting patients, and delivering vital first aid or CPR.',
        'requirements' => 'First aid, patient care, and emergency response training. Many enter after completing healthcare support or ambulance care programmes.',
        'salary' => [
            'Entry Level: £22,000 - £28,000 annually',
            'Experienced: £35,000+ as Paramedic or Technician',
            'Progression: ECA → Ambulance Technician → Paramedic → Emergency Medical Dispatcher',
        ],
        'training' => 'Learn patient assessment, trauma response, and essential life support through Emergency Care Assistant training.',
    ],
    [
        'id' => 'hospital-porter',
        'title' => 'Hospital Porter / Healthcare Support Worker',
        'number' => 12,
        'overview' => 'Hospital Porters keep facilities running smoothly by transporting patients between departments, delivering equipment, assisting with meals, and maintaining cleanliness and safety standards.',
        'requirements' => 'Most employers provide on-the-job training. Healthcare support training or background in health and safety beneficial.',
        'salary' => [
            'Entry Level: £20,000 - £25,000 annually',
            'Experienced: £30,000+ in specialist wards',
            'Progression: Hospital Porter → Healthcare Assistant → Nursing Associate',
        ],
        'training' => 'Gain foundation skills through Healthcare Assistant courses covering patient care, hygiene, communication, and workplace safety.',
    ],
    [
        'id' => 'pharmacy-assistant',
        'title' => 'Pharmacy Assistant / Pharmacy Dispenser',
        'number' => 13,
        'overview' => 'Pharmacy Assistants support pharmacists in preparing prescriptions, managing stock, advising customers, and ensuring medications are dispensed accurately and safely.',
        'requirements' => 'Basic GCSEs and on-the-job training. Pharmacy Assistant training significantly improves employment prospects.',
        'salary' => [
            'Average: £20,000 - £27,000 annually',
            'Experienced: £30,000+ as Pharmacy Technician',
            'Progression: Pharmacy Assistant → Pharmacy Technician → Senior Dispenser',
        ],
        'training' => 'Complete Pharmacy Assistant training covering pharmaceutical practice, stock control, and customer care.',
    ],
    [
        'id' => 'physiotherapy-assistant',
        'title' => 'Physiotherapy Assistant',
        'number' => 14,
        'overview' => 'Physiotherapy Assistants help patients recover from injuries, illnesses, or surgeries by setting up treatment areas, guiding exercises, monitoring progress, and providing motivational support.',
        'requirements' => 'GCSEs and caring attitude. Physiotherapy assistant training or healthcare experience enhances employability.',
        'salary' => [
            'Average: £21,000 - £29,000 annually',
            'Private Clinics: Up to £35,000+',
            'Progression: Physiotherapy Assistant → Senior Assistant → Qualified Physiotherapist',
        ],
        'training' => 'Study anatomy, rehabilitation techniques, and patient communication through physiotherapy courses.',
    ],
    [
        'id' => 'laboratory-assistant',
        'title' => 'Laboratory Assistant / Medical Lab Technician',
        'number' => 15,
        'overview' => 'Laboratory Assistants collect and process biological samples, prepare equipment, record data, and assist scientists in testing and research critical for diagnosing and preventing disease.',
        'requirements' => 'GCSEs (particularly science subjects) and practical lab skills. Medical Laboratory Assistant training provides technical grounding.',
        'salary' => [
            'Starting: £20,000 annually',
            'Experienced: £30,000+ with specialisation',
            'Progression: Lab Assistant → Lab Technician → Biomedical Scientist',
        ],
        'training' => 'Learn laboratory safety, sample handling, and data recording through Laboratory Assistant courses.',
    ],
    [
        'id' => 'massage-therapist',
        'title' => 'Massage Therapist / Holistic Therapist',
        'number' => 16,
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
        'id' => 'sterile-services-technician',
        'title' => 'Clinical Cleaning Operative / Sterile Services Technician',
        'number' => 17,
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
        'id' => 'mental-health-support-assistant',
        'title' => 'Counselling Support Worker / Mental Health Support Assistant',
        'number' => 18,
        'overview' => 'Mental Health Support Workers provide emotional and practical support to individuals facing mental health challenges, helping develop coping strategies and maintain daily routines.',
        'requirements' => 'Compassion, communication skills, and relevant training. Mental Health or Counselling Support courses boost employability.',
        'salary' => [
            'Average: £22,000 - £32,000 annually',
            'Senior/Specialised: £35,000+',
            'Progression: Support Assistant → Counsellor → Mental Health Practitioner',
        ],
        'training' => 'Study mental health conditions, therapeutic communication, and crisis management through Counselling Skills training.',
    ],
    [
        'id' => 'occupational-therapy-assistant',
        'title' => 'Occupational Therapy Assistant (OTA)',
        'number' => 21,
        'overview' => 'OTAs support occupational therapists in helping individuals regain independence after illness, injury, or disability by assisting with daily activities, adapting environments, and tracking recovery progress.',
        'requirements' => 'GCSEs and healthcare experience or occupational therapy assistant training introducing therapy principles, patient support, and rehabilitation.',
        'salary' => [
            'Entry Level: £21,000 - £28,000 annually',
            'Senior OTA: £35,000+',
            'Progression: OTA → Senior OTA → Qualified Occupational Therapist',
        ],
        'training' => 'Complete Occupational Therapy courses covering patient care essentials, mobility assistance, and adaptive techniques.',
        'bonus' => true,
    ],
];

$faqs = [
    [
        'question' => 'What is the highest-paying healthcare job without a degree in the UK?',
        'answer' => 'Health and Safety Officers, Senior Healthcare Assistants, and experienced Healthcare Support Workers can earn £30,000-£45,000+ annually with experience and accredited training.',
    ],
    [
        'question' => 'Can I work for the NHS without a degree?',
        'answer' => 'Yes, the NHS offers numerous roles including Healthcare Assistant, First Aider, and Clinical Support Worker that don\'t require degrees. Completing accredited training courses strengthens your application.',
    ],
    [
        'question' => 'What is the easiest healthcare job to get into in the UK?',
        'answer' => 'Healthcare Assistant and Support Worker roles are typically the most accessible. With Level 2-3 Health and Social Care training, you can become job-ready within weeks.',
    ],
    [
        'question' => 'How long does it take to qualify for a healthcare job without a degree?',
        'answer' => 'Most accredited healthcare courses take 4-12 weeks to complete. Once certified, you can immediately begin applying for entry-level positions.',
    ],
    [
        'question' => 'Can I be successful in healthcare without a degree?',
        'answer' => 'Absolutely. Many UK healthcare professionals build rewarding careers through vocational training and continuous learning. With experience, you can progress to supervisory or specialist roles earning competitive salaries.',
    ],
    [
        'question' => 'What healthcare careers can I start with short online training?',
        'answer' => 'You can quickly train for roles like First Aider, Mental Health Support Worker, Nutrition Advisor, and Medical Transcriptionist with short, flexible online courses.',
    ],
    [
        'question' => 'Are healthcare jobs in high demand?',
        'answer' => 'Yes. Healthcare Assistants, First Aiders, Mental Health Support Workers, and Occupational Therapy Assistants are among the most in-demand roles, with consistent vacancies across the UK.',
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle . ' | Simple CV Builder',
        'metaDescription' => $metaDescription,
        'canonicalUrl' => APP_URL . '/resources/jobs/healthcare-support-roles-no-degree.php',
        'structuredDataType' => 'article',
        'structuredData' => [
            'title' => $pageTitle,
            'description' => $metaDescription,
            'datePublished' => '2025-01-01',
            'dateModified' => date('Y-m-d'),
            'keywords' => 'healthcare jobs, healthcare careers, healthcare assistant, NHS jobs, healthcare training, healthcare without degree, UK healthcare jobs',
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
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/resources/jobs/" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow hover:bg-slate-100">
                        Back to job insights
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
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-emerald-100 via-teal-100 to-cyan-100 h-64 flex items-center justify-center">
                <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
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
            'reflexology-practitioner' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m-2 4c.5.5 1.5 1 2.5 1s2-.5 2.5-1m-5-4a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m-2 4c.5.5 1.5 1 2.5 1s2-.5 2.5-1m-5-4a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>',
            'medical-transcriptionist' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'nutrition-advisor' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
            'care-home-support' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
            'dental-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'emergency-care-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
            'hospital-porter' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
            'pharmacy-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
            'physiotherapy-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'laboratory-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
            'massage-therapist' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>',
            'sterile-services-technician' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'mental-health-support-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
            'occupational-therapy-assistant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        ];

        $iconIndex = 0;
        foreach ($jobs as $job):
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
        <?php endforeach; ?>
    </section>

    <!-- How to Choose Section -->
    <section id="how-to-choose" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-6">How to Choose the Right Healthcare Path for You</h2>

            <!-- Full-width image -->
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-violet-100 via-purple-100 to-fuchsia-100 h-64 flex items-center justify-center">
                <svg class="w-24 h-24 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>

            <div class="space-y-6 text-base leading-relaxed text-slate-700">
                <p>Selecting the right healthcare career depends on your personality, interests, and lifestyle preferences. Consider these key factors:</p>

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
                        <h3 class="font-semibold text-slate-900 mb-3">Your Work Style Preferences</h3>
                        <p class="mb-2"><strong>Patient Contact:</strong> Do you enjoy direct interaction (nursing assistant, care worker) or prefer behind-the-scenes work (lab assistant, medical transcriptionist)?</p>
                        <p class="mb-2"><strong>Physical Activity:</strong> Some roles are physically demanding (hospital porter, physiotherapy assistant) while others are more sedentary (medical transcriptionist, health and safety officer).</p>
                        <p><strong>Work Environment:</strong> Consider whether you'd prefer hospitals, clinics, community settings, laboratories, or remote work.</p>
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
                        <h3 class="font-semibold text-slate-900 mb-3">Schedule and Work-Life Balance</h3>
                        <p class="mb-2"><strong>Shift Patterns:</strong> Many healthcare roles offer flexible shifts, including nights and weekends.</p>
                        <p class="mb-2"><strong>Part-Time Options:</strong> Roles like massage therapy and nutrition coaching often allow part-time or freelance work.</p>
                        <p><strong>Career Progression:</strong> Some paths offer clear advancement (nursing assistant to registered nurse) while others focus on specialisation (sports massage, dermatology).</p>
                    </div>
                    <div class="clear-both"></div>
                </div>

                <div>
                    <h3 class="font-semibold text-slate-900 mb-3">Getting Started</h3>
                    <p>The beauty of healthcare careers is their accessibility. You don't need to commit to one path forever—many professionals start in one role and transition to another as they discover their interests and strengths.</p>
                </div>

                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-base text-emerald-800">
                    <strong>Pro Tip:</strong> Start with a single course that matches your interests—Health and Social Care, First Aid, or Mental Health Support—and grow your career step by step. Many courses can be completed online while you continue working.
                </div>
            </div>
        </div>
    </section>

    <!-- Next Steps Section -->
    <section id="next-steps" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-6">Your Next Steps to Begin a Healthcare Career</h2>

            <!-- Full-width image -->
            <div class="mb-6 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-amber-100 via-orange-100 to-red-100 h-64 flex items-center justify-center">
                <svg class="w-24 h-24 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <div class="space-y-6 text-base leading-relaxed text-slate-700">
                <p>The healthcare industry welcomes people from all backgrounds—no degree required. What matters most is compassion, commitment, and willingness to learn.</p>

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
                        <h3 class="font-semibold text-slate-900 mb-3">Why Choose Healthcare?</h3>
                        <p class="mb-2"><strong>Job Security:</strong> Healthcare is one of the fastest-growing sectors with thousands of vacancies nationwide.</p>
                        <p class="mb-2"><strong>Meaningful Work:</strong> Make a real difference in people's lives every day.</p>
                        <p class="mb-2"><strong>Career Progression:</strong> Clear pathways from entry-level to senior positions.</p>
                        <p class="mb-2"><strong>Flexible Learning:</strong> Train online while working, with accredited qualifications recognised across the industry.</p>
                        <p><strong>Competitive Pay:</strong> Many roles offer good starting salaries with potential to earn £30,000-£50,000+ with experience.</p>
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
                        <h3 class="font-semibold text-slate-900 mb-3">How to Get Started</h3>
                        <ol class="list-decimal list-inside space-y-2 ml-2">
                            <li><strong>Research Roles:</strong> Review the jobs above and identify which align with your interests and strengths</li>
                            <li><strong>Choose Training:</strong> Select an accredited course that provides the foundation for your chosen path</li>
                            <li><strong>Gain Experience:</strong> Look for volunteer opportunities, apprenticeships, or entry-level positions</li>
                            <li><strong>Build Your CV:</strong> Highlight your training, any healthcare experience, and transferable skills from other jobs</li>
                            <li><strong>Apply Strategically:</strong> Target NHS trusts, private clinics, care homes, or wellness centres depending on your interests</li>
                        </ol>
                    </div>
                    <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5">
            <h2 class="text-2xl font-semibold text-slate-900 mb-8">Frequently Asked Questions</h2>

            <!-- Full-width image -->
            <div class="mb-8 rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 h-48 flex items-center justify-center">
                <svg class="w-20 h-20 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <div class="space-y-6">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="font-semibold text-slate-900 mb-2"><?php echo e($faq['question']); ?></h3>
                        <p class="text-slate-700"><?php echo e($faq['answer']); ?></p>
                    </div>
                    <?php if (($index + 1) % 3 === 0 && $index < count($faqs) - 1): ?>
                        <!-- Add a floating image every 3 FAQs -->
                        <div class="my-6 flex justify-center">
                            <div class="rounded-xl overflow-hidden border border-slate-200 bg-gradient-to-br from-slate-100 via-gray-100 to-slate-100 w-48 h-32 flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
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
    $relatedArticles = getRelatedArticles('/resources/jobs/healthcare-support-roles-no-degree.php', 3);
    if (!empty($relatedArticles)):
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
                <a href="/resources/jobs/" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    View all job insights
                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php partial('footer'); ?>
<?php partial('auth-modals'); ?>
</body>
</html>
