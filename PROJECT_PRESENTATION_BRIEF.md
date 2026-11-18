# OmniFAQ AI — Project Master Presentation Brief & AI Prompt

> **Instructions for the AI Presentation Maker (ChatGPT, Claude, Gamma.app, Tome, SlidesAI, etc.):**
> Use this comprehensive brief to generate a professional, visually stunning, slide-by-slide presentation (12 to 15 slides). 
> Maintain a modern, confident, and professional technical tone suitable for engineering juries, viva examinations, and hackathon pitches.
> For each slide, produce:
> 1. **Slide Title & Subtitle**
> 2. **Key Talking Points (3-4 crisp bullet points)**
> 3. **Visual / Layout Recommendation** (Cards, comparison tables, flowcharts, KPI badges)
> 4. **Speaker Script / Viva Explanation** (What to say when presenting the slide)

---

## 📌 1. Project Overview & Elevator Pitch

- **Project Name:** OmniFAQ AI — Tech Interview Preparation Platform
- **Developer / Creator:** Khushi Dhakad
- **Domain:** EdTech / AI-Assisted Learning / Career Prep
- **Core Value Proposition:** An intelligent, full-stack tech interview preparation portal that combines curated industry-standard interview questions with Google Gemini generative AI. It offers real-time debounced search, role-based access control, analytics for tracking knowledge gaps, and an automated self-learning database mechanism.
- **Key Metrics:**
  - **18+** Expert pre-loaded questions across **5** core tech domains.
  - **3** Adaptive difficulty levels (Easy, Medium, Hard).
  - **9** Backend REST API endpoints.
  - **3** Relational database tables with FULLTEXT search indexing.
  - **Dual Deployment:** Local XAMPP (Apache + MariaDB) & Live Production (InfinityFree).

---

## 🎯 2. Problem Statement & Solution

### The Problem
1. **Scattered Resources:** Tech candidates struggle hopping between LeetCode, GitHub repos, blogs, and forums with no single structured repository.
2. **Lack of Comprehensive Model Answers:** Most platforms list interview questions without in-depth, interview-ready answers or code snippets.
3. **Static Content:** Traditional FAQ platforms cannot adapt when a student searches for emerging technologies or niche topics.
4. **No Visibility into Student Needs:** Administrators/mentors have no data on which questions are searched most or where students get stuck without answers.

### The OmniFAQ AI Solution
1. **Unified Hub:** A single portal covering JavaScript, React, Node.js, SQL, and System Design with tiered difficulty.
2. **AI-Powered On-Demand Generation:** When a topic isn't found in the database, users can trigger **Google Gemini AI** to generate 3–4 interview questions with model answers in seconds.
3. **Self-Learning Knowledge Base:** AI-generated questions are automatically sanitized and inserted into MySQL so subsequent users access them instantaneously from the database without repeated API latency or cost.
4. **Search Analytics & Gap Detection:** Automatically logs all queries, calculates the search resolution rate, and surfaces "Unresolved Queries" so administrators know exactly what topics need new content.

---

## 🛠️ 3. Technology Stack & Architecture

### Architectural Pattern: Three-Tier Client-Server Architecture
```
┌────────────────────────────────────────────────────────┐
│                      FRONTEND                          │
│   HTML5 · CSS3 (Glassmorphism) · jQuery 3.7.1          │
│   Lucide Icons · Space Grotesk & Inter Typography      │
└───────────────────────────┬────────────────────────────┘
                            │  HTTP / JSON (REST API)
                            ▼
┌────────────────────────────────────────────────────────┐
│                      BACKEND                           │
│   PHP 8 (Object-Oriented & PDO) · RESTful Routing      │
│   Environment Auto-Detector · Prepared Statements      │
└─────────────┬────────────────────────────┬─────────────┘
              │ PDO SQL                    │ cURL HTTPS
              ▼                            ▼
┌───────────────────────────┐  ┌─────────────────────────┐
│     DATABASE ENGINE       │  │       AI ENGINE         │
│  MySQL / MariaDB 8.2      │  │  Google Gemini API       │
│  InnoDB · UTF8mb4         │  │  (gemini-3.5-flash)     │
│  Full-Text Search Index   │  │  Structured JSON Output │
└───────────────────────────┘  └─────────────────────────┘
```

