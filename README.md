# 🚀 OmniFAQ AI — Tech Interview Preparation Platform

An intelligent, full-stack tech interview preparation web application combining curated industry-standard interview questions with **Google Gemini Generative AI**.

---

## ✨ Features

- **🎯 Curated Interview Repository:** Pre-loaded with top technical interview questions across JavaScript, React, Node.js, SQL, and System Design.
- **⚡ Adaptive Difficulty Tiers:** Filter questions by Easy, Medium, and Hard.
- **🔍 Instant Debounced Search:** Fast keyword search with relevance scoring across questions, tags, and answers.
- **🤖 Live AI Question Generator:** When a searched technology topic is missing, users can generate fresh interview questions and model answers via **Google Gemini AI**.
- **🔄 Self-Learning Knowledge Base:** AI-generated questions are automatically sanitized and persisted into the MySQL database for future visitors.
- **📊 Admin Analytics & Search Tracking:** Real-time tracking of search volume, query resolution rate, and top unresolved student queries to identify knowledge gaps.
- **🛡️ Secure Authentication:** User and Admin registration & login with Bcrypt password hashing and PDO prepared statements.

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3 (Glassmorphism Dark Theme), jQuery 3.7.1, Lucide Icons
- **Backend:** PHP 8+ (REST API architecture)
- **Database:** MySQL / MariaDB (InnoDB, UTF-8 mb4, Full-Text Search indexing)
- **AI Integration:** Google Gemini Generative API (`gemini-1.5-flash`)
- **Deployment:** Docker & Render / Apache XAMPP

---

## 📁 Project Structure

```text
├── index.html                   # Main Single Page Application (SPA)
├── style.css                    # Glassmorphism design and responsive styles
├── api.php                      # REST API Router & Controller (Auth, FAQs, AI, Analytics)
├── db.php                       # Database connection handler (PDO + Cloud Env support)
├── schema.sql                   # MySQL database schema with seed data
├── Dockerfile                   # Docker container configuration for Render deployment
├── render.yaml                  # Render Infrastructure Blueprint configuration
├── .dockerignore                # Files ignored during Docker build
├── .gitignore                   # Files ignored by Git
├── presentation.html            # Interactive Project Presentation Deck
└── PROJECT_PRESENTATION_BRIEF.md# Complete presentation & viva guide
```

---

## 💻 Local Setup (XAMPP)

1. **Clone or Copy:**
   Place this project directory inside your XAMPP `htdocs` folder (e.g., `/Applications/XAMPP/xamppfiles/htdocs/khushi project` or `C:\xampp\htdocs\khushi project`).

2. **Start Apache & MySQL:**
   Open XAMPP Control Panel and start both **Apache** and **MySQL**.

3. **Import Database:**
   - Go to `http://localhost/phpmyadmin`
   - Create a new database named `ai_faq_db`
   - Import `schema.sql` into `ai_faq_db`

4. **Run the Application:**
   Open your browser and visit:
   `http://localhost/khushi%20project/` (or the folder name you chose).

---

## 🌐 Deploying to Render (Step-by-Step)

### Step 1: Push Code to GitHub
1. Create a new repository on [GitHub](https://github.com/new).
2. Push your project code to GitHub.

### Step 2: Set up a Free Cloud MySQL Database
Render provides PostgreSQL natively, so for MySQL you can use a free cloud database provider:
- **Option A (Recommended): [TiDB Cloud Serverless](https://tidbcloud.com/)** — Free forever 5GB MySQL-compatible database.
- **Option B: [Aiven MySQL](https://aiven.io/)** — Free tier MySQL instance.
- **Option C: [Clever Cloud](https://www.clever-cloud.com/)** — Free MySQL database.

Once created:
1. Connect via your DB client (or Web Console) and run/import `schema.sql`.
2. Note down your connection credentials (`Host`, `Database`, `User`, `Password`, `Port`).

### Step 3: Deploy on Render
1. Go to [Render Dashboard](https://dashboard.render.com/) and click **New +** -> **Web Service**.
2. Connect your GitHub repository.
3. Select **Docker** as the Runtime (Render will automatically pick up `Dockerfile`).
4. Choose the **Free** instance type.
5. In the **Environment Variables** section, add:
   - `DB_HOST`: Your cloud database host
   - `DB_NAME`: Your database name
   - `DB_USER`: Your database username
   - `DB_PASS`: Your database password
   - `DB_PORT`: `3306` (or provider's port)
   - `GEMINI_API_KEY`: Your Google Gemini API Key
   - `GEMINI_MODEL`: `gemini-1.5-flash`
6. Click **Create Web Service**. Render will build the Docker container and provide a live URL (`https://your-app.onrender.com`)!
