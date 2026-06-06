<?php
declare(strict_types=1);

use App\Database;

$pdo = Database::pdo();

// Marker table to track if we've run the initial setup
$pdo->exec("CREATE TABLE IF NOT EXISTS _meta (key TEXT PRIMARY KEY, value TEXT)");

$installed = Database::fetchOne("SELECT value FROM _meta WHERE key = 'installed'");
if ($installed) return;

$pdo->exec("
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        name TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE profile (
        id INTEGER PRIMARY KEY CHECK (id = 1),
        name TEXT NOT NULL,
        tagline TEXT NOT NULL,
        bio TEXT NOT NULL,
        email TEXT NOT NULL,
        location TEXT,
        github_url TEXT,
        linkedin_url TEXT,
        twitter_url TEXT,
        dribbble_url TEXT,
        avatar TEXT,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE skills (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        proficiency INTEGER NOT NULL DEFAULT 80,
        sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE projects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT UNIQUE NOT NULL,
        title TEXT NOT NULL,
        summary TEXT NOT NULL,
        description TEXT NOT NULL,
        tech_stack TEXT NOT NULL,
        live_url TEXT,
        repo_url TEXT,
        cover_image TEXT,
        featured INTEGER NOT NULL DEFAULT 0,
        sort_order INTEGER NOT NULL DEFAULT 0,
        completed_on TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT NOT NULL,
        body TEXT NOT NULL,
        read_at TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE project_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        project_id INTEGER NOT NULL,
        filename TEXT NOT NULL,
        caption TEXT,
        sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
    );
");

// Seed default admin
Database::insert('users', [
    'email' => 'admin@portfolio.test',
    'password' => password_hash('admin123', PASSWORD_DEFAULT),
    'name' => 'Admin',
]);

// Seed profile
Database::insert('profile', [
    'id' => 1,
    'name' => 'Yeltsin Batiancila',
    'tagline' => 'Full Stack Developer · Laravel · PHP · Vue · React',
    'bio' => "Full Stack Developer with 9 years of focused experience in Laravel, PHP, and database engineering. I build scalable RESTful APIs and complex multi-tenant SaaS platforms, with a strong eye for performance and clean architecture. My work spans SaaS, e-commerce, SEO tooling, government systems, and quality control platforms, covering everything from backend architecture and payment integrations to polished frontends built with Vue, React, and Filament. I care about shipping code that holds up under real production load, not just code that passes a demo.",
    'email' => 'yeltsinpb@gmail.com',
    'location' => 'Davao, Philippines',
    'github_url' => 'https://github.com/',
    'linkedin_url' => 'https://linkedin.com/',
    'twitter_url' => '',
    'dribbble_url' => 'http://yeltsin.info',
]);

// Seed skills — Yeltsin's actual stack (with AI tools added)
$skills = [
    ['Laravel',          'Frameworks',  95, 1],
    ['Filament',         'Frameworks',  90, 2],
    ['Apiato',           'Frameworks',  88, 3],
    ['Vue.js',           'Frameworks',  85, 4],
    ['React',            'Frameworks',  82, 5],
    ['PHP',              'Languages',   95, 6],
    ['JavaScript',       'Languages',   90, 7],
    ['jQuery / Ajax',    'Languages',   88, 8],
    ['HTML / CSS',       'Languages',   92, 9],
    ['MySQL',            'Databases',   92, 10],
    ['PostgreSQL',       'Databases',   88, 11],
    ['Docker',           'DevOps',      85, 12],
    ['AWS',              'DevOps',      82, 13],
    ['Laravel Forge',    'DevOps',      88, 14],
    ['Git / Jira',       'Tools',       90, 15],
    ['Claude (AI)',      'Tools',       92, 16],
    ['Payment Gateways', 'Backend',     88, 17],
    ['Custom REST APIs', 'Backend',     92, 18],
];
foreach ($skills as [$name, $cat, $prof, $order]) {
    Database::insert('skills', [
        'name' => $name, 'category' => $cat,
        'proficiency' => $prof, 'sort_order' => $order,
    ]);
}

// Seed projects — real work from CV with completion dates
$projects = [
    [
        'slug' => 'cannabrands',
        'title' => 'Cannabrands · Cannabis SaaS Platform',
        'summary' => 'Wholesale and retail SaaS platform for the cannabis industry with full CRM, inventory, and a dual-side marketplace.',
        'description' => "A complete two-sided marketplace serving both wholesale buyers and retail sellers in the cannabis industry. Built the entire admin side with CRUD functionality for products, orders, invoices, lab results, and BOM (bill of materials) management. The CRM module handles lead pipelines, customer profiles, and communication history.\n\nOn the seller and buyer side, I implemented the add-to-cart flow, product and brand presentation pages, and a robust search system that handles strain, potency, and category filters. The platform processes orders end-to-end including compliance documentation specific to the cannabis industry.",
        'tech_stack' => 'Laravel, Filament, Vue, MySQL, REST APIs',
        'live_url' => 'https://cannabrands.app',
        'repo_url' => null,
        'featured' => 1,
        'sort_order' => 1,
        'completed_on' => '2024-12',
    ],
    [
        'slug' => 'printos',
        'title' => 'PRINTOS · Multi-tenant Printing Business Suite',
        'summary' => 'Built solo from the ground up: multi-tenant SaaS for managing printing businesses end to end.',
        'description' => "Designed and built entirely on my own, from database schema through API to UI. PRINTOS is a multi-tenant Laravel SaaS that handles every aspect of running a printing business: order management, product catalog, materials tracking, real-time inventory, production scheduling, and analytics dashboards.\n\nThe multi-tenancy architecture allows each printing shop to have isolated data with their own users, branding, and configuration while sharing the underlying infrastructure. Reports include profit-per-job analytics, materials usage forecasting, and customer lifetime value tracking.",
        'tech_stack' => 'Laravel, Filament, MySQL, Multi-tenancy, Forge, AWS',
        'live_url' => 'https://printos.builtph.com',
        'repo_url' => null,
        'featured' => 1,
        'sort_order' => 2,
        'completed_on' => '2025-09',
    ],
    [
        'slug' => '2friends',
        'title' => '2friends · Encrypted Cloud Storage & Referral SaaS',
        'summary' => 'Subscription SaaS combining encrypted cloud storage (Briefcase) with a 12-level cashback referral network.',
        'description' => "A privacy-focused cloud storage product paired with a multi-level cashback rewards system. The Briefcase product delivers 1TB of encrypted cloud storage across up to 10 devices, with real-time mobile photo and video sync, automatic backup with 30 versions of file history, and large-file sharing via secure links.\n\nThe referral side is where things get interesting: a 12-level cashback engine where users earn from their network of referred friends. At level 3 the cashback equals the product cost, meaning members can effectively use Briefcase for free, and continue earning from the remaining 9 levels. I built the network ledger, payout logic, and reporting interfaces to track this entire structure transparently.\n\nThe stack runs on UK data centers with ISO 27001 certification and full GDPR compliance. Pricing handles VAT logic based on country of residence, and the joining flow integrates with Belgian trade-practice regulations governing the rewards program.",
        'tech_stack' => 'Laravel, MySQL, Stripe, Multi-level cashback engine, GDPR',
        'live_url' => 'https://2friends.biz',
        'repo_url' => null,
        'featured' => 1,
        'sort_order' => 3,
        'completed_on' => '2025-06',
    ],
    [
        'slug' => 'seodata',
        'title' => 'SEOData.io · SEO API & Web App',
        'summary' => 'SEO platform aggregating multiple external APIs with credit-based subscriptions and full admin control.',
        'description' => "Built on Apiato, a Laravel-based framework for API-first applications. The platform integrates multiple external SEO data providers behind a unified API, with full admin implementation for managing endpoints, pricing, and rate limits.\n\nImplemented a credit-based billing system, payment processing, credential management for third-party APIs, and the complete frontend dashboard. Users can purchase credit packs, run keyword research, track domains, and export reports.",
        'tech_stack' => 'Apiato, Laravel, Vue, Stripe, External APIs',
        'live_url' => 'https://seodata.io',
        'repo_url' => null,
        'featured' => 1,
        'sort_order' => 4,
        'completed_on' => '2023-08',
    ],
    [
        'slug' => 'keyword-tracker',
        'title' => 'KeywordTracker.net · SERP Crawler',
        'summary' => 'Domain ranking tracker with custom crawler infrastructure, subscriptions, and payment processing.',
        'description' => "A keyword ranking tool that crawls search engines and tracks domain positions over time. Built with Filament for the admin interface and a custom crawler service that handles rotation, throttling, and result parsing.\n\nIntegrated subscription management with tiered pricing, payment processing via Stripe, and a clean reporting interface showing rank history, competitor comparisons, and SERP feature tracking (featured snippets, knowledge panels, etc).",
        'tech_stack' => 'Laravel, Filament, External APIs, Stripe, Custom crawlers',
        'live_url' => 'https://keywordtracker.net',
        'repo_url' => null,
        'featured' => 0,
        'sort_order' => 5,
        'completed_on' => '2022-11',
    ],
    [
        'slug' => 'wwqc',
        'title' => 'Worldwide Quality Control',
        'summary' => 'Audit, quality inspection, and laboratory testing booking platform with full-stack implementation.',
        'description' => "A platform for booking and managing quality control services across audits, on-site inspections, and laboratory testing. Implemented both backend and frontend from scratch.\n\nHandles complex booking workflows where inspections span multiple sites and require scheduling inspectors across time zones. The lab module manages sample intake, test assignment, and result reporting with attached PDF certifications.",
        'tech_stack' => 'Laravel, MySQL, jQuery, Bootstrap',
        'live_url' => 'https://worldwidequalitycontrol.com',
        'repo_url' => null,
        'featured' => 0,
        'sort_order' => 6,
        'completed_on' => '2018-09',
    ],
    [
        'slug' => 'nhts',
        'title' => 'NHTS · Household Targeting System',
        'summary' => 'Government app for monitoring household targeting activities at the Department of Social Welfare.',
        'description' => "Built during my time at the Department of Social Welfare and Development Philippines. The app assists field staff in monitoring activities within the household targeting section of the National Household Targeting System, used to identify and verify beneficiaries of government social programs.\n\nThe system tracks visit logs, eligibility assessments, and case management workflows for social workers in the field.",
        'tech_stack' => 'Laravel, PHP, MySQL, jQuery',
        'live_url' => null,
        'repo_url' => null,
        'featured' => 0,
        'sort_order' => 7,
        'completed_on' => '2019-04',
    ],
    [
        'slug' => 'venus-awards',
        'title' => 'Venus Awards · UK Business Women Awards',
        'summary' => 'End-to-end awards-management platform covering nominations, eligibility, applications, sponsorship, and judging.',
        'description' => "A complete platform for an awards organization in the UK recognizing achievements of business women. The system handles the entire awards lifecycle: public nominations, automated eligibility checking based on award criteria, application forms with supporting document uploads, sponsorship management, and a multi-stage judging module.\n\nThe judging module allows panels of judges to score applications across weighted criteria with audit logging of all decisions.",
        'tech_stack' => 'Laravel, PHP, MySQL, jQuery',
        'live_url' => null,
        'repo_url' => null,
        'featured' => 0,
        'sort_order' => 8,
        'completed_on' => '2016-07',
    ],
];
foreach ($projects as $p) {
    Database::insert('projects', $p);
}

Database::insert('_meta', ['key' => 'installed', 'value' => '1']);