| Layer | Technology | Purpose & Implementation Details |
|---|---|---|
| **Frontend UI** | HTML5, CSS3, Vanilla JS, jQuery | Single Page Application (SPA) architecture with view routing, responsive cards, toast notifications, and skeleton loaders. |
| **Styling & Theme** | Modern Glassmorphism | Dark Navy (`#0A1628` / `#112F3B`), Cyan accents (`#0891B2` / `#06B6D4`), subtle backdrop filters (`blur(10px)`), and radial ambient glow orbs. |
| **Backend API** | PHP 8 (Procedural/Modular) | Clean single-entry REST router (`api.php`) handling 9 actions, CORS headers, JSON request/response streaming, and session security. |
| **Database Engine** | MySQL / MariaDB (InnoDB) | `ai_faq_db` containing 3 optimized tables with indexation, foreign constraints, and UTF8mb4 full unicode support. |
| **Database Access** | PDO (PHP Data Objects) | Singleton pattern with native prepared statements for 100% defense against SQL Injection attacks. |
| **External AI** | Google Gemini Generative API | Model `gemini-3.5-flash` called via backend cURL with strict JSON schema constraints for automated extraction. |
| **Hosting & Servers** | Apache (XAMPP local) + Cloud | Tested on local macOS XAMPP stack and prepared for live LAMP deployment. |

---

## 🗄️ 4. Database Schema (ai_faq_db)

### 1. `users` Table
Stores authentication credentials and permissions.
- `id` (INT, PK, Auto-Increment)
- `full_name` (VARCHAR 100) — User's display name.
- `email` (VARCHAR 150, UNIQUE) — Used for authentication.
- `password` (VARCHAR 255) — Hashed using PHP `PASSWORD_DEFAULT` (Bcrypt).
- `role` (ENUM: `'user'`, `'admin'`) — Role-Based Access Control flag.
- `created_at` (TIMESTAMP) — Account creation date.

### 2. `faqs` Table (Tech Interview Repository)
Stores interview questions, answers, taxonomy, and metrics.
- `id` (INT, PK, Auto-Increment)
- `question` (VARCHAR 500) — The technical interview question.
- `answer` (TEXT) — Detailed model explanation suitable for technical interviews.
- `category` (VARCHAR 50) — Tech domain (JavaScript, React, Node.js, SQL, System Design).
- `difficulty` (ENUM: `'Easy'`, `'Medium'`, `'Hard'`) — Question tier.
- `tags` (VARCHAR 255) — Comma-separated keywords for search matching.
- `views` (INT, Default 0) — Popularity counter incremented on view/query.
- `created_at` (TIMESTAMP)
- **Indexes:** `INDEX(category)`, `INDEX(difficulty)`, `FULLTEXT(question, answer)`.

### 3. `search_analytics` Table
Telemetry and gap analysis engine.
- `id` (INT, PK, Auto-Increment)
- `query_text` (VARCHAR 255) — Search term entered by user.
- `field_category` (VARCHAR 50) — Category filter applied during search.
- `difficulty` (VARCHAR 20) — Difficulty filter applied.
- `matched_results_count` (INT) — Number of FAQs returned.
- `is_unresolved` (BOOLEAN: 0 or 1) — Flagged as 1 if 0 matches were found.
- `searched_at` (TIMESTAMP) — Query timestamp.

---

## ⚡ 5. Backend REST API Actions (`api.php`)

| # | Action Name | HTTP Method | Parameters | Description |
|---|---|---|---|---|
| 1 | `register` | POST | `full_name`, `email`, `password` | Validates input, hashes password via bcrypt, inserts user, returns user object. |
| 2 | `login` | POST | `email`, `password` | Authenticates against database using `password_verify()`, returns role & user session data. |
| 3 | `get_faqs` | GET/POST | `category`, `difficulty`, `search`, `id` | Filters questions dynamically, supports text matching, and increments view counters. |
| 4 | `ai_query` | POST | `query`, `field` | Algorithmic scoring search: evaluates keywords across question (4pts), tags (3pts), and answer (1pt). |
| 5 | `generate_ai_questions` | POST | `topic` | Prompts Gemini API for 3-4 structured interview questions, parses JSON, and saves directly to MySQL. |
| 6 | `ai_interactive_faq` | POST | `field`, `difficulty` | Retrieves randomized sample questions for interactive domain drills. |
| 7 | `admin_save_faq` | POST | `id` (optional), `question`, `answer`, `category`, `difficulty`, `tags` | Handles both CREATE (new) and UPDATE (existing) FAQ records. |
| 8 | `admin_delete_faq` | POST | `id` | Deletes an FAQ item from the database. |
| 9 | `admin_get_analytics` | GET/POST | — | Calculates KPIs (total FAQs, views, searches, resolution rate), returns top 10 unresolved queries and last 50 query logs. |

---

## 🔒 6. Key Security & Architectural Highlights

1. **Zero SQL Injection Risk:** 100% of database interactions run through PDO prepared statements with parameter binding.
2. **Cryptographic Password Security:** No plain-text passwords stored; uses bcrypt with automated cost factors (`password_hash`).
3. **Two-Tier Role-Based Access Control (RBAC):** Admin endpoints verify user privileges before allowing CRUD updates; admin controls are hidden from regular users on frontend and verified on backend.
4. **Environment Portability:** `db.php` inspects `$_SERVER['HTTP_HOST']` and automatically switches between local credentials (`127.0.0.1`, `root`, `ai_faq_db`) and production live server credentials.
5. **Debounced Network Efficiency:** Search bar waits 400ms after user keystrokes before dispatching network requests, preventing request spam.

---

## 🎬 7. Ready-to-Use 15-Slide Presentation Blueprint

*Feed the following slide-by-slide structure directly into your presentation tool:*

```markdown
### SLIDE 1: Title Slide (The Hook)
- Title: OmniFAQ AI
- Subtitle: Next-Generation Tech Interview Preparation Platform
- Presenter: Khushi Dhakad
- Visual: Modern tech badge, glowing cyan accents, high-contrast dark theme.
- Key Badges: Powered by Google Gemini AI · Built with PHP 8, MySQL & XAMPP
- Speaker Note: "Good morning respected evaluators. Today I present OmniFAQ AI, an intelligent, full-stack tech interview preparation platform designed to bridge the gap between static study material and dynamic, real-world interview practice."

### SLIDE 2: The Problem (Why Tech Candidates Struggle)
- Title: The Tech Interview Dilemma
- Problem 1: Fragmented Resources — Candidates waste hours hopping between disconnected forums and blogs.
- Problem 2: Missing Model Answers — Questions are common, but high-scoring, structured technical explanations are rare.
- Problem 3: Static & Outdated Material — Platforms don't adapt to new frameworks or niche domains.
- Problem 4: No Knowledge Gap Analytics — Learners don't know what they don't know.
- Visual: 4 glassmorphic warning cards with problem icons.
- Speaker Note: "Over 70% of tech students report feeling overwhelmed when preparing for technical rounds because study materials are scattered, lack in-depth model answers, and offer zero feedback on unresolved topics."

### SLIDE 3: The Solution (Introducing OmniFAQ AI)
- Title: The OmniFAQ AI Solution
- Core Concept: An integrated ecosystem combining structured interview repositories with live AI generation.
- Pillar 1: Pre-loaded Question Bank — 18+ comprehensive questions in JS, React, Node, SQL & System Design.
- Pillar 2: Live AI Question Generator — Instant generation of interview questions on any technology.
- Pillar 3: Self-Expanding Knowledge Base — AI responses become permanent database assets.
- Pillar 4: Real-Time Telemetry — Analytics dashboard tracking search patterns and content gaps.
- Visual: Split layout: Problem vs Solution visual comparison.
- Speaker Note: "OmniFAQ AI solves this by combining the reliability of a curated database with the flexibility of Google Gemini AI, turning missed searches into permanent learning material."

### SLIDE 4: System Architecture & Data Flow
- Title: System Architecture & Workflow
- Client Tier: HTML5, CSS3, jQuery Single Page Application.
- Server Tier: Modular PHP 8 REST Router handling 9 specialized actions.
- Persistence Tier: MySQL MariaDB with InnoDB engine and FULLTEXT indexation.
- AI Cloud Service: Google Gemini 3.5 Flash API via secure backend cURL.
- Visual: Clean 3-tier architectural diagram showing bidirectional data flow.
- Speaker Note: "Our architecture follows a clean 3-tier model. The frontend communicates with our PHP backend through RESTful endpoints. The backend securely connects to MySQL via PDO and interfaces with Google Gemini for real-time generative capabilities."

### SLIDE 5: Technology Stack Deep Dive
- Title: Technology Stack
- Frontend: HTML5, CSS3, jQuery 3.7.1, Lucide Icons, Space Grotesk & Inter typography.
- Backend: PHP 8 (REST API, PDO Database Singleton, cURL).
- Database: MySQL / MariaDB (InnoDB, UTF8mb4, Fulltext Indexing).
- Development Environment: XAMPP Local Server + Apache Port 80 + MariaDB Port 3306.
- AI Engine: Google Gemini API (Structured JSON generation).
- Visual: Grid of 5 tech cards with respective logos and version badges.
- Speaker Note: "We intentionally chose a robust, lightweight, and dependency-free stack: modern vanilla styling with jQuery for rapid DOM manipulation, backed by a scalable PHP 8 REST API and MariaDB."

### SLIDE 6: Database Architecture & Data Modeling
- Title: Database Architecture (3 Core Tables)
- Table 1: `users` — Stores credentials with bcrypt hashing and role-based permissions (user/admin).
- Table 2: `faqs` — Stores questions, comprehensive answers, categories, tags, and view counters.
- Table 3: `search_analytics` — Captures search terms, timestamps, result counts, and resolution flags.
- Visual: Entity Relationship (ER) diagram showing tables, primary keys, and index fields.
- Speaker Note: "The relational database is intentionally normalized into three tables: users for authentication, faqs for content, and search_analytics for capturing user search behavior and unresolved queries."

### SLIDE 7: Feature 1 & 2 — Authentication & Role-Based Access
- Title: Secure Authentication & Access Control
- Feature: User Registration & Login with client-side & server-side validation.
- Cryptography: Passwords hashed using bcrypt via PHP `password_hash()`.
- Access Control: Multi-level roles (`user` vs `admin`).
- Session Management: LocalStorage token persistence with automatic state synchronization.
- Visual: Step-by-step registration and login verification flow diagram.
- Speaker Note: "Security was a top priority. We implemented bcrypt password hashing and dual-layer role validation so standard users only access study tools, while admin features remain protected."

### SLIDE 8: Feature 3 — Smart Search & Intelligent Filtering
- Title: Real-Time Debounced Search
- Debounced Input: 400ms delay to prevent server overload during rapid typing.
- Suggestion Pills: One-click topic filters for JavaScript, React, Node.js, SQL, and System Design.
- Algorithmic Scoring: Weighted relevance ranking: Question matches (4 pts) > Tags (3 pts) > Answer content (1 pt).
- Visual State: Interactive card views with difficulty tags and collapsible answers.
- Visual: Search bar screenshot/mockup with active suggestion tags.
- Speaker Note: "Our search engine features 400-millisecond debouncing to minimize server overhead. It applies a weighted scoring algorithm to surface the most relevant questions first."

### SLIDE 9: Feature 4 (Star Feature) — Google Gemini AI Question Generator
- Title: AI-Powered On-Demand Question Generation
- The Workflow:
  1. User searches for a new topic (e.g., 'Docker' or 'Kubernetes').
  2. If 0 results are found, the UI offers an 'AI Generate' action.
  3. Backend calls Google Gemini API with a strict JSON system prompt.
  4. Gemini generates 3–4 interview questions with model answers.
  5. The questions are automatically persisted into MySQL and rendered on screen.
- Visual: Linear workflow showing User -> Search -> Missing -> Gemini API -> Database -> Instant View.
- Speaker Note: "This is our standout feature: when a candidate searches for a topic not yet in our database, Google Gemini generates tailored interview questions on the fly, which are then saved permanently in MySQL for future users."

### SLIDE 10: Feature 5 — Admin Control Center (CRUD Operations)
- Title: Comprehensive Admin Panel
- Tab 1 (All FAQs): Filter, sort, and inspect every question in the database.
- Tab 2 (Create & Edit): Full CRUD interface to add new questions or refine existing model answers.
- Tab 3 (Analytics Overview): Real-time metrics on user engagement and search trends.
- Tab 4 (Knowledge Gap Identification): Surfaces queries where students found zero results.
- Visual: 4-quadrant layout showcasing each admin tab's functionality.
- Speaker Note: "The admin dashboard empowers instructors and administrators to monitor platform health, update content via full CRUD operations, and directly address learning gaps."

### SLIDE 11: Feature 6 — Search Telemetry & Gap Analysis
- Title: Data-Driven Knowledge Gap Detection
- Metrics Tracked: Total FAQs, cumulative views, total search volume, and resolution percentage.
- The 'Unresolved Query' Engine: Identifies frequently searched terms that returned zero results.
- Business Value: Instructors know exactly which topics candidates are studying and where to expand the syllabus.
- Visual: Analytics KPI counter cards + list of top 3 unresolved searches.
- Speaker Note: "Every query is logged in our analytics table. If a query yields zero results, it is flagged as 'unresolved', allowing curriculum designers to see exactly what students need next."

### SLIDE 12: Content Showcase — 18+ Curated Questions
- Title: Pre-Loaded Technical Content
- JavaScript: Closures, Event Loop, var/let/const, Prototypal Inheritance.
- React: Virtual DOM, useState vs useReducer, Reconciliation, Server Components.
- Node.js: Event-driven architecture, Express middleware, REST vs GraphQL.
- SQL: INNER vs LEFT JOIN, ACID properties, Indexes (Clustered vs Non-Clustered), Normalization.
- System Design: URL Shortener design, Rate limiting algorithms, Load balancing & caching.
- Visual: Structured curriculum table showing topics, difficulty levels, and sample question count.
- Speaker Note: "We launched with 18 comprehensive, pre-loaded questions spanning five core domains, each written with detailed, interview-ready answers and code examples."

### SLIDE 13: Robust Security Architecture
- Title: Enterprise-Grade Security Principles
- Injection Prevention: 100% PDO prepared statements with strict parameter binding.
- Password Safety: One-way bcrypt hashing; plain-text passwords never touch logs or database.
- Input Sanitization: Strict email regex validation, type casting, and HTML entity encoding.
- Cross-Origin Control: CORS headers configured for controlled resource sharing.
- Dual-Environment Config: Automatic credential switching between Localhost and Live servers.
- Visual: Shield graphic surrounded by 5 security highlight badges.
- Speaker Note: "Security is built-in at every level: prepared statements prevent SQL injection, bcrypt secures credentials, and strict server-side validation sanitizes all inputs."

### SLIDE 14: UI/UX & Design Philosophy
- Title: Premium Dark Glassmorphism Design
- Aesthetic: Deep Navy backgrounds (`#0A1628`) with luminous Cyan and Teal accents.
- Sensory Polish: Frosted glass cards (`backdrop-filter: blur`), subtle borders, and ambient glow orbs.
- Micro-Interactions: Skeleton loading placeholders, toast pop-ups, and smooth card hover lifts.
- Typography: High-impact Space Grotesk headers paired with legible Inter body typography.
- Visual: Color palette swatches + UI card component showcase.
- Speaker Note: "We avoided generic templates in favor of a modern, dark glassmorphism aesthetic with subtle glow orbs and micro-animations, delivering an engaging, professional experience."

### SLIDE 15: Conclusion, Impact & Future Roadmap
- Title: Project Summary & Future Vision
- Accomplishments:
  - Fully functional full-stack platform running on XAMPP.
  - Seamless Google Gemini AI integration with automated database persistence.
  - End-to-end CRUD, search telemetry, and role-based access.
- Future Enhancements:
  - AI Voice Interviewer (audio question-and-answer simulation).
  - Code sandbox integration for running live JavaScript/SQL queries in-browser.
  - User progress tracking and spaced-repetition bookmarks.
- Speaker Note: "In summary, OmniFAQ AI turns passive FAQ reading into an active, AI-assisted learning platform. In the future, we plan to introduce voice-based mock interviews and live in-browser code evaluation. Thank you!"
```

---

## 💡 8. Quick Copy-Paste Prompt for Other AI Tools

If you want to paste this into another AI (like ChatGPT or Claude) to get slides or PowerPoint code, use this exact prompt:

```text
I am creating a 15-slide PowerPoint presentation for my college project viva / tech presentation called "OmniFAQ AI".
Here is the complete project documentation, technology stack, database architecture, and slide-by-slide blueprint:

[PASTE THE CONTENTS OF THIS FILE HERE]

Please generate:
1. A slide-by-slide presentation deck with compelling headlines, bullet points, and visual layout suggestions.
2. Word-for-word speaker notes for each slide so I know exactly what to say during my viva/presentation.
3. Keep the tone technical, professional, and confident.
```
